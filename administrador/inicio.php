<?php
require_once "../includes/validar_sesion.php";

if ($_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: ../login.php");
    exit;
}

require_once "../clases/Usuario.php";
require_once "../clases/Equipo.php"; // TODO: falta escribir esta clase con PDO

$usuario = Usuario->cargar($_SESSION['id_usuario']);

// Cada uno de estos trae la lista de equipos filtrados por estado.
// Igual que en Prestamo, deberían ser estáticos, PDO con prepared statements,
// y devolver arrays asociativos con foto, codigo_inventario, marca, modelo.
$equiposPrestados     = Equipo->obtenerPorEstado('Prestado');
$equiposVencidos      = Equipo->obtenerVencidos(); // requiere JOIN con prestamos
$equiposMantenimiento = Equipo->obtenerPorEstado('Mantenimiento');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador</title>
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

    <?php
    // Reutilizamos la misma tabla para las 3 secciones, evitando triplicar el HTML
    function mostrarTablaEquipos($titulo, $equipos) {
        echo "<fieldset><legend align='center'>" . htmlspecialchars($titulo) . "</legend>";
        if (empty($equipos)) {
            echo "<p>No hay equipos en esta categoría.</p>";
        } else {
            echo "<table border='1' align='center'>";
            echo "<tr><th>Imagen</th><th>Código de inventario</th><th>Marca y modelo</th></tr>";
            foreach ($equipos as $equipo) {
                echo "<tr>";
                echo "<td><img src='../fotos_equipos/" . htmlspecialchars($equipo['foto']) . "' width='100'></td>";
                echo "<td>" . htmlspecialchars($equipo['codigo_inventario']) . "</td>";
                echo "<td>" . htmlspecialchars($equipo['marca'] . ' ' . $equipo['modelo']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        echo "</fieldset>";
    }

    mostrarTablaEquipos("Equipos prestados", $equiposPrestados);
    mostrarTablaEquipos("Equipos vencidos", $equiposVencidos);
    mostrarTablaEquipos("Equipos en mantenimiento", $equiposMantenimiento);
    ?>

    <p><a href="../logout.php">Salir</a></p>

</body>
</html>