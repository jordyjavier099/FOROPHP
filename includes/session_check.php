<?php
session_start();

// Redirigir si no hay sesión activa
function requerirAutenticacion() {
    if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['correo'])) {
        header('Location: index.php?error=sesion_requerida');
        exit;
    }
}

// Redirigir si YA hay sesión (para login/registro)
function redirigirSiAutenticado() {
    if (isset($_SESSION['usuario_id'])) {
        header('Location: perfil.php');
        exit;
    }
}
?>