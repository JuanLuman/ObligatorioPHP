<?php

// Inicio la sesión para acceder a los datos del usuario logueado
require_once __DIR__ . "/includes/validar_sesion.php";

// Verifico que el usuario sea un funcionario
if ($_SESSION['tipo_usuario'] !== 'funcionario') {
    header("Location: login.php?error=No tienes permisos para acceder a esta página");
    exit();
}

// Incluyo la clase Prestamo
require_once __DIR__ . "/clases/Prestamo.php";


//=======================================================
// Si el usuario presionó el botón Registrar devolución
//=======================================================

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Registro la devolución (devuelve un array de errores, vacío si salió bien)
    $errores = Prestamo::registrarDevolucion($_POST["id_prestamo"]);

    if (empty($errores))
    {
        echo "<p align='center' style='color:green'><b>Devolución registrada correctamente.</b></p>";
    }
    else
    {
        echo "<p align='center' style='color:red'><b>" . implode("<br>", array_map('htmlspecialchars', $errores)) . "</b></p>";
    }
}


//=======================================================
// Obtengo los préstamos activos del funcionario
//=======================================================

$prestamos = Prestamo::obtenerPrestamosActivos($_SESSION["id_usuario"]);


//=======================================================
// Muestro el formulario
//=======================================================

echo "<h2 align='center'>Registrar devolución - TechRent</h2>";

if (count($prestamos) == 0)
{
    echo "<p align='center'>No posee préstamos activos.</p>";
    exit();
}

echo "<table border='1' align='center'>";

echo "<tr>";
echo "<th>Equipo</th>";
echo "<th>Fecha préstamo</th>";
echo "<th>Fecha devolución prevista</th>";
echo "<th>Acción</th>";
echo "</tr>";

foreach ($prestamos as $prestamo)
{
    echo "<tr>";

    echo "<td>";
    echo $prestamo["codigo_inventario"] . " - " .
         $prestamo["marca"] . " " .
         $prestamo["modelo"];
    echo "</td>";

    echo "<td>";
    echo $prestamo["fecha_prestamo"];
    echo "</td>";

    echo "<td>";
    echo $prestamo["fecha_devolucion_prevista"];
    echo "</td>";

    echo "<td>";

    echo "<form method='POST'>";

    echo "<input type='hidden'
                 name='id_prestamo'
                 value='".$prestamo["id_prestamo"]."'>";

    echo "<input type='hidden'
                 name='id_equipo'
                 value='".$prestamo["id_equipo"]."'>";

    echo "<input type='submit'
                 value='Registrar devolución'>";

    echo "</form>";

    echo "</td>";

    echo "</tr>";
}

echo "</table>";

?>