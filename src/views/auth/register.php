<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Usuario</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="container">
    <h2>Registrar Usuario</h2>
    <p>Por favor complete el formulario con información veraz</p>
    <form method="POST" action="/public/index.php?action=register">
        <input type="text" name="nombre" placeholder="Nombres / Apellidos" required>
        <input type="email" name="correo" placeholder="Correo Electrónico" required>
        <select name="pais" required>
            <option value="">Selecciona País</option>
            <option value="Ecuador">Ecuador</option>
            <option value="Colombia">Colombia</option>
            <option value="Perú">Perú</option>
            <option value="Chile">Chile</option>
            <option value="Estados Unidos">Estados Unidos</option>
        </select>
        <input type="text" name="celular" placeholder="Celular / WhatsApp" required>
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="contrasena" placeholder="Contraseña" required>
        <button type="submit">SOLICITAR PRUEBA</button>
        <p>¿Ya tienes cuenta? <a href="/public/index.php?action=login">INICIAR SESIÓN</a></p>
    </form>
    <a href="/public/index.php" class="btn-home">Volver al Inicio</a>
</div>
</body>
</html>