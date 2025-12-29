<?php
require_once '../includes/conexion.php';
require_once '../includes/sesion.php';

requerirAutenticacion();

$titulo_pagina = 'Agregar Producto';
$errores = [];
$exito = false;

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
    
    // Procesar imagen
    $imagen_nombre = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $directorio_destino = '../img/productos/';
        
        // Crear directorio si no existe
        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0777, true);
        }
        
        $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($extension, $extensiones_permitidas)) {
            $imagen_nombre = uniqid() . '.' . $extension;
            $ruta_destino = $directorio_destino . $imagen_nombre;
            
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
                $errores[] = "Error al subir la imagen";
            }
        } else {
            $errores[] = "Formato de imagen no permitido";
        }
    }
    
    // Insertar si no hay errores
    if (empty($errores)) {
        $query = "INSERT INTO productos (nombre, descripcion, tipo, categoria, precio, disponibilidad, unidad_medida, imagen, activo) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssddssi", $nombre, $descripcion, $tipo, $categoria, $precio, $disponibilidad, $unidad_medida, $imagen_nombre, $activo);
        
        if ($stmt->execute()) {
            redirigirConMensaje('productos_crud.php', 'Producto agregado exitosamente', 'success');
        } else {
            $errores[] = "Error al agregar el producto: " . $conn->error;
        }
    }
}

include '../includes/header.php';
?>

<div class="content-card">
    <h1 class="page-title">
        <i class="fas fa-plus-circle"></i> Agregar Nuevo Producto
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
                           value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" required>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="categoria" class="form-label">Categoría *</label>
                    <select class="form-control" id="categoria" name="categoria" required>
                        <option value="">Seleccione una categoría</option>
                        <option value="tuna_verde">Tuna Verde</option>
                        <option value="tuna_roja">Tuna Roja</option>
                        <option value="tuna_amarilla">Tuna Amarilla</option>
                        <option value="tuna_bonda">Tuna Bonda</option>
                        <option value="xoconostle">Xoconostle</option>
                        <option value="mermelada">Mermelada</option>
                        <option value="salsa">Salsa</option>
                        <option value="dulce">Dulce</option>
                        <option value="deshidratado">Deshidratado</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : ''; ?></textarea>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo de Producto *</label>
                    <select class="form-control" id="tipo" name="tipo" required>
                        <option value="">Seleccione tipo</option>
                        <option value="fruta_fresca">Fruta Fresca</option>
                        <option value="procesado">Procesado</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="precio" class="form-label">Precio ($) *</label>
                    <input type="number" step="0.01" class="form-control" id="precio" name="precio" 
                           value="<?php echo isset($_POST['precio']) ? $_POST['precio'] : ''; ?>" required>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="disponibilidad" class="form-label">Disponibilidad *</label>
                    <input type="number" step="0.01" class="form-control" id="disponibilidad" name="disponibilidad" 
                           value="<?php echo isset($_POST['disponibilidad']) ? $_POST['disponibilidad'] : '0'; ?>" required>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="unidad_medida" class="form-label">Unidad de Medida *</label>
                    <select class="form-control" id="unidad_medida" name="unidad_medida" required>
                        <option value="kg">Kilogramos (kg)</option>
                        <option value="piezas">Piezas</option>
                        <option value="unidad">Unidad</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="imagen" class="form-label">Imagen del Producto</label>
                    <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                    <small class="text-muted">Formatos permitidos: JPG, PNG, GIF, WEBP</small>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="activo" name="activo" checked>
                <label class="form-check-label" for="activo">
                    Producto activo
                </label>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Guardar Producto
            </button>
            <a href="productos_crud.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>