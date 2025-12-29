<?php
require_once '../includes/conexion.php';
require_once '../includes/sesion.php';

// Verificar autenticación
requerirAutenticacion();

$titulo_pagina = 'Gestión de Productos';

// Obtener todos los productos
$query = "SELECT * FROM productos ORDER BY id DESC";
$resultado = $conn->query($query);

include '../includes/header.php';
?>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">
            <i class="fas fa-box"></i> Gestión de Productos
        </h1>
        <a href="agregar_producto.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Agregar Producto
        </a>
    </div>
    
    <div class="table-responsive">
        <table id="tablaProductos" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Tipo</th>
                    <th>Precio</th>
                    <th>Disponibilidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($producto = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $producto['id']; ?></td>
                        <td>
                            <?php if (!empty($producto['imagen'])): ?>
                                <img src="../img/productos/<?php echo htmlspecialchars($producto['imagen']); ?>" 
                                     alt="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; background: #e0e0e0; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td>
                            <span class="badge bg-info">
                                <?php echo ucwords(str_replace('_', ' ', $producto['categoria'])); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($producto['tipo'] === 'fruta_fresca'): ?>
                                <span class="badge bg-success">Fruta Fresca</span>
                            <?php else: ?>
                                <span class="badge bg-warning">Procesado</span>
                            <?php endif; ?>
                        </td>
                        <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                        <td>
                            <?php 
                            echo number_format($producto['disponibilidad'], 2); 
                            echo ' ' . $producto['unidad_medida'];
                            ?>
                        </td>
                        <td>
                            <?php if ($producto['activo'] == 1): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="editar_producto.php?id=<?php echo $producto['id']; ?>" 
                               class="btn btn-sm btn-warning" 
                               title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="eliminar_producto.php?id=<?php echo $producto['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('¿Está seguro de eliminar este producto?');"
                               title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>