<?php
require_once 'includes/session_check.php';
redirigirSiAutenticado();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Auth System</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #333; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; color: #555; }
        input { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 0.75rem; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        button:hover { background: #0056b3; }
        .mensaje { padding: 0.75rem; margin-bottom: 1rem; border-radius: 4px; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .links { text-align: center; margin-top: 1rem; }
        .links a { color: #007bff; text-decoration: none; margin: 0 0.5rem; }
    </style>
</head>
<body>
    <div class="container">
       <h2><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</h2>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="mensaje error">
                <?php
                $errores = [
                    'credenciales' => 'Correo o contraseña incorrectos',
                    'sesion_requerida' => 'Debes iniciar sesión para acceder',
                    'registro_exitoso' => 'Registro exitoso. Ahora puedes iniciar sesión'
                ];
                echo $errores[$_GET['error']] ?? 'Ocurrió un error';
                ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" required placeholder="tu@correo.com">
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit">Ingresar</button>
        </form>
        
        <div class="links">
<a href="registro.php"><i class="fas fa-user-plus"></i> Crear cuenta</a>
        </div>
    </div>
</body>
</html>