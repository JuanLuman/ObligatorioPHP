<?php
require_once __DIR__ . "/../includes/validar_sesion.php";

if ($_SESSION['tipo_usuario'] !== 'funcionario') {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../clases/Prestamo.php";

// Muestra el detalle completo de un préstamo puntual, recibe el id por GET.
// Solo puede ver el detalle de sus propios préstamos, nunca los de otro funcionario.
$idPrestamo = $_GET['id'] ?? null;
$prestamo = $idPrestamo !== null ? Prestamo::obtenerDetalle($idPrestamo) : false;

if ($prestamo && (string) $prestamo['id_funcionario'] !== (string) $_SESSION['id_usuario']) {
    $prestamo = false;
}
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
