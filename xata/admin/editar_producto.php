<?php
require_once '../includes/conexion.php';
require_once '../includes/sesion.php';

requerirAutenticacion();

$titulo_pagina = 'Editar Producto';
$errores = [];
$producto = null;

// Verificar que se recibió un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirigirConMensaje('productos_crud.php', 'ID de producto inválido', 'error');
}

$producto_id = intval($_GET['id']);

// Obtener datos del producto
$query = "SELECT * FROM productos WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $producto_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    redirigirConMensaje('productos_crud.php', 'Producto no encontrado', 'error');
}

$producto = $resultado->fetch_assoc();

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = limpiarDatos($_POST['nombre']);
    $descripcion = limpiarDatos($_POST['descripcion']);
    $tipo = limpiarDatos($_POST['tipo']);
    $categoria = limpiarDatos($_POST['categoria']);
    $precio = floatval($_POST['precio']);
    $disponibilidad = floatval($_POST['disponibilidad']);
    $unidad_medida = limpiarDatos($_POST['unidad_medida']);
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    // Validaciones
    if (empty($nombre)) $errores[] = "El nombre es requerido";
    if (empty($tipo)) $errores[] = "El tipo es requerido";
    if (empty($categoria)) $errores[] = "La categoría es requerida";
    if ($precio <= 0) $errores[] = "El precio debe ser mayor a 0";
    if ($disponibilidad < 0) $errores[] = "La disponibilidad no puede ser negativa";
    
    // Procesar imagen si se subió una nueva
    $imagen_nombre = $producto['imagen'];
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $directorio_destino = '../img/productos/';
        
        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0777, true);
        }
        
        $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($extension, $extensiones_permitidas)) {
            // Eliminar imagen anterior si existe
            if (!empty($producto['imagen']) && file_exists($directorio_destino . $producto['imagen'])) {
                unlink($directorio_destino . $producto['imagen']);
            }
            
            $imagen_nombre = uniqid() . '.' . $extension;
            $ruta_destino = $directorio_destino . $imagen_nombre;
            
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
                $errores[] = "Error al subir la imagen";
            }
        } else {
            $errores[] = "Formato de imagen no permitido";
        }
    }
    
    // Actualizar si no hay errores
    if (empty($errores)) {
        $query = "UPDATE productos SET 
                  nombre = ?, 
                  descripcion = ?, 
                  tipo = ?, 
                  categoria = ?, 
                  precio = ?, 
                  disponibilidad = ?, 
                  unidad_medida = ?, 
                  imagen = ?, 
                  activo = ?
                  WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssddssii", $nombre, $descripcion, $tipo, $categoria, $precio, $disponibilidad, $unidad_medida, $imagen_nombre, $activo, $producto_id);
        
        if ($stmt->execute()) {
            redirigirConMensaje('productos_crud.php', 'Producto actualizado exitosamente', 'success');
        } else {
            $errores[] = "Error al actualizar el producto: " . $conn->error;
        }
    }
}

include '../includes/header.php';
?>

<div class="content-card">
    <h1 class="page-title">
        <i class="fas fa-edit"></i> Editar Producto
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
    
    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Producto *</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" 
                           value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="categoria" class="form-label">Categoría *</label>
                    <select class="form-control" id="categoria" name="categoria" required>
                        <option value="">Seleccione una categoría</option>
                        <option value="tuna_verde" <?php echo $producto['categoria'] === 'tuna_verde' ? 'selected' : ''; ?>>Tuna Verde</option>
                        <option value="tuna_roja" <?php echo $producto['categoria'] === 'tuna_roja' ? 'selected' : ''; ?>>Tuna Roja</option>
                        <option value="tuna_amarilla" <?php echo $producto['categoria'] === 'tuna_amarilla' ? 'selected' : ''; ?>>Tuna Amarilla</option>
                        <option value="tuna_bonda" <?php echo $producto['categoria'] === 'tuna_blanca' ? 'selected' : ''; ?>>Tuna Blanca</option>
                        <option value="xoconostle" <?php echo $producto['categoria'] === 'xoconostle' ? 'selected' : ''; ?>>Xoconostle</option>
                        <option value="mermelada" <?php echo $producto['categoria'] === 'mermelada' ? 'selected' : ''; ?>>Mermelada</option>
                        <option value="salsa" <?php echo $producto['categoria'] === 'salsa' ? 'selected' : ''; ?>>Salsa</option>
                        <option value="dulce" <?php echo $producto['categoria'] === 'queso' ? 'selected' : ''; ?>>Queso</option>
                        <option value="deshidratado" <?php echo $producto['categoria'] === 'deshidratado' ? 'selected' : ''; ?>>Deshidratado</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo de Producto *</label>
                    <select class="form-control" id="tipo" name="tipo" required>
                        <option value="">Seleccione tipo</option>
                        <option value="fruta_fresca" <?php echo $producto['tipo'] === 'fruta_fresca' ? 'selected' : ''; ?>>Fruta Fresca</option>
                        <option value="procesado" <?php echo $producto['tipo'] === 'procesado' ? 'selected' : ''; ?>>Procesado</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="precio" class="form-label">Precio ($) *</label>
                    <input type="number" step="0.01" class="form-control" id="precio" name="precio" 
                           value="<?php echo $producto['precio']; ?>" required>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="disponibilidad" class="form-label">Disponibilidad *</label>
                    <input type="number" step="0.01" class="form-control" id="disponibilidad" name="disponibilidad" 
                           value="<?php echo $producto['disponibilidad']; ?>" required>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="unidad_medida" class="form-label">Unidad de Medida *</label>
                    <select class="form-control" id="unidad_medida" name="unidad_medida" required>
                        <option value="kg" <?php echo $producto['unidad_medida'] === 'kg' ? 'selected' : ''; ?>>Kilogramos (kg)</option>
                        <option value="piezas" <?php echo $producto['unidad_medida'] === 'piezas' ? 'selected' : ''; ?>>Piezas</option>
                        <option value="unidad" <?php echo $producto['unidad_medida'] === 'unidad' ? 'selected' : ''; ?>>Unidad</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="imagen" class="form-label">Imagen del Producto</label>
                    <?php if (!empty($producto['imagen'])): ?>
                        <div class="mb-2">
                            <img src="../img/productos/<?php echo htmlspecialchars($producto['imagen']); ?>" 
                                 alt="Imagen actual" 
                                 style="max-width: 150px; border-radius: 5px;">
                            <p class="text-muted small mt-1">Imagen actual - Suba una nueva para reemplazarla</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                    <small class="text-muted">Formatos permitidos: JPG, PNG, GIF, WEBP</small>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="activo" name="activo" 
                       <?php echo $producto['activo'] == 1 ? 'checked' : ''; ?>>
                <label class="form-check-label" for="activo">
                    Producto activo
                </label>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Actualizar Producto
            </button>
            <a href="productos_crud.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>