<?php
// Validar formato de correo electrónico
function validarCorreo($correo) {
    return filter_var($correo, FILTER_VALIDATE_EMAIL);
}

// Sanitizar entradas de usuario
function sanitizar($dato) {
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}

// Verificar fortaleza de contraseña (mínimo 6 caracteres)
function validarPassword($password) {
    return strlen($password) >= 6;
}

// Generar mensaje de respuesta JSON
function responderJSON($exito, $mensaje, $datos = []) {
    header('Content-Type: application/json');
    echo json_encode(array_merge(['exito' => $exito, 'mensaje' => $mensaje], $datos));
    exit;
}
?>