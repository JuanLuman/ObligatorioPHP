<?php

// Inicio la sesión para acceder a los datos del usuario logueado
session_start();


// Verifico que exista una sesión activa y que el usuario sea funcionario
// Si no cumple la condición, lo redirijo al login
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'funcionario') {
    header("Location: login.html?error=No tienes permisos para acceder a esta página");
    exit();
}


// Incluyo la clase encargada de la conexión a la base de datos
require_once "Conexion.php";

// Incluyo las clases necesarias
require_once "Equipo.php";
require_once "Prestamo.php";

// Título de la página
echo "<h2>Solicitud de Préstamo - TechRent</h2>";


// =====================================================
// Si el usuario envió el formulario, registro el préstamo
// =====================================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Creo el objeto préstamo
    $prestamo = new Prestamo(
        $_POST["id_equipo"],
        $_SESSION["id_usuario"],
        $_POST["fecha_prestamo"],
        $_POST["fecha_devolucion_prevista"],
        $_POST["observaciones"]
    );

    // Registro el préstamo
    $resultado = $prestamo->registrarPrestamo();

    // Muestro el resultado
    if ($resultado === true) {
        echo "<p align='center' style='color:green'><b>Préstamo registrado correctamente.</b></p>";
    } else {
        echo "<p align='center' style='color:red'><b>$resultado</b></p>";
    }
}

// =====================================================
// Obtengo los equipos disponibles de la sucursal
// =====================================================

$equipos = Equipo::listarDisponibles($_SESSION["id_sucursal"]);

// =====================================================
// Muestro el formulario
// =====================================================

MostrarFormularioPrestamo($equipos);



//=======================================================
// Función que muestra el formulario
//=======================================================

function MostrarFormularioPrestamo($equipos)
{
    echo "<h2 align='center'>Solicitud de Préstamo - TechRent</h2>";

    echo "<form method='POST'>";

    echo "<fieldset>";
    echo "<legend align='center'>Datos del préstamo</legend>";

    echo "<table align='center'>";

    //===================================================
    // Equipo
    //===================================================

    echo "<tr>";
    echo "<td>Equipo:</td>";
    echo "<td>";

    echo "<select name='id_equipo' required>";
    echo "<option value=''>Seleccione un equipo</option>";

    foreach ($equipos as $equipo) {

        $descripcion =
            $equipo->getCodigoInventario() .
            " - " .
            $equipo->getMarca() .
            " " .
            $equipo->getModelo();

        echo "<option value='" . $equipo->getIdEquipo() . "'>";
        echo $descripcion;
        echo "</option>";
    }

    echo "</select>";

    echo "</td>";
    echo "</tr>";

    //===================================================
    // Fecha préstamo
    //===================================================

    echo "<tr>";
    echo "<td>Fecha préstamo:</td>";
    echo "<td>";
    echo "<input type='date' name='fecha_prestamo' required>";
    echo "</td>";
    echo "</tr>";

    //===================================================
    // Fecha devolución prevista
    //===================================================

    echo "<tr>";
    echo "<td>Fecha devolución prevista:</td>";
    echo "<td>";
    echo "<input type='date' name='fecha_devolucion_prevista' required>";
    echo "</td>";
    echo "</tr>";

    //===================================================
    // Observaciones
    //===================================================

    echo "<tr>";
    echo "<td>Observaciones:</td>";
    echo "<td>";
    echo "<textarea name='observaciones' rows='4' cols='40'></textarea>";
    echo "</td>";
    echo "</tr>";

    //===================================================
    // Botón
    //===================================================

    echo "<tr>";
    echo "<td colspan='2' align='center'>";
    echo "<input type='submit' value='Solicitar Préstamo'>";
    echo "</td>";
    echo "</tr>";

    echo "</table>";

    echo "</fieldset>";

    echo "</form>";
}
?>