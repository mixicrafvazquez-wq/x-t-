<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'xata');
define('DB_CHARSET', 'utf8mb4');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

if (!$conn->set_charset(DB_CHARSET)) {
    die("Error al establecer el conjunto de caracteres: " . $conn->error);
}

date_default_timezone_set('America/Mexico_City');

function ejecutarConsulta($conn, $query, $types = '', $params = []) {
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        error_log("Error en preparación de consulta: " . $conn->error);
        return false;
    }
    
    if (!empty($types) && !empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        error_log("Error en ejecución de consulta: " . $stmt->error);
        return false;
    }
    
    $result = $stmt->get_result();
    $stmt->close();
    
    return $result;
}

function limpiarDatos($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function redirigirConMensaje($url, $mensaje, $tipo = 'info') {
    session_start();
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['tipo_mensaje'] = $tipo;
    header("Location: $url");
    exit();
}

function mostrarMensaje() {
    if (isset($_SESSION['mensaje'])) {
        $tipo = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : 'info';
        $clase = '';
        
        switch ($tipo) {
            case 'success':
                $clase = 'alert-success';
                break;
            case 'error':
                $clase = 'alert-danger';
                break;
            case 'warning':
                $clase = 'alert-warning';
                break;
            default:
                $clase = 'alert-info';
        }
        
        $mensaje = $_SESSION['mensaje'];
        unset($_SESSION['mensaje']);
        unset($_SESSION['tipo_mensaje']);
        
        return "<div class='alert $clase alert-dismissible fade show' role='alert'>
                    $mensaje
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                </div>";
    }
    return '';
}
?>