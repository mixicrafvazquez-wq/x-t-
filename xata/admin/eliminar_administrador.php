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
    redirigirConMensaje('dashboard.php', 'No tienes permisos para realizar esta acción', 'error');
}

// Verificar que se recibió un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirigirConMensaje('administradores_crud.php', 'ID de administrador inválido', 'error');
}

$admin_id = intval($_GET['id']);

// No permitir eliminar el propio usuario
if ($admin_id === $_SESSION['usuario_id']) {
    redirigirConMensaje('administradores_crud.php', 'No puedes eliminar tu propia cuenta', 'error');
}

// Verificar que el administrador existe
$query = "SELECT id, usuario, rol FROM usuarios_admin WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    redirigirConMensaje('administradores_crud.php', 'Administrador no encontrado', 'error');
}

$administrador = $resultado->fetch_assoc();

// No permitir eliminar super admins
if ($administrador['rol'] === 'super_admin') {
    redirigirConMensaje('administradores_crud.php', 'No se pueden eliminar cuentas de Super Administrador por seguridad', 'error');
}

// Eliminar el administrador
$query_eliminar = "DELETE FROM usuarios_admin WHERE id = ?";
$stmt_eliminar = $conn->prepare($query_eliminar);
$stmt_eliminar->bind_param("i", $admin_id);

if ($stmt_eliminar->execute()) {
    redirigirConMensaje('administradores_crud.php', 'Administrador eliminado exitosamente', 'success');
} else {
    redirigirConMensaje('administradores_crud.php', 'Error al eliminar el administrador: ' . $conn->error, 'error');
}
?>