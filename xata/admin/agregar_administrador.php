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

$titulo_pagina = 'Agregar Administrador';
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = limpiarDatos($_POST['usuario']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $nombre_completo = limpiarDatos($_POST['nombre_completo']);
    $email = limpiarDatos($_POST['email']);
    $rol = limpiarDatos($_POST['rol']);
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    // Validaciones
    if (empty($usuario)) $errores[] = "El nombre de usuario es requerido";
    if (strlen($usuario) < 4) $errores[] = "El usuario debe tener al menos 4 caracteres";
    if (empty($password)) $errores[] = "La contraseña es requerida";
    if (strlen($password) < 4) $errores[] = "La contraseña debe tener al menos 4 caracteres";
    if ($password !== $password_confirm) $errores[] = "Las contraseñas no coinciden";
    if (empty($nombre_completo)) $errores[] = "El nombre completo es requerido";
    if (empty($rol)) $errores[] = "El rol es requerido";
    
    // Validar email si se proporcionó
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del email no es válido";
    }
    
    // Verificar que el usuario no exista
    if (empty($errores)) {
        $query_check = "SELECT id FROM usuarios_admin WHERE usuario = ?";
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->bind_param("s", $usuario);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows > 0) {
            $errores[] = "El nombre de usuario ya está en uso";
        }
    }
    
    // Insertar si no hay errores
    if (empty($errores)) {
        $password_hash = md5($password);
        
        $query = "INSERT INTO usuarios_admin (usuario, password, nombre_completo, email, rol, activo) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $usuario, $password_hash, $nombre_completo, $email, $rol, $activo);
        
        if ($stmt->execute()) {
            redirigirConMensaje('administradores_crud.php', 'Administrador registrado exitosamente', 'success');
        } else {
            $errores[] = "Error al registrar el administrador: " . $conn->error;
        }
    }
}

include '../includes/header.php';
?>

<div class="content-card">
    <h1 class="page-title">
        <i class="fas fa-user-plus"></i> Registrar Nuevo Administrador
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
                           value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>" 
                           placeholder="usuario123"
                           required>
                    <small class="text-muted">Mínimo 4 caracteres, sin espacios</small>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nombre_completo" class="form-label">Nombre Completo *</label>
                    <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" 
                           value="<?php echo isset($_POST['nombre_completo']) ? htmlspecialchars($_POST['nombre_completo']) : ''; ?>" 
                           placeholder="Juan Pérez García"
                           required>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña *</label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Mínimo 4 caracteres"
                           required>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Confirmar Contraseña *</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                           placeholder="Repite la contraseña"
                           required>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                           placeholder="correo@ejemplo.com">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="rol" class="form-label">Rol de Usuario *</label>
                    <select class="form-control" id="rol" name="rol" required>
                        <option value="">Seleccione un rol</option>
                        <option value="admin">Administrador</option>
                        <option value="super_admin">Super Administrador</option>
                    </select>
                    <small class="text-muted">
                        <strong>Admin:</strong> Puede gestionar productos, productores y producción.<br>
                        <strong>Super Admin:</strong> Puede hacer todo + gestionar otros administradores.
                    </small>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="activo" name="activo" checked>
                <label class="form-check-label" for="activo">
                    Usuario activo (puede iniciar sesión)
                </label>
            </div>
        </div>
        
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Importante:</strong> Asegúrate de anotar el nombre de usuario y contraseña. 
            El nuevo administrador deberá usar estas credenciales para iniciar sesión.
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Registrar Administrador
            </button>
            <a href="administradores_crud.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>