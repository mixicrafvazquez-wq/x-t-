<?php
require_once 'includes/conexion.php';
// Obtener todos los productores activos
$query = "SELECT * FROM productores WHERE activo = 1 ORDER BY nombre ASC";
$resultado = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestros Productores - Asociación xät'ä</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #2ecc71;
            --secondary-color: #27ae60;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand, .nav-link {
            color: white !important;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 60px 0 40px;
            margin-bottom: 40px;
        }
        
        .productor-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .productor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        }
        
        .productor-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            margin: 0 auto 20px;
        }
        
        .productor-name {
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .info-item {
            display: flex;
            align-items: start;
            margin-bottom: 12px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .info-item i {
            color: var(--primary-color);
            margin-right: 10px;
            font-size: 18px;
            min-width: 24px;
        }
    </style>
</head>
<body>
    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-leaf"></i> xät'ä
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="catalogo.php">Catálogo</a></li>
                    <li class="nav-item"><a class="nav-link active" href="productores_publico.php">Productores</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php"><i class="fas fa-user-lock"></i> Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="page-header">
        <div class="container">
            <h1><i class="fas fa-users"></i> Nuestros Productores</h1>
            <p class="lead">Conoce a los productores locales que hacen posible nuestros productos de calidad</p>
        </div>
    </div>

    <div class="container mb-5">
        <?php if ($resultado->num_rows > 0): ?>
            <div class="row g-4">
                <?php while ($productor = $resultado->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="productor-card">
                            <div class="productor-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            
                            <h3 class="productor-name">
                                <?php echo htmlspecialchars($productor['nombre'] . ' ' . $productor['apellidos']); ?>
                            </h3>
                            
                            <div class="info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <strong>Localidad:</strong><br>
                                    <?php echo htmlspecialchars($productor['localidad']); ?>
                                </div>
                            </div>
                            
                            <?php if (!empty($productor['telefono'])): ?>
                                <div class="info-item">
                                    <i class="fas fa-phone"></i>
                                    <div>
                                        <strong>Teléfono:</strong><br>
                                        <?php echo htmlspecialchars($productor['telefono']); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="info-item">
                                <i class="fas fa-seedling"></i>
                                <div>
                                    <strong>Tipo de Producto:</strong><br>
                                    <?php echo htmlspecialchars($productor['tipo_producto']); ?>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <i class="fas fa-chart-line"></i>
                                <div>
                                    <strong>Producción Promedio Mensual:</strong><br>
                                    <?php echo number_format($productor['produccion_promedio_mensual'], 2); ?> kg
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <h4>No hay productores registrados</h4>
                <p>Próximamente agregaremos más información sobre nuestros productores</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>