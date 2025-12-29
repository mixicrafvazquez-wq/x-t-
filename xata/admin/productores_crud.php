<?php
require_once '../includes/conexion.php';
require_once '../includes/sesion.php';

requerirAutenticacion();

$titulo_pagina = 'Gestión de Productores';

// Obtener todos los productores
$query = "SELECT * FROM productores ORDER BY id DESC";
$resultado = $conn->query($query);

include '../includes/header.php';
?>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">
            <i class="fas fa-users"></i> Gestión de Productores
        </h1>
        <a href="agregar_productor.php" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Agregar Productor
        </a>
    </div>
    
    <div class="table-responsive">
        <table id="tablaProductores" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Localidad</th>
                    <th>Teléfono</th>
                    <th>Tipo de Producto</th>
                    <th>Producción Mensual</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($productor = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $productor['id']; ?></td>
                        <td>
                            <strong>
                                <?php echo htmlspecialchars($productor['nombre'] . ' ' . $productor['apellidos']); ?>
                            </strong>
                        </td>
                        <td>
                            <i class="fas fa-map-marker-alt text-muted"></i>
                            <?php echo htmlspecialchars($productor['localidad']); ?>
                        </td>
                        <td>
                            <?php if (!empty($productor['telefono'])): ?>
                                <i class="fas fa-phone text-muted"></i>
                                <?php echo htmlspecialchars($productor['telefono']); ?>
                            <?php else: ?>
                                <span class="text-muted">No registrado</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-info">
                                <?php echo htmlspecialchars($productor['tipo_producto']); ?>
                            </span>
                        </td>
                        <td>
                            <strong><?php echo number_format($productor['produccion_promedio_mensual'], 2); ?></strong> kg
                        </td>
                        <td>
                            <?php if ($productor['activo'] == 1): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="editar_productor.php?id=<?php echo $productor['id']; ?>" 
                               class="btn btn-sm btn-warning" 
                               title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="eliminar_productor.php?id=<?php echo $productor['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('¿Está seguro de eliminar este productor?');"
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