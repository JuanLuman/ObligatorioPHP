<?php
require_once __DIR__ . "/../includes/validar_sesion.php";

if ($_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../clases/Prestamo.php";

// El administrador puede ver el detalle de cualquier prestamo
$idPrestamo = $_GET['id'] ?? null;
$prestamo = $idPrestamo !== null ? Prestamo::obtenerDetalle($idPrestamo) : false;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de préstamo - TechRent</title>
</head>
<body>

    <h2 align="center">Detalle de préstamo - TechRent</h2>

    <?php if (!$prestamo): ?>
        <p align="center">El préstamo solicitado no existe.</p>
    <?php else: ?>
        <fieldset>
            <legend align="center">Préstamo #<?php echo htmlspecialchars($prestamo['id_prestamo']); ?></legend>
            <table align="center">
                <tr><td>Funcionario:</td><td><?php echo htmlspecialchars($prestamo['primer_nombre'] . ' ' . $prestamo['primer_apellido']); ?></td></tr>
                <tr><td>Equipo:</td><td><?php echo htmlspecialchars($prestamo['codigo_inventario'] . ' - ' . $prestamo['marca'] . ' ' . $prestamo['modelo']); ?></td></tr>
                <tr><td>Imagen:</td><td><img src="../fotos/<?php echo htmlspecialchars($prestamo['foto'] ?? ''); ?>" width="150"></td></tr>
                <tr><td>Fecha préstamo:</td><td><?php echo htmlspecialchars($prestamo['fecha_prestamo']); ?></td></tr>
                <tr><td>Fecha devolución prevista:</td><td><?php echo htmlspecialchars($prestamo['fecha_devolucion_prevista']); ?></td></tr>
                <tr><td>Fecha devolución real:</td><td><?php echo htmlspecialchars($prestamo['fecha_devolucion_real'] ?? 'Aún no devuelto'); ?></td></tr>
                <tr><td>Estado:</td><td><?php echo htmlspecialchars($prestamo['estado']); ?></td></tr>
                <tr><td>Observaciones:</td><td><?php echo nl2br(htmlspecialchars($prestamo['observaciones'] ?? '')); ?></td></tr>
            </table>
        </fieldset>
    <?php endif; ?>

    <p align="center"><a href="historial.php">Volver al historial</a> | <a href="../logout.php">Salir</a></p>

</body>
</html>
