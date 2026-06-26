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


// Título de la página
echo "<h2>Solicitud de Préstamo - TechRent</h2>";


// Función encargada de mostrar el formulario
function MostrarFormularioPrestamo()
{
    // Creo un objeto conexión
    $conexion = new ConexionBD();

    // Abro la conexión con MySQL
    $conexion->conectar();

    // Verifico que la conexión se haya realizado correctamente
    if ($conexion->conectar() === false) {
        die("Error al conectar a la base de datos");
    }

    // Consulta para obtener únicamente los equipos disponibles
    // Los equipos prestados, en mantenimiento o dados de baja no aparecerán
    $consulta = "
        SELECT id_equipo,
               codigo_inventario,
               marca,
               modelo
        FROM equipos
        WHERE estado = 'Disponible'
        ORDER BY marca, modelo
    ";

    // Ejecuto la consulta
    $resultado = $conexion->ejecutarConsulta($consulta);

    // Comienzo el formulario
    echo "<form action='procesarPrestamo.php' method='POST'>";

    // Agrupo visualmente los controles
    echo "<fieldset>";
    echo "<legend>Datos del préstamo</legend>";

    echo "<table align='center'>";

    // ---------------- EQUIPO ----------------

    echo "<tr>";
    echo "<td>Equipo:</td>";
    echo "<td>";

    // Combo con los equipos disponibles
    echo "<select name='id_equipo' required>";

    echo "<option value=''>Seleccione un equipo</option>";

    // Recorro todos los equipos obtenidos desde la base
    while ($fila = mysqli_fetch_array($resultado, MYSQLI_ASSOC))
    {
        // Texto descriptivo que verá el usuario
        $descripcion =
            $fila['codigo_inventario'] . " - " .
            $fila['marca'] . " " .
            $fila['modelo'];

        // Creo una opción del combo
        echo "<option value='" . $fila['id_equipo'] . "'>";
        echo $descripcion;
        echo "</option>";
    }

    echo "</select>";
    echo "</td>";
    echo "</tr>";

    // ---------------- FECHA PRÉSTAMO ----------------

    echo "<tr>";
    echo "<td>Fecha préstamo:</td>";
    echo "<td>";
    echo "<input type='date' name='fecha_prestamo' required>";
    echo "</td>";
    echo "</tr>";

    // ---------------- FECHA DEVOLUCIÓN ----------------

    echo "<tr>";
    echo "<td>Fecha devolución prevista:</td>";
    echo "<td>";
    echo "<input type='date' name='fecha_devolucion_prevista' required>";
    echo "</td>";
    echo "</tr>";

    // ---------------- OBSERVACIONES ----------------

    echo "<tr>";
    echo "<td>Observaciones:</td>";
    echo "<td>";
    echo "<textarea name='observaciones' rows='4' cols='40'></textarea>";
    echo "</td>";
    echo "</tr>";

    // ---------------- BOTÓN ENVIAR ----------------

    echo "<tr>";
    echo "<td colspan='2' align='center'>";

    // Al presionar este botón se enviarán los datos al archivo procesarPrestamo.php
    echo "<input type='submit' value='Solicitar Préstamo'>";

    echo "</td>";
    echo "</tr>";

    echo "</table>";

    echo "</fieldset>";

    echo "</form>";

    // Cierro la conexión a la base de datos
    $conexion->cerrarConexion();
}


// Llamo a la función para mostrar el formulario
MostrarFormularioPrestamo();

?>