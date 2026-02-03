<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Préstamos</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="container">
    <h2>Mis Préstamos</h2>
    <?php if (empty($prestamos)): ?>
        <p>No tienes préstamos registrados.</p>
    <?php else: ?>
        <?php foreach ($prestamos as $p): ?>
            <h3>Préstamo #<?= $p['id'] ?> - <?= ucfirst(str_replace('_', ' ', $p['modalidad'])) ?></h3>
            <p><strong>Monto:</strong> <?= number_format($p['monto'], 2) ?> | 
                <strong>Cuotas:</strong> <?= $p['cuotas'] ?> | 
                <strong>Fecha inicio:</strong> <?= $p['fecha_primer_pago'] ?></p>
        <?php endforeach; ?>
    <?php endif; ?>
    <br>
    <a href="/public/index.php?action=logout">Cerrar Sesión</a><br>
    <a href="/public/index.php" class="btn-home">Volver al Inicio</a>
</div>
</body>
</html>