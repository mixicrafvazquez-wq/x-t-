<?php
require_once '../includes/conexion.php';
require_once '../includes/sesion.php';

requerirAutenticacion();

$titulo_pagina = 'Panel de Control';

// Obtener estadísticas
$stats = [];

// Total de productos activos
$query_productos = "SELECT COUNT(*) as total FROM productos WHERE activo = 1";
$result = $conn->query($query_productos);
$stats['productos'] = $result->fetch_assoc()['total'];

// Total de productores activos
$query_productores = "SELECT COUNT(*) as total FROM productores WHERE activo = 1";
$result = $conn->query($query_productores);
$stats['productores'] = $result->fetch_assoc()['total'];

// Total de entradas este mes
$query_entradas = "SELECT COUNT(*) as total FROM entradas_produccion 
                   WHERE MONTH(fecha_entrega) = MONTH(CURRENT_DATE()) 
                   AND YEAR(fecha_entrega) = YEAR(CURRENT_DATE())";
$result = $conn->query($query_entradas);
$stats['entradas_mes'] = $result->fetch_assoc()['total'];

// Total de kilogramos recibidos este mes
$query_kg = "SELECT COALESCE(SUM(cantidad), 0) as total FROM entradas_produccion 
             WHERE MONTH(fecha_entrega) = MONTH(CURRENT_DATE()) 
             AND YEAR(fecha_entrega) = YEAR(CURRENT_DATE())";
$result = $conn->query($query_kg);
$stats['kg_mes'] = $result->fetch_assoc()['total'];
// Ganancias totales
$query_ganancias_total = "SELECT COALESCE(SUM(total), 0) as total FROM ventas";
$result = $conn->query($query_ganancias_total);
$stats['ganancias_total'] = $result->fetch_assoc()['total'];

// Ganancias del mes actual
$query_ganancias_mes = "SELECT COALESCE(SUM(total), 0) as total FROM ventas 
                        WHERE MONTH(fecha_venta) = MONTH(CURRENT_DATE()) 
                        AND YEAR(fecha_venta) = YEAR(CURRENT_DATE())";
$result = $conn->query($query_ganancias_mes);
$stats['ganancias_mes'] = $result->fetch_assoc()['total'];

// Ganancias de hoy
$query_ganancias_hoy = "SELECT COALESCE(SUM(total), 0) as total FROM ventas 
                        WHERE DATE(fecha_venta) = CURRENT_DATE()";
$result = $conn->query($query_ganancias_hoy);
$stats['ganancias_hoy'] = $result->fetch_assoc()['total'];

// Ventas este mes
$query_ventas_mes = "SELECT COUNT(*) as total FROM ventas 
                     WHERE MONTH(fecha_venta) = MONTH(CURRENT_DATE()) 
                     AND YEAR(fecha_venta) = YEAR(CURRENT_DATE())";
$result = $conn->query($query_ventas_mes);
$stats['ventas_mes'] = $result->fetch_assoc()['total'];

// Productos con bajo inventario (menos de 50 kg)
$query_bajo_inventario = "SELECT nombre, disponibilidad, unidad_medida 
                          FROM productos 
                          WHERE activo = 1 AND disponibilidad < 50 
                          ORDER BY disponibilidad ASC 
                          LIMIT 5";
$productos_bajo = $conn->query($query_bajo_inventario);

// Últimas entradas de producción
$query_ultimas = "SELECT ep.*, prod.nombre as productor_nombre, prod.apellidos as productor_apellidos,
                  p.nombre as producto_nombre
                  FROM entradas_produccion ep
                  INNER JOIN productores prod ON ep.productor_id = prod.id
                  INNER JOIN productos p ON ep.producto_id = p.id
                  ORDER BY ep.fecha_registro DESC
                  LIMIT 5";
$ultimas_entradas = $conn->query($query_ultimas);

// Últimas ventas
$query_ultimas_ventas = "SELECT v.*, p.nombre as producto_nombre
                         FROM ventas v
                         INNER JOIN productos p ON v.producto_id = p.id
                         ORDER BY v.fecha_venta DESC
                         LIMIT 5";
$ultimas_ventas = $conn->query($query_ultimas_ventas);

// Verificar si es super admin
$es_super_admin = false;
$query_rol = "SELECT rol FROM usuarios_admin WHERE id = ?";
$stmt = $conn->prepare($query_rol);
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$result_rol = $stmt->get_result();
if ($result_rol->num_rows > 0) {
    $rol_data = $result_rol->fetch_assoc();
    $es_super_admin = ($rol_data['rol'] === 'super_admin');
}

include '../includes/header.php';
?>

<div class="content-card">
    <h1 class="page-title">
        <i class="fas fa-chart-line"></i> Resumen General
    </h1>
    <p class="text-muted">Bienvenido al panel de administración de la Asociación xät'ä</p>
