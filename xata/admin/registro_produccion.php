<?php
require_once '../includes/conexion.php';
require_once '../includes/sesion.php';

requerirAutenticacion();

$titulo_pagina = 'Registrar Producción';
$errores = [];

// Obtener productores activos
$query_productores = "SELECT id, nombre, apellidos FROM productores WHERE activo = 1 ORDER BY nombre ASC";
$productores = $conn->query($query_productores);

// Obtener productos activos (solo frutas frescas para entradas de producción)
$query_productos = "SELECT id, nombre, categoria FROM productos WHERE activo = 1 AND tipo = 'fruta_fresca' ORDER BY nombre ASC";
$productos = $conn->query($query_productos);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productor_id = intval($_POST['productor_id']);
    $producto_id = intval($_POST['producto_id']);
    $cantidad = floatval($_POST['cantidad']);
    $fecha_entrega = limpiarDatos($_POST['fecha_entrega']);
    $notas = limpiarDatos($_POST['notas']);
    
    // Validaciones
    if ($productor_id <= 0) $errores[] = "Debe seleccionar un productor";
    if ($producto_id <= 0) $errores[] = "Debe seleccionar un producto";
    if ($cantidad <= 0) $errores[] = "La cantidad debe ser mayor a 0";
    if (empty($fecha_entrega)) $errores[] = "La fecha de entrega es requerida";
    
    // Validar que la fecha no sea futura
    if (!empty($fecha_entrega) && strtotime($fecha_entrega) > time()) {
        $errores[] = "La fecha de entrega no puede ser futura";
    }
    
    // Insertar si no hay errores
    if (empty($errores)) {
        // Iniciar transacción
        $conn->begin_transaction();
        
        try {
            // Insertar la entrada de producción
            $query = "INSERT INTO entradas_produccion (productor_id, producto_id, cantidad, fecha_entrega, notas) 
                      VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iidss", $productor_id, $producto_id, $cantidad, $fecha_entrega, $notas);
            $stmt->execute();
            
            // Actualizar el inventario del producto (sumar la cantidad recibida)
            $query_update = "UPDATE productos SET disponibilidad = disponibilidad + ? WHERE id = ?";
            $stmt_update = $conn->prepare($query_update);
            $stmt_update->bind_param("di", $cantidad, $producto_id);
            $stmt_update->execute();
            
            // Confirmar transacción
            $conn->commit();
            
            redirigirConMensaje('historial_produccion.php', 'Entrada de producción registrada exitosamente. El inventario se ha actualizado.', 'success');
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $conn->rollback();
            $errores[] = "Error al registrar la entrada: " . $e->getMessage();
        }
    }
}

include '../includes/header.php';
?>

<div class="content-card">
    <h1 class="page-title">
        <i class="fas fa-clipboard-list"></i> Registrar Entrada de Producción
    </h1>
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Nota:</strong> Al registrar una entrada de producción, el inventario del producto se actualizará automáticamente.
    </div>
    
    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <strong>Errores encontrados:</strong>
            <ul class="mb-0">
                <?php foreach ($errores as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="productor_id" class="form-label">Productor *</label>
                    <select class="form-control" id="productor_id" name="productor_id" required>
                        <option value="">Seleccione un productor</option>
                        <?php while ($productor = $productores->fetch_assoc()): ?>
                            <option value="<?php echo $productor['id']; ?>"
                                    <?php echo (isset($_POST['productor_id']) && $_POST['productor_id'] == $productor['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($productor['nombre'] . ' ' . $productor['apellidos']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="producto_id" class="form-label">Producto (Fruta Fresca) *</label>
                    <select class="form-control" id="producto_id" name="producto_id" required>
                        <option value="">Seleccione un producto</option>
                        <?php while ($producto = $productos->fetch_assoc()): ?>
                            <option value="<?php echo $producto['id']; ?>"
                                    <?php echo (isset($_POST['producto_id']) && $_POST['producto_id'] == $producto['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($producto['nombre']); ?>
                                (<?php echo ucwords(str_replace('_', ' ', $producto['categoria'])); ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <small class="text-muted">Solo se muestran productos de fruta fresca activos</small>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="cantidad" class="form-label">Cantidad (kg) *</label>
                    <input type="number" step="0.01" class="form-control" id="cantidad" name="cantidad" 
                           value="<?php echo isset($_POST['cantidad']) ? $_POST['cantidad'] : ''; ?>" 
                           placeholder="0.00"
                           required>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="fecha_entrega" class="form-label">Fecha de Entrega *</label>
                    <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" 
                           value="<?php echo isset($_POST['fecha_entrega']) ? $_POST['fecha_entrega'] : date('Y-m-d'); ?>" 
                           max="<?php echo date('Y-m-d'); ?>"
                           required>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="notas" class="form-label">Notas u Observaciones</label>
            <textarea class="form-control" id="notas" name="notas" rows="3" 
                      placeholder="Ej: Calidad premium, producto orgánico, etc."><?php echo isset($_POST['notas']) ? htmlspecialchars($_POST['notas']) : ''; ?></textarea>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Registrar Entrada
            </button>
            <a href="historial_produccion.php" class="btn btn-secondary">
                <i class="fas fa-list"></i> Ver Historial
            </a>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<div class="content-card mt-4">
    <h5><i class="fas fa-chart-bar"></i> Resumen Rápido</h5>
    <div class="row text-center">
        <?php
        // Obtener estadísticas rápidas
        $stats_hoy = $conn->query("SELECT COUNT(*) as total, COALESCE(SUM(cantidad), 0) as kg 
                                   FROM entradas_produccion 
                                   WHERE DATE(fecha_entrega) = CURDATE()")->fetch_assoc();
        
        $stats_semana = $conn->query("SELECT COUNT(*) as total, COALESCE(SUM(cantidad), 0) as kg 
                                      FROM entradas_produccion 
                                      WHERE YEARWEEK(fecha_entrega) = YEARWEEK(CURDATE())")->fetch_assoc();
        ?>
        <div class="col-md-6">
            <div class="p-3 bg-light rounded">
                <h6>Entradas de Hoy</h6>
                <h3 class="text-primary mb-0"><?php echo $stats_hoy['total']; ?></h3>
                <small class="text-muted"><?php echo number_format($stats_hoy['kg'], 2); ?> kg totales</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-3 bg-light rounded">
                <h6>Entradas Esta Semana</h6>
                <h3 class="text-success mb-0"><?php echo $stats_semana['total']; ?></h3>
                <small class="text-muted"><?php echo number_format($stats_semana['kg'], 2); ?> kg totales</small>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>