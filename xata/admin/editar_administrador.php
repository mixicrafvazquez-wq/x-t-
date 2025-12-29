<?php
require_once '../includes/conexion.php';
require_once '../includes/sesion.php';

requerirAutenticacion();

// Verificar que sea super admin
$query_rol = "SELECT rol FROM usuarios_admin WHERE id = ?";
$stmt = $conn->prepare($query_rol);
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0 || $result->fetch_assoc()['rol'] !== 'super_admin') {
    redirigirConMensaje('dashboard.php', 'No tienes permisos para acceder a esta sección', 'error');
}

$titulo_pagina = 'Editar Administrador';
$errores = [];
$administrador = null;

// Verificar que se recibió un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirigirConMensaje('administradores_crud.php', 'ID de administrador inválido', 'error');
}

$admin_id = intval($_GET['id']);

// Obtener datos del administrador
$query = "SELECT * FROM usuarios_admin WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    redirigirConMensaje('administradores_crud.php', 'Administrador no encontrado', 'error');
}

$administrador = $resultado->fetch_assoc();

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = limpiarDatos($_POST['usuario']);
    $nombre_completo = limpiarDatos($_POST['nombre_completo']);
    $email = limpiarDatos($_POST['email']);
    $rol = limpiarDatos($_POST['rol']);
    $activo = isset($_POST['activo']) ? 1 : 0;
    $cambiar_password = !empty($_POST['password']);
    
    // Validaciones
    if (empty($usuario)) $errores[] = "El nombre de usuario es requerido";
    if (strlen($usuario) < 4) $errores[] = "El usuario debe tener al menos 4 caracteres";
    if (empty($nombre_completo)) $errores[] = "El nombre completo es requerido";
    if (empty($rol)) $errores[] = "El rol es requerido";
    
    if ($cambiar_password) {
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];
        
        if (strlen($password) < 4) $errores[] = "La contraseña debe tener al menos 4 caracteres";
        if ($password !== $password_confirm) $errores[] = "Las contraseñas no coinciden";
    }
    
    // Validar email si se proporcionó
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del email no es válido";
    }
    
    // Verificar que el usuario no esté en uso por otro admin
    if (empty($errores)) {
        $query_check = "SELECT id FROM usuarios_admin WHERE usuario = ? AND id != ?";
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->bind_param("si", $usuario, $admin_id);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows > 0) {
            $errores[] = "El nombre de usuario ya está en uso por otro administrador";
        }
    }
    
    // Actualizar si no hay errores
    if (empty($errores)) {
        if ($cambiar_password) {
            $password_hash = md5($password);
            $query = "UPDATE usuarios_admin SET 
                      usuario = ?, 
                      password = ?,
                      nombre_completo = ?, 
                      email = ?, 
                      rol = ?, 
                      activo = ?
                      WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssii", $usuario, $password_hash, $nombre_completo, $email, $rol, $activo, $admin_id);
        } else {
            $query = "UPDATE usuarios_admin SET 
                      usuario = ?, 
                      nombre_completo = ?, 
                      email = ?, 
                      rol = ?, 
                      activo = ?
                      WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssii", $usuario, $nombre_completo, $email, $rol, $activo, $admin_id);
        }
        
        if ($stmt->execute()) {
            redirigirConMensaje('administradores_crud.php', 'Administrador actualizado exitosamente', 'success');
        } else {
            $errores[] = "Error al actualizar el administrador: " . $conn->error;
        }
    }
}

include '../includes/header.php';
?>

<div class="content-card">
    <h1 class="page-title">
        <i class="fas fa-user-edit"></i> Editar Administrador
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
                    <label for="usuario" class="form-label">Nombre de Usuario *</label>
                    <input type="text" class="form-control" id="usuario" name="usuario" 
                           value="<?php echo htmlspecialchars($administrador['usuario']); ?>" 
                           required>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nombre_completo" class="form-label">Nombre Completo *</label>
                    <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" 
                           value="<?php echo htmlspecialchars($administrador['nombre_completo']); ?>" 
                           required>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Deja los campos de contraseña en blanco si no deseas cambiarla
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="password" class="form-label">Nueva Contraseña (opcional)</label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Dejar en blanco para no cambiar">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Confirmar Nueva Contraseña</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                           placeholder="Repetir contraseña">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo htmlspecialchars($administrador['email']); ?>">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="rol" class="form-label">Rol de Usuario *</label>
                    <select class="form-control" id="rol" name="rol" required>
                        <option value="admin" <?php echo $administrador['rol'] === 'admin' ? 'selected' : ''; ?>>
                            Administrador
                        </option>
                        <option value="super_admin" <?php echo $administrador['rol'] === 'super_admin' ? 'selected' : ''; ?>>
                            Super Administrador
                        </option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="activo" name="activo" 
                       <?php echo $administrador['activo'] == 1 ? 'checked' : ''; ?>>
                <label class="form-check-label" for="activo">
                    Usuario activo
                </label>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Actualizar Administrador
            </button>
            <a href="administradores_crud.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>