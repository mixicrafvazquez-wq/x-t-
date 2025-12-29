<?php
require_once '../includes/conexion.php';
require_once '../includes/sesion.php';

requerirAutenticacion();

$titulo_pagina = 'Historial de Producción';

// Obtener todas las entradas con información de productor y producto
$query = "SELECT ep.*, 
          prod.nombre as productor_nombre, 
          prod.apellidos as productor_apellidos,
          prod.localidad as productor_localidad,
          p.nombre as producto_nombre,
          p.categoria as producto_categoria
          FROM entradas_produccion ep
          INNER JOIN productores prod ON ep.productor_id = prod.id
          INNER JOIN productos p ON ep.producto_id = p.id
          ORDER BY ep.fecha_entrega DESC, ep.fecha_registro DESC";

$resultado = $conn->query($query);

// Obtener estadísticas generales
$stats_query = "SELECT 
                COUNT(*) as total_entradas,
                COALESCE(SUM(cantidad), 0) as total_kg,
                COUNT(DISTINCT productor_id) as productores_distintos
                FROM entradas_produccion";
$stats = $conn->query($stats_query)->fetch_assoc();

include '../includes/header.php';
?>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">
            <i class="fas fa-history"></i> Historial de Entradas de Producción
        </h1>
        <a href="registro_produccion.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Entrada
        </a>
    </div>
    
    <!-- Estadísticas Generales -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="alert alert-primary mb-0">
                <h6 class="mb-2"><i class="fas fa-list"></i> Total de Entradas</h6>
                <h3 class="mb-0"><?php echo number_format($stats['total_entradas']); ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="alert alert-success mb-0">
                <h6 class="mb-2"><i class="fas fa-weight"></i> Total Recibido</h6>
                <h3 class="mb-0"><?php echo number_format($stats['total_kg'], 2); ?> kg</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="alert alert-info mb-0">
                <h6 class="mb-2"><i class="fas fa-users"></i> Productores Activos</h6>
                <h3 class="mb-0"><?php echo $stats['productores_distintos']; ?></h3>
            </div>
        </div>
    </div>
    
    <?php if ($resultado->num_rows > 0): ?>
        <div class="table-responsive">
            <table id="tablaProduccion" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha Entrega</th>
                        <th>Productor</th>
                        <th>Localidad</th>
                        <th>Producto</th>
                        <th>Cantidad (kg)</th>
                        <th>Notas</th>
                        <th>Fecha Registro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($entrada = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo $entrada['id']; ?></strong></td>
                            <td>
                                <i class="fas fa-calendar text-muted"></i>
                                <?php echo date('d/m/Y', strtotime($entrada['fecha_entrega'])); ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($entrada['productor_nombre'] . ' ' . $entrada['productor_apellidos']); ?></strong>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($entrada['productor_localidad']); ?>
                                </small>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($entrada['producto_nombre']); ?>
                                <br>
                                <span class="badge bg-info">
                                    <?php echo ucwords(str_replace('_', ' ', $entrada['producto_categoria'])); ?>
                                </span>
                            </td>
                            <td>
                                <strong class="text-success">
                                    <?php echo number_format($entrada['cantidad'], 2); ?> kg
                                </strong>
                            </td>
                            <td>
                                <?php if (!empty($entrada['notas'])): ?>
                                    <small><?php echo htmlspecialchars($entrada['notas']); ?></small>
                                <?php else: ?>
                                    <span class="text-muted">Sin notas</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php echo date('d/m/Y H:i', strtotime($entrada['fecha_registro'])); ?>
                                </small>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle fa-3x mb-3"></i>
            <h4>No hay entradas de producción registradas</h4>
            <p>Comience registrando la primera entrada de producción</p>
            <a href="registro_produccion.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Registrar Primera Entrada
            </a>
        </div>
    <?php endif; ?>
</div>
<div class="content-card mt-4">
    <h5 class="mb-3"><i class="fas fa-chart-pie"></i> Resumen por Productor (Mes Actual)</h5>
    
    <?php
    $resumen_query = "SELECT 
                      prod.nombre, 
                      prod.apellidos,
                      COUNT(ep.id) as total_entradas,
                      COALESCE(SUM(ep.cantidad), 0) as total_kg
                      FROM productores prod
                      LEFT JOIN entradas_produccion ep ON prod.id = ep.productor_id 
                          AND MONTH(ep.fecha_entrega) = MONTH(CURRENT_DATE())
                          AND YEAR(ep.fecha_entrega) = YEAR(CURRENT_DATE())
                      WHERE prod.activo = 1
                      GROUP BY prod.id
                      HAVING total_entradas > 0
                      ORDER BY total_kg DESC";
    
    $resumen = $conn->query($resumen_query);
    
    if ($resumen->num_rows > 0):
    ?>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Productor</th>
                        <th>Entradas</th>
                        <th>Total (kg)</th>
                        <th>Progreso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $max_kg = 0;
                    $data_productores = [];
                    
                    // Primera pasada para obtener el máximo
                    while ($row = $resumen->fetch_assoc()) {
                        $data_productores[] = $row;
                        if ($row['total_kg'] > $max_kg) {
                            $max_kg = $row['total_kg'];
                        }
                    }
                    
                    // Segunda pasada para mostrar los datos
                    foreach ($data_productores as $prod):
                        $porcentaje = $max_kg > 0 ? ($prod['total_kg'] / $max_kg) * 100 : 0;
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($prod['nombre'] . ' ' . $prod['apellidos']); ?></td>
                            <td><?php echo $prod['total_entradas']; ?></td>
                            <td><strong><?php echo number_format($prod['total_kg'], 2); ?> kg</strong></td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: <?php echo $porcentaje; ?>%">
                                        <?php echo number_format($porcentaje, 0); ?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle"></i>
            No hay entradas registradas en el mes actual
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>