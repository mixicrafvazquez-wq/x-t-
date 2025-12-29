<?php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo_pagina) ? $titulo_pagina . ' - ' : ''; ?>Administración xät'ä</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f6fa;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            color: white;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
        
        .sidebar-header h4 {
            margin: 0;
            font-weight: 600;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            margin: 5px 15px;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sidebar-menu a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
        }
        
        .top-navbar {
            background: white;
            padding: 15px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .content-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .table thead {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
        }
        
        .badge-success {
            background-color: #28a745;
        }
        
        .badge-danger {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-leaf fa-2x mb-2"></i>
            <h4>xät'ä</h4>
            <small>Panel Administrativo</small>
        </div>
        
        <ul class="sidebar-menu">
            <li>
                <a href="dashboard.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'class="active"' : ''; ?>>
                    <i class="fas fa-home"></i>
                    <span>Inicio</span>
                </a>
            </li>
            <li>
                <a href="productos_crud.php" <?php echo (strpos(basename($_SERVER['PHP_SELF']), 'producto') !== false) ? 'class="active"' : ''; ?>>
                    <i class="fas fa-box"></i>
                    <span>Productos</span>
                </a>
            </li>
            <li>
                <a href="productores_crud.php" <?php echo (strpos(basename($_SERVER['PHP_SELF']), 'productor') !== false) ? 'class="active"' : ''; ?>>
                    <i class="fas fa-users"></i>
                    <span>Productores</span>
                </a>
            </li>
            <li>
                <a href="registro_produccion.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'registro_produccion.php') ? 'class="active"' : ''; ?>>
                    <i class="fas fa-clipboard-list"></i>
                    <span>Registrar Producción</span>
                </a>
            </li>
            <li>
                <a href="historial_produccion.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'historial_produccion.php') ? 'class="active"' : ''; ?>>
                    <i class="fas fa-history"></i>
                    <span>Historial de Entradas</span>
                </a>
            </li>
            <?php
            // Verificar si es super admin
            $query_rol = "SELECT rol FROM usuarios_admin WHERE id = ?";
            $stmt_rol = $conn->prepare($query_rol);
            $stmt_rol->bind_param("i", $_SESSION['usuario_id']);
            $stmt_rol->execute();
            $result_rol = $stmt_rol->get_result();
            $es_super_admin = false;
            if ($result_rol->num_rows > 0) {
                $rol_data = $result_rol->fetch_assoc();
                $es_super_admin = ($rol_data['rol'] === 'super_admin');
            }
            ?>
            <?php if ($es_super_admin): ?>
            <li>
                <a href="administradores_crud.php" <?php echo (strpos(basename($_SERVER['PHP_SELF']), 'administrador') !== false) ? 'class="active"' : ''; ?>>
                    <i class="fas fa-user-shield"></i>
                    <span>Administradores</span>
                </a>
            </li>
            <?php endif; ?>
            <li>
                <a href="../index.php" target="_blank">
                    <i class="fas fa-globe"></i>
                    <span>Ver Sitio Público</span>
                </a>
            </li>
            <li>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="main-content">
        <div class="top-navbar">
            <div>
                <h5 class="mb-0"><?php echo isset($titulo_pagina) ? $titulo_pagina : 'Panel de Administración'; ?></h5>
            </div>
            <div>
                <span class="text-muted">
                    <i class="fas fa-user-circle"></i> 
                    <?php echo obtenerNombreUsuario(); ?>
                </span>
            </div>
        </div>
        
        <?php echo mostrarMensaje(); ?>