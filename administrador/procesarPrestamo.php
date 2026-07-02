<?php

// Inicio la sesión para obtener los datos del usuario logueado
session_start();


// Incluyo la clase de conexión
require_once "Conexion.php";


// Recupero los datos enviados desde el formulario
$idEquipo = $_POST['id_equipo'];


// Obtengo el funcionario desde la sesión
// Esto evita que el usuario pueda modificar el ID manualmente
$idFuncionario = $_SESSION['id_usuario'];


// Recupero las fechas ingresadas
$fechaPrestamo = $_POST['fecha_prestamo'];
$fechaDevolucionPrevista = $_POST['fecha_devolucion_prevista'];


// Recupero las observaciones
$observaciones = $_POST['observaciones'];


// Creo el objeto conexión
$conexion = new ConexionBD();


// Abro la conexión con la base
$conexion->conectar();


// Consulta de inserción
if ($fechaPrestamo >= $fechaDevolucionPrevista) {

    echo "La fecha de devolución debe ser posterior a la fecha de préstamo";
    exit();
}
else
{
$consulta = "
INSERT INTO prestamos
(
    id_equipo,
    id_funcionario,
    fecha_prestamo,
    fecha_devolucion_prevista,
    observaciones
)
VALUES
(
    $idEquipo,
    $idFuncionario,
    '$fechaPrestamo',
    '$fechaDevolucionPrevista',
    '$observaciones'
)";
}

// Ejecuto la consulta
$resultado = $conexion->ejecutarConsulta($consulta);


// Verifico si realmente se insertó un registro
if (mysqli_affected_rows($conexion->getConexion()) > 0)
{
    // Si el préstamo se registró correctamente,
    // actualizo el estado del equipo a Prestado

    $conexion->ejecutarConsulta("
        UPDATE equipos
        SET estado = 'Prestado'
        WHERE id_equipo = $idEquipo
    ");

    echo "<h3>Préstamo registrado correctamente.</h3>";
}
else
{
    // Si no se insertó ningún registro,
    // significa que la validación de fechas falló

    echo "<h3>Error:</h3>";

    //Creo que esta validacion es al pedo porque ya se validan las fechas para hacer el insert
    echo "La fecha de devolución prevista debe ser posterior a la fecha del préstamo.";
}


// Cierro la conexión
$conexion->cerrarConexion();

?>