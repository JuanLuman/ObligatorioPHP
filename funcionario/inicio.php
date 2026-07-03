<?php
require_once "../includes/validar_sesion.php";

// Si por alguna razón un administrador entra directo a esta URL, lo sacamos
if ($_SESSION['tipo_usuario'] !== 'funcionario') {
    header("Location: ../login.php");
    exit;
}

require_once "../clases/Usuario.php";
require_once "../clases/Prestamo.php";

$usuario = Usuario::cargar($_SESSION['ci']);
$prestamosActivos = $usuario->obtenerPrestamosActivos();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Funcionario</title>
</head>
<body>

    <h2>Bienvenido, <?php echo htmlspecialchars($usuario->getPrimerNombre()); ?></h2>
    <p>Tiempo de inactividad restante: <?php echo gmdate("H:i:s", $tiempoRestante); ?></p>

    <nav>
        <ul>
            <?php foreach ($usuario->obtenerMenu() as $opcion): ?>
                <li><?php echo htmlspecialchars($opcion); ?></li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <?php if (empty($prestamosActivos)): ?>
        <p>Usted no posee préstamos activos.</p>
    <?php else: ?>
        <fieldset>
            <legend align="center">Préstamos activos</legend>
            <table border="1" align="center">
                <tr>
                    <th>Imagen</th>
                    <th>Código de inventario</th>
                    <th>Marca y modelo</th>
                    <th>Fecha préstamo</th>
                    <th>Fecha devolución prevista</th>
                    <th>Estado</th>
                </tr>
                <?php foreach ($prestamosActivos as $prestamo): ?>
                    <tr>
                        <td><img src="../fotos_equipos/<?php echo htmlspecialchars($prestamo['foto']); ?>" alt="Imagen del equipo" width="100"></td>
                        <td><?php echo htmlspecialchars($prestamo['codigo_inventario']); ?></td>
                        <td><?php echo htmlspecialchars($prestamo['marca'] . ' ' . $prestamo['modelo']); ?></td>
                        <td><?php echo htmlspecialchars($prestamo['fecha_prestamo']); ?></td>
                        <td><?php echo htmlspecialchars($prestamo['fecha_devolucion_prevista']); ?></td>
                        <td><?php echo htmlspecialchars($prestamo['estado']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </fieldset>
    <?php endif; ?>

    <p><a href="../logout.php">Salir</a></p>

</body>
</html>