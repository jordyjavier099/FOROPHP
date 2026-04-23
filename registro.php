<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/session_check.php';
redirigirSiAutenticado();

$mensaje = '';
$tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = sanitizar($_POST['cedula'] ?? '');
    $nombre = sanitizar($_POST['nombre'] ?? '');
    $correo = strtolower(sanitizar($_POST['correo'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';
    
    // Validaciones
    if (empty($cedula) || empty($nombre) || empty($correo) || empty($password)) {
        $mensaje = 'Todos los campos son obligatorios';
        $tipo = 'error';
    } elseif (!validarCorreo($correo)) {
        $mensaje = 'Formato de correo inválido';
        $tipo = 'error';
    } elseif (!validarPassword($password)) {
        $mensaje = 'La contraseña debe tener al menos 6 caracteres';
        $tipo = 'error';
    } elseif ($password !== $confirmar) {
        $mensaje = 'Las contraseñas no coinciden';
        $tipo = 'error';
    } else {
        try {
            $conn = getConnection();
            
            // Verificar correo o cédula duplicados
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = :correo OR cedula = :cedula");
            $stmt->execute(['correo' => $correo, 'cedula' => $cedula]);
            
            if ($stmt->rowCount() > 0) {
                $mensaje = 'El correo o cédula ya están registrados';
                $tipo = 'error';
            } else {
                // Insertar usuario con contraseña hasheada
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO usuarios (cedula, nombre, correo, password) VALUES (:cedula, :nombre, :correo, :password)");
                $stmt->execute([
                    'cedula' => $cedula,
                    'nombre' => $nombre,
                    'correo' => $correo,
                    'password' => $hash
                ]);
                
                $mensaje = '✅ Registro exitoso. <a href="index.php">Inicia sesión aquí</a>';
                $tipo = 'success';
            }
        } catch (PDOException $e) {
            $mensaje = 'Error en el servidor: ' . $e->getMessage();
            $tipo = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Auth System</title>
    <style>
        /* Mismos estilos que index.php + ajustes */
        body { font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 450px; }
        h2 { text-align: center; color: #333; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; color: #555; }
        input { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 0.75rem; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        button:hover { background: #218838; }
        .mensaje { padding: 0.75rem; margin-bottom: 1rem; border-radius: 4px; }
        .error { background: #f8d7da; color: #721c24; }
        .success { background: #d4edda; color: #155724; }
        .links { text-align: center; margin-top: 1rem; }
        .links a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
<h2><i class="fas fa-user-plus"></i> Registro de Usuario</h2>        
        <?php if ($mensaje): ?>
            <div class="mensaje <?= $tipo ?>"><?= $mensaje ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Cédula</label>
                <input type="text" name="cedula" required maxlength="20" placeholder="1234567890">
            </div>
            <div class="form-group">
                <label>Nombre Completo</label>
                <input type="text" name="nombre" required maxlength="100" placeholder="Juan Pérez">
            </div>
            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="correo" required placeholder="tu@correo.com">
            </div>
            <div class="form-group">
                <label>Contraseña (mín. 6 caracteres)</label>
                <input type="password" name="password" required minlength="6">
            </div>
            <div class="form-group">
                <label>Confirmar Contraseña</label>
                <input type="password" name="confirmar" required minlength="6">
            </div>
            <button type="submit">Registrarse</button>
        </form>
        
        <div class="links">
            <a href="index.php">← Ya tengo cuenta</a>
        </div>
    </div>
</body>
</html>