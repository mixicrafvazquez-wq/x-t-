<?php
require_once '../includes/conexion.php';
require_once '../includes/sesion.php';

requerirAutenticacion();

$titulo_pagina = 'Editar Productor';
$errores = [];
$productor = null;

// Verificar que se recibió un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirigirConMensaje('productores_crud.php', 'ID de productor inválido', 'error');
}

$productor_id = intval($_GET['id']);

// Obtener datos del productor
$query = "SELECT * FROM productores WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $productor_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    redirigirConMensaje('productores_crud.php', 'Productor no encontrado', 'error');
}

$productor = $resultado->fetch_assoc();

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = limpiarDatos($_POST['nombre']);
    $apellidos = limpiarDatos($_POST['apellidos']);
    $localidad = limpiarDatos($_POST['localidad']);
    $telefono = limpiarDatos($_POST['telefono']);
    $email = limpiarDatos($_POST['email']);
    $tipo_producto = limpiarDatos($_POST['tipo_producto']);
    $produccion_promedio = floatval($_POST['produccion_promedio_mensual']);
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    // Validaciones
    if (empty($nombre)) $errores[] = "El nombre es requerido";
    if (empty($apellidos)) $errores[] = "Los apellidos son requeridos";
    if (empty($localidad)) $errores[] = "La localidad es requerida";
    if (empty($tipo_producto)) $errores[] = "El tipo de producto es requerido";
    if ($produccion_promedio <= 0) $errores[] = "La producción promedio debe ser mayor a 0";
    
    // Validar email si se proporcionó
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del email no es válido";
    }
    
    // Validar teléfono si se proporcionó
    if (!empty($telefono) && !preg_match('/^[0-9]{10}$/', $telefono)) {
        $errores[] = "El teléfono debe tener 10 dígitos";
    }
    
    // Actualizar si no hay errores
    if (empty($errores)) {
        $query = "UPDATE productores SET 
                  nombre = ?, 
                  apellidos = ?, 
                  localidad = ?, 
                  telefono = ?, 
                  email = ?, 
                  tipo_producto = ?, 
                  produccion_promedio_mensual = ?, 
                  activo = ?
                  WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssdii", $nombre, $apellidos, $localidad, $telefono, $email, $tipo_producto, $produccion_promedio, $activo, $productor_id);
        
        if ($stmt->execute()) {
            redirigirConMensaje('productores_crud.php', 'Productor actualizado exitosamente', 'success');
        } else {
            $errores[] = "Error al actualizar el productor: " . $conn->error;
        }
    }
}

include '../includes/header.php';
?>

<div class="content-card">
    <h1 class="page-title">
        <i class="fas fa-user-edit"></i> Editar Productor
    </h1>
    
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
                    <label for="nombre" class="form-label">Nombre(s) *</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" 
                           value="<?php echo htmlspecialchars($productor['nombre']); ?>" required>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="apellidos" class="form-label">Apellidos *</label>
                    <input type="text" class="form-control" id="apellidos" name="apellidos" 
                           value="<?php echo htmlspecialchars($productor['apellidos']); ?>" required>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="localidad" class="form-label">Localidad *</label>
                    <input type="text" class="form-control" id="localidad" name="localidad" 
                           value="<?php echo htmlspecialchars($productor['localidad']); ?>" required>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                           value="<?php echo htmlspecialchars($productor['telefono']); ?>" 
                           maxlength="10">
                    <small class="text-muted">10 dígitos sin espacios</small>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo htmlspecialchars($productor['email']); ?>">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="tipo_producto" class="form-label">Tipo de Producto que Aporta *</label>
                    <input type="text" class="form-control" id="tipo_producto" name="tipo_producto" 
                           value="<?php echo htmlspecialchars($productor['tipo_producto']); ?>" required>
                    <small class="text-muted">Puede especificar varios tipos separados por comas</small>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="produccion_promedio_mensual" class="form-label">Producción Promedio Mensual (kg) *</label>
                    <input type="number" step="0.01" class="form-control" id="produccion_promedio_mensual" 
                           name="produccion_promedio_mensual" 
                           value="<?php echo $productor['produccion_promedio_mensual']; ?>" required>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="activo" name="activo" 
                       <?php echo $productor['activo'] == 1 ? 'checked' : ''; ?>>
                <label class="form-check-label" for="activo">
                    Productor activo
                </label>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Actualizar Productor
            </button>
            <a href="productores_crud.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>