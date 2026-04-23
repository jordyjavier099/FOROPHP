<?php
session_start();

// Limpiar todas las variables de sesión
$_SESSION = [];

// Eliminar cookie de sesión si existe
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destruir la sesión
session_destroy();

// Redirigir al login
header('Location: index.php?mensaje=sesion_cerrada');
exit;
?>