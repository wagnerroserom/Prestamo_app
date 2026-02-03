<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Préstamo</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="container">
    <h2>Editar Préstamo #<?= $prestamo['id'] ?></h2>
    <form method="POST" action="/public/index.php?action=update_loan">
        <input type="hidden" name="id" value="<?= $prestamo['id'] ?>">
        <select name="cliente_id" required>
            <?php foreach ($clientes as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($c['id'] == $prestamo['cliente_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nombre_completo']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="number" step="0.01" name="monto" value="<?= $prestamo['monto'] ?>" required>
        <select name="modalidad" required>
            <option value="cuota_fija" <?= ($prestamo['modalidad'] === 'cuota_fija') ? 'selected' : '' ?>>Cuota Fija</option>
            <option value="cuota_disminuyente" <?= ($prestamo['modalidad'] === 'cuota_disminuyente') ? 'selected' : '' ?>>Cuota Disminuyente</option>
            <option value="interes_fijo" <?= ($prestamo['modalidad'] === 'interes_fijo') ? 'selected' : '' ?>>Interés Fijo</option>
            <option value="capital_al_final" <?= ($prestamo['modalidad'] === 'capital_al_final') ? 'selected' : '' ?>>Capital al Final</option>
        </select>
        <input type="number" name="cuotas" value="<?= $prestamo['cuotas'] ?>" min="1" required>
        <input type="number" step="0.01" name="tasa_mensual" value="<?= $prestamo['tasa_mensual'] ?>" required>
        <input type="date" name="fecha_primer_pago" value="<?= $prestamo['fecha_primer_pago'] ?>" required>
        <button type="submit">ACTUALIZAR PRÉSTAMO</button>
        <a href="/public/index.php?action=admin_panel">Cancelar</a>
    </form>
    <a href="/public/index.php" class="btn-home">Volver al Inicio</a>
</div>
</body>
</html>