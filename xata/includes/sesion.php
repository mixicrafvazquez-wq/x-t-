<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function estaAutenticado() {
    return isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_nombre']);
}
function requerirAutenticacion() {
    if (!estaAutenticado()) {
        header("Location: ../login.php");
        exit();
    }
}
function iniciarSesion($id, $usuario, $nombre_completo) {
    $_SESSION['usuario_id'] = $id;
    $_SESSION['usuario_nombre'] = $usuario;
    $_SESSION['nombre_completo'] = $nombre_completo;
    $_SESSION['tiempo_inicio'] = time();
    
    // Regenerar ID de sesión por seguridad
    session_regenerate_id(true);
}
function cerrarSesion() {
    // Destruir todas las variables de sesión
    $_SESSION = array();
    
    // Destruir la cookie de sesión
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
    
    // Destruir la sesión
    session_destroy();
    
    // Redirigir al login
    header("Location: ../login.php");
    exit();
}
function obtenerNombreUsuario() {
    return isset($_SESSION['nombre_completo']) ? $_SESSION['nombre_completo'] : '';
}
function verificarInactividad() {
    $tiempo_limite = 1800; // 30 minutos en segundos
    
    if (isset($_SESSION['tiempo_inicio'])) {
        $tiempo_transcurrido = time() - $_SESSION['tiempo_inicio'];
        
        if ($tiempo_transcurrido > $tiempo_limite) {
            cerrarSesion();
        }
    }
    // Actualizar el tiempo de inicio
    $_SESSION['tiempo_inicio'] = time();
}
// Verificar inactividad automáticamente si hay sesión activa
if (estaAutenticado()) {
    verificarInactividad();
}
?>