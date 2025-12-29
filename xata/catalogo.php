<?php
require_once 'includes/conexion.php';

$filtro_tipo = isset($_GET['tipo']) ? limpiarDatos($_GET['tipo']) : '';
$filtro_categoria = isset($_GET['categoria']) ? limpiarDatos($_GET['categoria']) : '';
$buscar_color = isset($_GET['color']) ? limpiarDatos($_GET['color']) : '';

$where_clauses = ["activo = 1"];
$params = [];
$types = "";

if (!empty($filtro_tipo)) {
    $where_clauses[] = "tipo = ?";
    $params[] = $filtro_tipo;
    $types .= "s";
}

if (!empty($filtro_categoria)) {
    $where_clauses[] = "categoria = ?";
    $params[] = $filtro_categoria;
    $types .= "s";
}

if (!empty($buscar_color)) {
    $where_clauses[] = "categoria LIKE ?";
    $params[] = "%$buscar_color%";
    $types .= "s";
}

$where_sql = implode(" AND ", $where_clauses);
$query = "SELECT * FROM productos WHERE $where_sql ORDER BY nombre ASC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$resultado = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - xät'ä</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f7fafc;
        }
        
        .navbar-modern {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .nav-link {
            color: #2d3748 !important;
            font-weight: 600;
            margin: 0 10px;
        }
        
        .page-header-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 100px 0 60px;
            color: white;
            margin-top: 76px;
        }
        
        .page-header-modern h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 15px;
        }
        
        .filter-card-modern {
            background: white;
            border-radius: 25px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            margin-bottom: 40px;
        }
        
        .filter-card-modern h5 {
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 25px;
        }
        
        .form-control, .form-select {
            border-radius: 15px;
            border: 2px solid #e2e8f0;
            padding: 12px 20px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-search {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .btn-clear {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            padding: 12px 30px;
            border-radius: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-clear:hover {
            background: #667eea;
            color: white;
        }
        
        .product-card-modern {
            background: white;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .product-card-modern:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(102, 126, 234, 0.2);
        }
        
        .product-image-wrapper {
            position: relative;
            height: 280px;
            overflow: hidden;
        }
        
        .product-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.3s ease;
        }
        
        .product-card-modern:hover .product-image-wrapper img {
            transform: scale(1.1);
        }
        
        .product-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .product-content {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .product-title {
            font-size: 22px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
        }
        
        .product-price {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
        }
        
        .product-info {
            color: #718096;
            margin-bottom: 15px;
            flex-grow: 1;
        }
        
        .btn-add-cart {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-add-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .cart-float {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .cart-float:hover {
            transform: scale(1.1);
            box-shadow: 0 15px 50px rgba(102, 126, 234, 0.5);
        }
        
        .cart-float i {
            font-size: 28px;
            color: white;
        }
        
        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #f093fb;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .badge-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 6px 12px;
            border-radius: 10px;
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 20px;
        }
        
        .empty-state i {
            font-size: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
        }
        
        .empty-state h4 {
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg navbar-modern fixed-top">
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
                    <li class="nav-item"><a class="nav-link active" href="catalogo.php">Catálogo</a></li>
                    <li class="nav-item"><a class="nav-link" href="productores_publico.php">Productores</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php"><i class="fas fa-user-lock"></i> Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="page-header-modern">
        <div class="container">
            <h1><i class="fas fa-shopping-bag"></i> Catálogo de Productos</h1>
            <p class="lead">Descubre nuestra selección de productos frescos y artesanales</p>
        </div>
    </div>

    <div class="container my-5">
        <!-- Filtros -->
        <div class="filter-card-modern">
            <h5><i class="fas fa-filter me-2"></i>Filtrar Productos</h5>
            <form method="GET" action="catalogo.php">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Buscar por Color</label>
                        <input type="text" class="form-control" name="color" 
                               placeholder="verde, roja, amarilla..." 
                               value="<?php echo htmlspecialchars($buscar_color); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tipo de Producto</label>
                        <select class="form-select" name="tipo">
                            <option value="">Todos los tipos</option>
                            <option value="fruta_fresca" <?php echo $filtro_tipo === 'fruta_fresca' ? 'selected' : ''; ?>>
                                Fruta Fresca
                            </option>
                            <option value="procesado" <?php echo $filtro_tipo === 'procesado' ? 'selected' : ''; ?>>
                                Procesados
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Categoría</label>
                        <select class="form-select" name="categoria">
                            <option value="">Todas</option>
                            <option value="mermelada" <?php echo $filtro_categoria === 'mermelada' ? 'selected' : ''; ?>>Mermeladas</option>
                            <option value="tuna_verde" <?php echo $filtro_categoria === 'tuna_verde' ? 'selected' : ''; ?>>Tuna Verde</option>
                            <option value="tuna_roja" <?php echo $filtro_categoria === 'tuna_roja' ? 'selected' : ''; ?>>Tuna Roja</option>
                            <option value="tuna_amarilla" <?php echo $filtro_categoria === 'tuna_amarilla' ? 'selected' : ''; ?>>Tuna Amarilla</option>
                            <option value="xoconostle" <?php echo $filtro_categoria === 'xoconostle' ? 'selected' : ''; ?>>Xoconostle</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-search">
                        <i class="fas fa-search me-2"></i>Buscar
                    </button>
                    <a href="catalogo.php" class="btn btn-clear">
                        <i class="fas fa-times me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>

        <!-- Productos -->
        <div class="row g-4">
            <?php if ($resultado->num_rows > 0): ?>
                <?php while ($producto = $resultado->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="product-card-modern">
                            <div class="product-image-wrapper">
                                <?php if (!empty($producto['imagen'])): ?>
                                    <img src="img/productos/<?php echo htmlspecialchars($producto['imagen']); ?>" 
                                         alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                <?php else: ?>
                                    <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image fa-4x text-white"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($producto['tipo'] === 'fruta_fresca'): ?>
                                    <span class="product-badge" style="color: #10b981;">
                                        <i class="fas fa-leaf me-1"></i>Fresca
                                    </span>
                                <?php else: ?>
                                    <span class="product-badge" style="color: #f59e0b;">
                                        <i class="fas fa-jar me-1"></i>Procesado
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-content">
                                <h3 class="product-title"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                
                                <div class="mb-2">
                                    <span class="badge badge-gradient">
                                        <?php echo ucwords(str_replace('_', ' ', $producto['categoria'])); ?>
                                    </span>
                                </div>
                                
                                <?php if (!empty($producto['descripcion'])): ?>
                                    <p class="product-info small">
                                        <?php echo htmlspecialchars(substr($producto['descripcion'], 0, 80)); ?>...
                                    </p>
                                <?php endif; ?>
                                
                                <div class="product-price">
                                    $<?php echo number_format($producto['precio'], 2); ?>
                                    <small style="font-size: 16px; color: #718096;">
                                        / <?php echo $producto['unidad_medida']; ?>
                                    </small>
                                </div>
                                
                                <p class="text-muted small mb-3">
                                    <i class="fas fa-box me-1"></i>
                                    Disponible: <?php echo number_format($producto['disponibilidad'], 2); ?> 
                                    <?php echo $producto['unidad_medida']; ?>
                                </p>
                                
                                <button class="btn btn-add-cart" 
                                        onclick="agregarAlCarrito(<?php echo $producto['id']; ?>, '<?php echo addslashes($producto['nombre']); ?>', <?php echo $producto['precio']; ?>)">
                                    <i class="fas fa-cart-plus me-2"></i>Agregar al Carrito
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h4>No se encontraron productos</h4>
                        <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                        <a href="catalogo.php" class="btn btn-search mt-3">Ver todos los productos</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Carrito Flotante -->
    <div class="cart-float" onclick="mostrarCarrito()">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-count" id="cartCount">0</span>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/carrito.js"></script>
</body>
</html>