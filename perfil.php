<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/session_check.php';
requerirAutenticacion();

// Actualizar última actividad
$_SESSION['ultima_actividad'] = time();

// Obtener datos actualizados desde BD
$conn = getConnection();
$stmt = $conn->prepare("SELECT nombre, correo, fecha_registro FROM usuarios WHERE id = :id");
$stmt->execute(['id' => $_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$mensaje = '';
$tipo = '';

// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_perfil'])) {
    $nuevoNombre = sanitizar($_POST['nombre'] ?? '');
    $nuevoCorreo = strtolower(sanitizar($_POST['correo'] ?? ''));
    
    if (empty($nuevoNombre) || !validarCorreo($nuevoCorreo)) {
        $mensaje = 'Datos inválidos';
        $tipo = 'error';
    } else {
        try {
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = :correo AND id != :id");
            $stmt->execute(['correo' => $nuevoCorreo, 'id' => $_SESSION['usuario_id']]);
            
            if ($stmt->rowCount() > 0) {
                $mensaje = 'El correo ya está registrado';
                $tipo = 'error';
            } else {
                $stmt = $conn->prepare("UPDATE usuarios SET nombre = :nombre, correo = :correo WHERE id = :id");
                $stmt->execute([
                    'nombre' => $nuevoNombre,
                    'correo' => $nuevoCorreo,
                    'id' => $_SESSION['usuario_id']
                ]);
                
                $_SESSION['nombre'] = $nuevoNombre;
                $_SESSION['correo'] = $nuevoCorreo;
                
                $mensaje = '<i class="fas fa-check-circle"></i> Perfil actualizado correctamente';
                $tipo = 'success';
                $usuario['nombre'] = $nuevoNombre;
                $usuario['correo'] = $nuevoCorreo;
            }
        } catch (PDOException $e) {
            $mensaje = 'Error al actualizar';
            $tipo = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - Auth System</title>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid #eee; }
        .header h2 { margin: 0; }
        .header h2 i { margin-right: 0.5rem; color: #007bff; }
        .btn { padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 0.9rem; display: inline-block; margin: 0.25rem; }
        .btn i { margin-right: 0.5rem; }
        .btn-primary { background: #007bff; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: #212529; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; color: #555; font-weight: bold; }
        label i { margin-right: 0.5rem; color: #007bff; }
        input { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        input[readonly] { background: #f8f9fa; cursor: not-allowed; }
        input:not([readonly]) { background: #fff; border-color: #007bff; }
        .mensaje { padding: 0.75rem; margin-bottom: 1rem; border-radius: 4px; }
        .error { background: #f8d7da; color: #721c24; }
        .success { background: #d4edda; color: #155724; }
        .info-box { background: #e7f3ff; padding: 1rem; border-radius: 4px; margin: 1rem 0; border-left: 4px solid #007bff; }
        .info-box i { margin-right: 0.5rem; }
        .actions { display: flex; gap: 1rem; margin-top: 1.5rem; flex-wrap: wrap; }
    </style>
</head>
<body>
    <div class="container" id="perfilContainer">
        <div class="header">
            <h2><i class="fas fa-user-circle"></i> Mi Perfil</h2>
            <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>

        <?php if ($mensaje): ?>
            <div class="mensaje <?= $tipo ?>">
                <?php if ($tipo === 'success'): ?>
                    <i class="fas fa-check-circle"></i>
                <?php else: ?>
                    <i class="fas fa-exclamation-circle"></i>
                <?php endif; ?>
                <?= $mensaje ?>
            </div>
        <?php endif; ?>

        <div class="info-box">
            <i class="fas fa-calendar-alt"></i>
            <strong>Miembro desde:</strong> <?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?>
        </div>

        <form method="POST" id="formPerfil">
            <div class="form-group">
                <label><i class="fas fa-id-card"></i> Cédula</label>
                <input type="text" value="<?= htmlspecialchars($_SESSION['cedula']) ?>" readonly disabled>
            </div>
            <div class="form-group">
                <label><i class="fas fa-user"></i> Nombre</label>
                <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" readonly>
            </div>
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Correo Electrónico</label>
                <input type="email" name="correo" id="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" readonly>
            </div>
            <div class="actions">
                <button type="button" class="btn btn-warning btn-edit" onclick="habilitarEdicion()">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button type="submit" name="actualizar_perfil" class="btn btn-success btn-save" style="display: none;">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <button type="button" class="btn btn-secondary btn-cancel" style="display: none;" onclick="cancelarEdicion()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <a href="cambiar_password.php" class="btn btn-primary">
                    <i class="fas fa-key"></i> Cambiar Contraseña
                </a>
            </div>
        </form>
    </div>

    <script>
        function habilitarEdicion() {
            document.getElementById('nombre').removeAttribute('readonly');
            document.getElementById('correo').removeAttribute('readonly');
            document.getElementById('nombre').dataset.original = document.getElementById('nombre').value;
            document.getElementById('correo').dataset.original = document.getElementById('correo').value;
            document.querySelector('.btn-edit').style.display = 'none';
            document.querySelector('.btn-save').style.display = 'inline-block';
            document.querySelector('.btn-cancel').style.display = 'inline-block';
            document.getElementById('nombre').focus();
        }

        function cancelarEdicion() {
            document.getElementById('nombre').value = document.getElementById('nombre').dataset.original;
            document.getElementById('correo').value = document.getElementById('correo').dataset.original;
            document.getElementById('nombre').setAttribute('readonly', 'readonly');
            document.getElementById('correo').setAttribute('readonly', 'readonly');
            document.querySelector('.btn-edit').style.display = 'inline-block';
            document.querySelector('.btn-save').style.display = 'none';
            document.querySelector('.btn-cancel').style.display = 'none';
        }

        <?php if ($tipo === 'success'): ?>
            document.getElementById('nombre').setAttribute('readonly', 'readonly');
            document.getElementById('correo').setAttribute('readonly', 'readonly');
            document.querySelector('.btn-edit').style.display = 'inline-block';
            document.querySelector('.btn-save').style.display = 'none';
            document.querySelector('.btn-cancel').style.display = 'none';
        <?php endif; ?>
    </script>
</body>
</html>