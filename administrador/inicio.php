<?php
require_once "../includes/validar_sesion.php";

if ($_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: ../login.php");
    exit;
}

require_once "../clases/Usuario.php";
require_once "../clases/Equipo.php";

$usuario = new Usuario();
$usuario->cargar($_SESSION['id_usuario']);

$equipo = new Equipo();
$equiposPrestados     = $equipo->obtenerPorEstado(Equipo::ESTADO_PRESTADO);
$equiposVencidos      = $equipo->obtenerVencidos();
$equiposMantenimiento = $equipo->obtenerPorEstado(Equipo::ESTADO_MANTENIMIENTO);
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
            <li><a href="alta_equipo.php">Alta de equipo</a></li>
            <li><a href="alta_sucursal.php">Alta de sucursal</a></li>
            <li><a href="alta_usuario.php">Alta de usuario</a></li>
            <li><a href="historial.php">Historial de préstamos</a></li>
            <li><a href="reportes.php">Reportes</a></li>
            <li><a href="perfil.php">Mi perfil</a></li>
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
                echo "<td><img src='../fotos/" . htmlspecialchars($equipo['foto']) . "' width='100'></td>";
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