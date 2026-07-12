<?php

// Inicio la sesión para acceder a los datos del usuario logueado
require_once __DIR__ . "/../includes/validar_sesion.php";

// Verifico que el usuario sea funcionario
if ($_SESSION['tipo_usuario'] !== 'funcionario') {
    header("Location: ../login.php?error=No tienes permisos para acceder a esta página");
    exit();
}

// Incluyo las clases necesarias
require_once __DIR__ . '/../clases/Equipo.php';
require_once __DIR__ . '/../clases/Prestamo.php';

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

    // Registro el préstamo (devuelve un array de errores, vacío si salió bien)
    $errores = $prestamo->procesarPrestamo();

    if (empty($errores)) {
        echo "<p align='center' style='color:green'><b>Préstamo registrado correctamente.</b></p>";
    } else {
        echo "<p align='center' style='color:red'><b>" . implode("<br>", array_map('htmlspecialchars', $errores)) . "</b></p>";
    }
}


// =====================================================
// Obtengo los equipos disponibles de la sucursal
// =====================================================

$equipos = (new Equipo())->listarDisponibles($_SESSION["id_sucursal"]);


// =====================================================
// Muestro el formulario
// =====================================================

MostrarFormularioPrestamo($equipos);


//=======================================================
// Función que muestra el formulario
//=======================================================

function MostrarFormularioPrestamo($equipos)
{
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
        echo htmlspecialchars($descripcion);
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
