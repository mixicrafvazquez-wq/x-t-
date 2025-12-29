<?php
require_once 'includes/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asociación xät'ä - Productores de Tunas</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #10b981;
            --secondary-color: #059669;
            --accent-color: #f97316;
            --accent-light: #fb923c;
        }
        
        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-size: 28px;
            font-weight: 700;
            color: white !important;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .nav-link {
            color: rgba(255,255,255,0.95) !important;
            font-weight: 500;
            transition: all 0.3s;
            margin: 0 5px;
        }
        
        .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="40" fill="white" opacity="0.05"/></svg>');
            animation: float 20s infinite linear;
        }
        
        @keyframes float {
            from { transform: translateY(0); }
            to { transform: translateY(-100px); }
        }
        
        .hero-section h1 {
            font-size: 56px;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
        }
        
        .hero-section p {
            font-size: 22px;
            margin-bottom: 30px;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }
        
        .section-title {
            font-size: 42px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 50px;
            color: var(--primary-color);
        }
        
        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 35px;
            text-align: center;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            transition: all 0.3s ease;
            height: 100%;
            border: 3px solid transparent;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            border-color: var(--accent-color);
        }
        
        .feature-icon {
            font-size: 56px;
            color: var(--accent-color);
            margin-bottom: 20px;
        }
        
        .feature-card h4 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .btn-custom {
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--accent-light) 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 18px;
            transition: all 0.3s;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            position: relative;
            z-index: 1;
        }
        
        .btn-custom:hover {
            background: linear-gradient(135deg, var(--accent-light) 0%, var(--accent-color) 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
            color: white;
        }
        
        .carousel-item img {
            height: 500px;
            object-fit: cover;
            border-radius: 20px;
        }
        
        .carousel {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .carousel-caption {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 15px 25px;
        }
        
        .about-section {
            background: linear-gradient(180deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 80px 0;
        }
        
        footer {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: white;
            padding: 50px 0 20px;
            margin-top: 80px;
        }
        
        footer h5 {
            color: var(--accent-color);
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        footer a:hover {
            color: var(--accent-color);
            transform: translateX(5px);
        }
        
        .product-preview-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            transition: all 0.3s;
        }
        
        .product-preview-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }
        
        .product-preview-card img {
            height: 250px;
            object-fit: cover;
            width: 100%;
        }
        
        .product-preview-card .card-body {
            padding: 25px;
        }
        
        .product-preview-card h4 {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .badge-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-leaf"></i> xät'ä
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="catalogo.php">Catálogo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="productores_publico.php">Productores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fas fa-user-lock"></i> Administración
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1><i class="fas fa-leaf"></i> Asociación de Productores xät'ä</h1>
            <p>Productores de tunas de la más alta calidad y productos derivados artesanales</p>
            <a href="catalogo.php" class="btn btn-custom btn-lg">
                <i class="fas fa-shopping-cart"></i> Ver Productos
            </a>
        </div>
    </section>

    <!-- Carrusel de Imágenes -->
    <section class="py-5">
        <div class="container">
            <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="0" class="active"></button>
                    <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="1"></button>
                    <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="2"></button>
                    <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="3"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="/XATA/img/carousel/tunas.jpg" class="d-block w-100" alt="Tunas Frescas">
                        <div class="carousel-caption">
                            <h3>Tunas de Diversos Colores</h3>
                            <p>Verde, roja, amarilla y bonda de la mejor calidad</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="/XATA/img/carousel/xoconostle.jpg" class="d-block w-100" alt="Xoconostles">
                        <div class="carousel-caption">
                            <h3>Xoconostles Frescos</h3>
                            <p>Producto tradicional mexicano de excelente sabor</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="/XATA/img/carousel/mermelada.jpg" class="d-block w-100" alt="Mermeladas">
                        <div class="carousel-caption">
                            <h3>Mermeladas Artesanales</h3>
                            <p>Elaboradas con nuestras tunas más frescas</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="/XATA/img/carousel/tunades.jpg" class="d-block w-100" alt="Productos Procesados">
                        <div class="carousel-caption">
                            <h3>Productos Procesados</h3>
                            <p>Salsas, dulces y deshidratados de calidad premium</p>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>
    </section>
    <section class="about-section">
        <div class="container">
            <h2 class="section-title">¿Quiénes Somos?</h2>
            <div class="row align-items-center mb-5">
                <div class="col-md-6">
                    <p style="font-size: 18px; line-height: 1.8;">
                        La Asociación <strong>xät'ä</strong> es una organización de productores comprometidos con la excelencia 
                        en la producción y comercialización de tunas de diversos colores, xoconostles y productos derivados 
                        de alta calidad.
                    </p>
                    <p style="font-size: 18px; line-height: 1.8;">
                        Trabajamos directamente con productores locales para garantizar la frescura y calidad de todos 
                        nuestros productos, desde las tunas frescas hasta las mermeladas, salsas y productos deshidratados 
                        que elaboramos artesanalmente.
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="feature-card">
                                <div class="feature-icon"><i class="fas fa-leaf"></i></div>
                                <h5>100% Natural</h5>
                                <p>Productos cultivados de forma natural y sostenible</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="feature-card">
                                <div class="feature-icon"><i class="fas fa-award"></i></div>
                                <h5>Calidad Premium</h5>
                                <p>Los mejores estándares de calidad</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="feature-card">
                                <div class="feature-icon"><i class="fas fa-users"></i></div>
                                <h5>Apoyo Local</h5>
                                <p>Respaldamos a productores locales</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="feature-card">
                                <div class="feature-icon"><i class="fas fa-heart"></i></div>
                                <h5>Hecho con Amor</h5>
                                <p>Productos elaborados artesanalmente</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="py-5">
        <div class="container">
            <h2 class="section-title">Nuestros Productos</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="product-preview-card">
                        <img src="/XATA/img/carousel/tunas.jpg" alt="Tunas">
                        <div class="card-body">
                            <span class="badge badge-custom mb-2">Fruta Fresca</span>
                            <h4>Tunas Frescas</h4>
                            <p>Verde, roja, amarilla y bonda. Frescas y listas para consumir.</p>
                            <a href="catalogo.php" class="btn btn-custom">Ver más</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="product-preview-card">
                        <img src="/XATA/img/carousel/xoconostle.jpg" alt="Xoconostles">
                        <div class="card-body">
                            <span class="badge badge-custom mb-2">Fruta Fresca</span>
                            <h4>Xoconostles</h4>
                            <p>Producto tradicional mexicano, ideal para preparaciones culinarias.</p>
                            <a href="catalogo.php" class="btn btn-custom">Ver más</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="product-preview-card">
                        <img src="/XATA/img/carousel/tunades.jpg" alt="Productos">
                        <div class="card-body">
                            <span class="badge badge-custom mb-2">Procesados</span>
                            <h4>Productos Procesados</h4>
                            <p>Mermeladas, salsas, dulces y deshidratados artesanales.</p>
                            <a href="catalogo.php" class="btn btn-custom">Ver más</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-leaf"></i> xät'ä</h5>
                    <p>Asociación de Productores de Tunas y Productos Derivados</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Enlaces</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php">Inicio</a></li>
                        <li class="mb-2"><a href="catalogo.php">Catálogo</a></li>
                        <li class="mb-2"><a href="productores_publico.php">Productores</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Contacto</h5>
                    <p><i class="fas fa-envelope"></i> contacto@xata.com</p>
                    <p><i class="fas fa-phone"></i> (555) 123-4567</p>
                </div>
            </div>
            <hr style="background: rgba(255,255,255,0.2); margin: 30px 0;">
            <div class="text-center">
                <p class="mb-0">&copy; 2025 Asociación xät'ä. Todos los derechos reservados. Hecho por Litzy</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>