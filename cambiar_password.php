<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/session_check.php';
requerirAutenticacion();

$mensaje = '';
$tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actual = $_POST['password_actual'] ?? '';
    $nueva = $_POST['password_nueva'] ?? '';
    $confirmar = $_POST['password_confirmar'] ?? '';
    
    if (empty($actual) || empty($nueva) || empty($confirmar)) {
        $mensaje = 'Todos los campos son obligatorios';
        $tipo = 'error';
    } elseif (!validarPassword($nueva)) {
        $mensaje = 'La nueva contraseña debe tener al menos 6 caracteres';
        $tipo = 'error';
    } elseif ($nueva !== $confirmar) {
        $mensaje = 'Las nuevas contraseñas no coinciden';
        $tipo = 'error';
    } else {
        try {
            $conn = getConnection();
            
            // Obtener hash actual
            $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = :id");
            $stmt->execute(['id' => $_SESSION['usuario_id']]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar contraseña actual
            if (!password_verify($actual, $usuario['password'])) {
                $mensaje = 'La contraseña actual es incorrecta';
                $tipo = 'error';
            } else {
                // Actualizar con nuevo hash
                $nuevoHash = password_hash($nueva, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE usuarios SET password = :password WHERE id = :id");
                $stmt->execute([
                    'password' => $nuevoHash,
                    'id' => $_SESSION['usuario_id']
                ]);
                
                $mensaje = '✅ Contraseña actualizada correctamente';
                $tipo = 'success';
            }
        } catch (PDOException $e) {
            $mensaje = 'Error en el servidor';
            $tipo = 'error';
            error_log("Error password: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña - Auth System</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 450px; }
        h2 { text-align: center; color: #333; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; color: #555; }
        input { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 0.75rem; background: #6f42c1; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; margin-top: 0.5rem; }
        button:hover { background: #5a32a3; }
        .mensaje { padding: 0.75rem; margin-bottom: 1rem; border-radius: 4px; }
        .error { background: #f8d7da; color: #721c24; }
        .success { background: #d4edda; color: #155724; }
        .btn-back { display: block; text-align: center; margin-top: 1rem; color: #007bff; text-decoration: none; }
        .password-hint { font-size: 0.85rem; color: #666; margin-top: 0.25rem; }
    </style>
</head>
<body>
    <div class="container">
<h2><i class="fas fa-key"></i> Cambiar Contraseña</h2>        
        <?php if ($mensaje): ?>
            <div class="mensaje <?= $tipo ?>"><?= $mensaje ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Contraseña Actual</label>
                <input type="password" name="password_actual" required placeholder="••••••••">
            </div>
            <div class="form-group">
                <label>Nueva Contraseña</label>
                <input type="password" name="password_nueva" required minlength="6" placeholder="••••••••">
                <div class="password-hint">Mínimo 6 caracteres</div>
            </div>
            <div class="form-group">
                <label>Confirmar Nueva Contraseña</label>
                <input type="password" name="password_confirmar" required minlength="6" placeholder="••••••••">
            </div>
            <button type="submit">Actualizar Contraseña</button>
        </form>
        
        <a href="perfil.php" class="btn-back">← Volver al perfil</a>
    </div>
</body>
</html>