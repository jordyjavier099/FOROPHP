<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/session_check.php';

// Verificar autenticación
requerirAutenticacion();

// Verificar que sea método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: perfil.php');
    exit;
}

$nombre = sanitizar($_POST['nombre'] ?? '');
$correo = strtolower(sanitizar($_POST['correo'] ?? ''));
$usuario_id = $_SESSION['usuario_id'];

// Validaciones
if (empty($nombre) || empty($correo)) {
    $_SESSION['mensaje'] = 'Nombre y correo son obligatorios';
    $_SESSION['mensaje_tipo'] = 'error';
    header('Location: perfil.php');
    exit;
}

if (!validarCorreo($correo)) {
    $_SESSION['mensaje'] = 'Formato de correo inválido';
    $_SESSION['mensaje_tipo'] = 'error';
    header('Location: perfil.php');
    exit;
}

try {
    $conn = getConnection();
    
    // Verificar que el correo no esté en uso por otro usuario
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = :correo AND id != :id");
    $stmt->execute(['correo' => $correo, 'id' => $usuario_id]);
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['mensaje'] = 'El correo ya está registrado por otro usuario';
        $_SESSION['mensaje_tipo'] = 'error';
        header('Location: perfil.php');
        exit;
    }
    
    // Actualizar datos
    $stmt = $conn->prepare("UPDATE usuarios SET nombre = :nombre, correo = :correo WHERE id = :id");
    $stmt->execute([
        'nombre' => $nombre,
        'correo' => $correo,
        'id' => $usuario_id
    ]);
    
    // Actualizar sesión
    $_SESSION['nombre'] = $nombre;
    $_SESSION['correo'] = $correo;
    
    $_SESSION['mensaje'] = '✅ Perfil actualizado correctamente';
    $_SESSION['mensaje_tipo'] = 'success';
    
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al actualizar: ' . $e->getMessage();
    $_SESSION['mensaje_tipo'] = 'error';
}

header('Location: perfil.php');
exit;
?>