<?php
require_once '../includes/conexion.php';
require_once '../includes/sesion.php';

requerirAutenticacion();

// Verificar que se recibió un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirigirConMensaje('productos_crud.php', 'ID de producto inválido', 'error');
}

$producto_id = intval($_GET['id']);

// Verificar que el producto existe
$query = "SELECT id, nombre, imagen FROM productos WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $producto_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    redirigirConMensaje('productos_crud.php', 'Producto no encontrado', 'error');
}

$producto = $resultado->fetch_assoc();

// Verificar si hay entradas de producción relacionadas
$query_entradas = "SELECT COUNT(*) as total FROM entradas_produccion WHERE producto_id = ?";
$stmt_entradas = $conn->prepare($query_entradas);
$stmt_entradas->bind_param("i", $producto_id);
$stmt_entradas->execute();
$resultado_entradas = $stmt_entradas->get_result();
$entradas = $resultado_entradas->fetch_assoc();

if ($entradas['total'] > 0) {
    // No eliminar, solo desactivar si tiene entradas relacionadas
    $query_desactivar = "UPDATE productos SET activo = 0 WHERE id = ?";
    $stmt_desactivar = $conn->prepare($query_desactivar);
    $stmt_desactivar->bind_param("i", $producto_id);
    
    if ($stmt_desactivar->execute()) {
        redirigirConMensaje('productos_crud.php', 'El producto tiene entradas de producción asociadas. Se ha desactivado en lugar de eliminarse.', 'warning');
    } else {
        redirigirConMensaje('productos_crud.php', 'Error al desactivar el producto', 'error');
    }
} else {
    // Eliminar la imagen si existe
    if (!empty($producto['imagen'])) {
        $ruta_imagen = '../img/productos/' . $producto['imagen'];
        if (file_exists($ruta_imagen)) {
            unlink($ruta_imagen);
        }
    }
    
    // Eliminar el producto
    $query_eliminar = "DELETE FROM productos WHERE id = ?";
    $stmt_eliminar = $conn->prepare($query_eliminar);
    $stmt_eliminar->bind_param("i", $producto_id);
    
    if ($stmt_eliminar->execute()) {
        redirigirConMensaje('productos_crud.php', 'Producto eliminado exitosamente', 'success');
    } else {
        redirigirConMensaje('productos_crud.php', 'Error al eliminar el producto: ' . $conn->error, 'error');
    }
}
?>