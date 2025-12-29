<?php
require_once '../includes/conexion.php';
require_once '../includes/sesion.php';

requerirAutenticacion();

// Verificar que se recibió un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirigirConMensaje('productores_crud.php', 'ID de productor inválido', 'error');
}

$productor_id = intval($_GET['id']);

// Verificar que el productor existe
$query = "SELECT id, nombre, apellidos FROM productores WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $productor_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    redirigirConMensaje('productores_crud.php', 'Productor no encontrado', 'error');
}

$productor = $resultado->fetch_assoc();

// Verificar si hay entradas de producción relacionadas
$query_entradas = "SELECT COUNT(*) as total FROM entradas_produccion WHERE productor_id = ?";
$stmt_entradas = $conn->prepare($query_entradas);
$stmt_entradas->bind_param("i", $productor_id);
$stmt_entradas->execute();
$resultado_entradas = $stmt_entradas->get_result();
$entradas = $resultado_entradas->fetch_assoc();

if ($entradas['total'] > 0) {
    // No eliminar, solo desactivar si tiene entradas relacionadas
    $query_desactivar = "UPDATE productores SET activo = 0 WHERE id = ?";
    $stmt_desactivar = $conn->prepare($query_desactivar);
    $stmt_desactivar->bind_param("i", $productor_id);
    
    if ($stmt_desactivar->execute()) {
        redirigirConMensaje('productores_crud.php', 'El productor tiene entradas de producción asociadas. Se ha desactivado en lugar de eliminarse.', 'warning');
    } else {
        redirigirConMensaje('productores_crud.php', 'Error al desactivar el productor', 'error');
    }
} else {
    // Eliminar el productor
    $query_eliminar = "DELETE FROM productores WHERE id = ?";
    $stmt_eliminar = $conn->prepare($query_eliminar);
    $stmt_eliminar->bind_param("i", $productor_id);
    
    if ($stmt_eliminar->execute()) {
        redirigirConMensaje('productores_crud.php', 'Productor eliminado exitosamente', 'success');
    } else {
        redirigirConMensaje('productores_crud.php', 'Error al eliminar el productor: ' . $conn->error, 'error');
    }
}
?>