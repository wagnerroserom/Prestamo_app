<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="container">
    <h2>Panel de Administrador - PrestamistApp</h2>
    <p>Bienvenido, <strong><?= htmlspecialchars($_SESSION['usuario']) ?></strong></p>

    <h3>Crear Nuevo Préstamo</h3>
    <form method="POST" action="/public/index.php?action=create_loan">
        <select name="cliente_id" required>
            <option value="">Seleccionar Cliente</option>
            <?php foreach ($clientes as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre_completo']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" step="0.01" name="monto" placeholder="Monto a prestar" required>
        <select name="modalidad" required>
            <option value="cuota_fija">Cuota Fija</option>
            <option value="cuota_disminuyente">Cuota Disminuyente</option>
            <option value="interes_fijo">Interés Fijo</option>
            <option value="capital_al_final">Capital al Final</option>
        </select>
        <input type="number" name="cuotas" placeholder="Número de cuotas" min="1" required>
        <input type="number" step="0.01" name="tasa_mensual" placeholder="Interés Mensual (%)" required>
        <input type="date" name="fecha_primer_pago" required value="<?= date('Y-m-d') ?>">
        <button type="submit">CREAR PRÉSTAMO</button>
    </form>

    <br><hr>
    <h3>Historial General de Préstamos</h3>
    <table>
        <tr><th>#</th><th>Cliente</th><th>Monto</th><th>Modalidad</th><th>Fecha</th><th>Acciones</th></tr>
        <?php foreach ($todos_prestamos as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['cliente_nombre']) ?></td>
            <td><?= number_format($p['monto'], 2) ?></td>
            <td><?= ucfirst(str_replace('_', ' ', $p['modalidad'])) ?></td>
            <td><?= $p['created_at'] ?></td>
            <td>
                <a href="/public/index.php?action=edit_loan_form&id=<?= $p['id'] ?>">Editar</a> |
                <a href="/public/index.php?action=delete_loan&id=<?= $p['id'] ?>" 
                    onclick="return confirm('¿Eliminar préstamo #<?= $p['id'] ?>?')">Eliminar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <a href="/public/index.php?action=logout">Cerrar Sesión</a><br>
    <a href="/public/index.php" class="btn-home">Volver al Inicio</a>
</div>
</body>
</html>