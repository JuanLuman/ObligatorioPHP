<?php
require_once __DIR__ . "/../includes/validar_sesion.php";

if ($_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../clases/Prestamo.php";

// Historial completo: todos los prestamos de todos los funcionarios,
// ordenados por fecha de devolucion prevista descendente
$prestamos = Prestamo::obtenerHistorialCompleto();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de préstamos - TechRent</title>
</head>
<body>

    <h2 align="center">Historial de préstamos - TechRent</h2>

    <?php if (empty($prestamos)): ?>
        <p align="center">No hay préstamos registrados.</p>
    <?php else: ?>
        <table border="1" align="center">
            <tr>
                <th>Funcionario</th>
                <th>Equipo</th>
                <th>Fecha préstamo</th>
                <th>Fecha devolución prevista</th>
                <th>Fecha devolución real</th>
                <th>Estado</th>
                <th>Detalle</th>
            </tr>
            <?php foreach ($prestamos as $prestamo): ?>
                <tr>
                    <td><?php echo htmlspecialchars($prestamo['primer_nombre'] . ' ' . $prestamo['primer_apellido']); ?></td>
                    <td><?php echo htmlspecialchars($prestamo['codigo_inventario'] . ' - ' . $prestamo['marca'] . ' ' . $prestamo['modelo']); ?></td>
                    <td><?php echo htmlspecialchars($prestamo['fecha_prestamo']); ?></td>
                    <td><?php echo htmlspecialchars($prestamo['fecha_devolucion_prevista']); ?></td>
                    <td><?php echo htmlspecialchars($prestamo['fecha_devolucion_real'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($prestamo['estado']); ?></td>
                    <td><a href="detalle_prestamo.php?id=<?php echo $prestamo['id_prestamo']; ?>">Ver detalle</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p align="center"><a href="inicio.php">Volver</a> | <a href="../logout.php">Salir</a></p>

</body>
</html>
