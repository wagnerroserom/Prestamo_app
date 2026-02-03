<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="container">
    <h2>PrestamistApp</h2>
    <p>Administrador de Préstamos y Cobros</p>

    <?php if (!empty($error)): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="/public/index.php?action=login_process">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="contrasena" placeholder="Contraseña" required>
        <button type="submit">INICIAR SESIÓN</button>
    </form>
    <p>¿No tienes cuenta? <a href="/public/index.php?action=register">REGISTRARME</a></p>
    <a href="/public/index.php" class="btn-home">Volver al Inicio</a>
</div>
</body>
</html>