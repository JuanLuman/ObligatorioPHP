<?php

// Inicio la sesión para acceder a los datos del usuario logueado
session_start();


// Verifico que exista una sesión activa y que el usuario sea funcionario
// Si no cumple la condición, lo redirijo al login

//creo el objeto funcionario para validar que el usuario logueado es un funcionario
require_once __DIR__ . '/../conexion/ConexionBD.php';
$conexion = new ConexionBD();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'funcionario') {
    header("Location: login.php?error=No tienes permisos para acceder a esta página");
    exit();
}


<<<<<<< HEAD:solicitarPrestamo.php
// Incluyo la clase encargada de la conexión a la base de datos
require_once "Conexion.php";

// Incluyo las clases necesarias
require_once "Equipo.php";
require_once "Prestamo.php";

=======
>>>>>>> 0870a7b (creacion de carpetas, archivos nuevos, correccion conexion y login):funcionario/solicitarPrestamo.php
// Título de la página
echo "<h2>Solicitud de Préstamo - TechRent</h2>";


<<<<<<< HEAD:solicitarPrestamo.php
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
=======
// Función encargada de mostrar el formulario
function MostrarFormularioPrestamo(){
  
    global $conexion;


    // Abro la conexión con PDO para poder ejecutar consultas
    $conexion->conectarPDO();

    // Verifico que la conexión se haya realizado correctamente
    if ($conexion->conectarPDO() === false) {
        die("Error al conectar a la base de datos");
>>>>>>> 0870a7b (creacion de carpetas, archivos nuevos, correccion conexion y login):funcionario/solicitarPrestamo.php
    }
}

// =====================================================
// Obtengo los equipos disponibles de la sucursal
// =====================================================

$equipos = Equipo::listarDisponibles($_SESSION["id_sucursal"]);

<<<<<<< HEAD:solicitarPrestamo.php
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
=======
    // Comienzo el formulario
    echo "<form action='administrador/procesarPrestamo.php' method='POST'>";
>>>>>>> 0870a7b (creacion de carpetas, archivos nuevos, correccion conexion y login):funcionario/solicitarPrestamo.php

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