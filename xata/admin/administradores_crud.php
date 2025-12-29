<?php
/* Solo accesible para super_admin*/
require_once '../includes/conexion.php';
require_once '../includes/sesion.php';

requerirAutenticacion();

// Verificar que sea super admin
$query_rol = "SELECT rol FROM usuarios_admin WHERE id = ?";
$stmt = $conn->prepare($query_rol);
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard.php");
    exit();
}

$rol_data = $result->fetch_assoc();
if ($rol_data['rol'] !== 'super_admin') {
    redirigirConMensaje('dashboard.php', 'No tienes permisos para acceder a esta sección', 'error');
}

$titulo_pagina = 'Gestión de Administradores';

// Obtener todos los administradores
$query = "SELECT * FROM usuarios_admin ORDER BY id DESC";
$resultado = $conn->query($query);

include '../includes/header.php';
?>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">
            <i class="fas fa-user-shield"></i> Gestión de Administradores
        </h1>
        <a href="agregar_administrador.php" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Agregar Administrador
        </a>
    </div>
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Nota:</strong> Solo los Super Administradores pueden gestionar cuentas de administrador.
    </div>
    
    <div class="table-responsive">
        <table id="tablaAdministradores" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Último Acceso</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($admin = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $admin['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($admin['usuario']); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($admin['nombre_completo']); ?></td>
                        <td>
                            <?php if (!empty($admin['email'])): ?>
                                <i class="fas fa-envelope text-muted"></i>
                                <?php echo htmlspecialchars($admin['email']); ?>
                            <?php else: ?>
                                <span class="text-muted">No registrado</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($admin['rol'] === 'super_admin'): ?>
                                <span class="badge bg-danger">
                                    <i class="fas fa-crown"></i> Super Admin
                                </span>
                            <?php else: ?>
                                <span class="badge bg-primary">
                                    <i class="fas fa-user"></i> Admin
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($admin['ultimo_acceso'])): ?>
                                <?php echo date('d/m/Y H:i', strtotime($admin['ultimo_acceso'])); ?>
                            <?php else: ?>
                                <span class="text-muted">Nunca</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($admin['activo'] == 1): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($admin['id'] != $_SESSION['usuario_id']): ?>
                                <a href="editar_administrador.php?id=<?php echo $admin['id']; ?>" 
                                   class="btn btn-sm btn-warning" 
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($admin['rol'] !== 'super_admin'): ?>
                                    <a href="eliminar_administrador.php?id=<?php echo $admin['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('¿Está seguro de eliminar este administrador?');"
                                       title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge bg-info">Tú</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Inicializar DataTable para administradores
    $(document).ready(function() {
        $('#tablaAdministradores').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            pageLength: 10
        });
    });
</script>

<?php include '../includes/footer.php'; ?>