</div>
<div class="row mb-4">
    <div class="col-md-6">
        <div class="content-card text-center" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
            <div>
                <i class="fas fa-dollar-sign fa-3x mb-3"></i>
            </div>
            <h2 class="mb-1">$<?php echo number_format($stats['ganancias_total'], 2); ?></h2>
            <p class="mb-0" style="font-size: 18px; opacity: 0.9;">Ganancias Totales Acumuladas</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="content-card text-center" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
            <div>
                <i class="fas fa-calendar-check fa-3x mb-3"></i>
            </div>
            <h2 class="mb-1">$<?php echo number_format($stats['ganancias_mes'], 2); ?></h2>
            <p class="mb-0" style="font-size: 18px; opacity: 0.9;">Ganancias Este Mes</p>
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-md-3">
        <div class="content-card text-center">
            <div style="color: #667eea;">
                <i class="fas fa-money-bill-wave fa-3x mb-3"></i>
            </div>
            <h3 class="mb-1">$<?php echo number_format($stats['ganancias_hoy'], 2); ?></h3>
            <p class="text-muted mb-0">Ganancias Hoy</p>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="content-card text-center">
            <div style="color: #8b5cf6;">
                <i class="fas fa-box fa-3x mb-3"></i>
            </div>
            <h3 class="mb-1"><?php echo $stats['productos']; ?></h3>
            <p class="text-muted mb-0">Productos Activos</p>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="content-card text-center">
            <div style="color: #28a745;">
                <i class="fas fa-users fa-3x mb-3"></i>
            </div>
            <h3 class="mb-1"><?php echo $stats['productores']; ?></h3>
            <p class="text-muted mb-0">Productores Registrados</p>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="content-card text-center">
            <div style="color: #ffc107;">
                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
            </div>
            <h3 class="mb-1"><?php echo $stats['ventas_mes']; ?></h3>
            <p class="text-muted mb-0">Ventas Este Mes</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="content-card">
            <h5 class="mb-3">
                <i class="fas fa-cash-register text-success"></i> 
                Últimas Ventas Registradas
            </h5>
            
            <?php if ($ultimas_ventas->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($venta = $ultimas_ventas->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($venta['fecha_venta'])); ?></td>
                                    <td><?php echo htmlspecialchars($venta['producto_nombre']); ?></td>
                                    <td><?php echo number_format($venta['cantidad'], 2); ?></td>
                                    <td>
                                        <strong class="text-success">
                                            $<?php echo number_format($venta['total'], 2); ?>
                                        </strong>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="ventas.php" class="btn btn-sm btn-outline-primary">Ver todas las ventas</a>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    No hay ventas registradas aún
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="content-card">
            <h5 class="mb-3">
                <i class="fas fa-exclamation-triangle text-warning"></i> 
                Productos con Bajo Inventario
            </h5>
            
            <?php if ($productos_bajo->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Disponibilidad</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($producto = $productos_bajo->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                    <td>
                                        <?php echo number_format($producto['disponibilidad'], 2); ?> 
                                        <?php echo $producto['unidad_medida']; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">Bajo Stock</span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> 
                    Todos los productos tienen inventario suficiente
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="content-card mt-4">
    <h5 class="mb-3">
        <i class="fas fa-clock"></i> 
        Últimas Entradas de Producción
    </h5>
    
    <?php if ($ultimas_entradas->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Productor</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($entrada = $ultimas_entradas->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($entrada['fecha_entrega'])); ?></td>
                            <td>
                                <?php 
                                echo htmlspecialchars($entrada['productor_nombre'] . ' ' . $entrada['productor_apellidos']); 
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($entrada['producto_nombre']); ?></td>
                            <td><?php echo number_format($entrada['cantidad'], 2); ?> kg</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            No hay entradas registradas aún
        </div>
    <?php endif; ?>
</div>
<div class="content-card mt-4">
    <h5 class="mb-3">
        <i class="fas fa-bolt"></i> Accesos Rápidos
    </h5>
    <div class="row text-center">
        <div class="col-md-3 mb-3">
            <a href="agregar_producto.php" class="btn btn-primary btn-lg w-100">
                <i class="fas fa-plus-circle"></i><br>
                Agregar Producto
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="agregar_productor.php" class="btn btn-success btn-lg w-100">
                <i class="fas fa-user-plus"></i><br>
                Agregar Productor
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="registro_produccion.php" class="btn btn-warning btn-lg w-100">
                <i class="fas fa-clipboard-check"></i><br>
                Registrar Entrada
            </a>
        </div>
        <?php if ($es_super_admin): ?>
        <div class="col-md-3 mb-3">
            <a href="administradores_crud.php" class="btn btn-info btn-lg w-100">
                <i class="fas fa-user-shield"></i><br>
                Administradores
            </a>
        </div>
        <?php else: ?>
        <div class="col-md-3 mb-3">
            <a href="historial_produccion.php" class="btn btn-info btn-lg w-100">
                <i class="fas fa-list-alt"></i><br>
                Ver Historial
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>