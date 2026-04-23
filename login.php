<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$correo = strtolower(sanitizar($_POST['correo'] ?? ''));
$password = $_POST['password'] ?? '';

if (empty($correo) || empty($password)) {
    header('Location: index.php?error=credenciales');
    exit;
}

try {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT id, cedula, nombre, correo, password FROM usuarios WHERE correo = :correo");
    $stmt->execute(['correo' => $correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario && password_verify($password, $usuario['password'])) {
        // Regenerar ID de sesión para prevenir fijación
        session_regenerate_id(true);
        
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['cedula'] = $usuario['cedula'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['correo'] = $usuario['correo'];
        $_SESSION['ultima_actividad'] = time();
        
        header('Location: perfil.php');
        exit;
    } else {
        header('Location: index.php?error=credenciales');
        exit;
    }
} catch (PDOException $e) {
    error_log("Error login: " . $e->getMessage());
    header('Location: index.php?error=servidor');
    exit;
}
?>