<?php
// debo verificar que el usuario sea un funcionario antes de mostrar la página principal de funcionario
session_start();

if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'funcionario') {
     //aca va el código para mostrar la pantalla principal de funcionario

     echo "<h2>Bienvenido, funcionario</h2>";

// incluyo el archivo de conexión a la base de datos para poder consultar los préstamos activos del funcionario
require_once "Conexion.php";

// creo un objeto de la clase ConexionBD
$conexion = new ConexionBD(); // creo un objeto de la clase Conexion
$conexion->conectar();  //conectar a la base de datos

//verificar que la conexión se haya establecido correctamente
if ($conexion->conectar() === false) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// consultar a la base por los préstamos activos del funcionario
$consulta = "SELECT p.id_prestamo, 
                    p.id_funcionario, 
                    p.id_equipo, 
                    p.fecha_prestamo, 
                    p.fecha_devolucion_prevista, 
                    p.fecha_devolucion_real, 
                    p.estado, 
                    e.foto, 
                    e.codigo_inventario, 
                    e.marca, 
                    e.modelo
             FROM prestamos p 
             INNER JOIN equipos e ON p.id_equipo = e.id_equipo 
             WHERE p.id_funcionario = " . $_SESSION['ci'] . " AND p.estado = 'activo'";

$resultado = $conexion->ejecutarConsulta($consulta);

//verifico si hay resultados, si no los hay, muestro un mensaje indicando que no hay prestamos activos
if ($resultado && mysqli_num_rows($resultado) > 0) {

      // Mostrar los resultados en una tabla 
    echo "<fieldset><legend align='center'>Préstamos activos</legend>";
    echo "<table border='1' align='center'>";
    echo "<tr><th>Imagen</th><th>
                  Código de inventario</th><th>
                  Marca y modelo</th><th>
                  Fecha préstamo</th><th>
                  Fecha devolución prevista</th><th>
                  Estado</th></tr>";

    while ($fila = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
        echo "<tr><td><img src='" . $fila['foto'] . "' alt='Imagen del equipo' width='100'></td><td>" . 
                                    $fila['codigo_inventario'] . "</td><td>" . 
                                    $fila['marca'] . " " . 
                                    $fila['modelo'] . "</td><td>" . 
                                    $fila['fecha_prestamo'] . "</td><td>" . 
                                    $fila['fecha_devolucion_prevista'] . "</td><td>" . 
                                    $fila['estado'] . "</td></tr>";
    }
    echo "</table>";
    echo "</fieldset>";

    $conexion->cerrarConexion();
    return;
}
else{
    echo "<p align='center'>Usted no posee prestamos activos.</p>";
    $conexion->cerrarConexion();
    return;
    }


} 






// si es administrador, muestro la pantalla principal de administrador
if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'administrador') {
    
    echo "<h2>Bienvenido, administrador</h2>";

    // incluyo el archivo de conexión a la base de datos para poder consultar los préstamos activos del funcionario
    require_once "Conexion.php";

    // creo un objeto de la clase ConexionBD
    $conexion = new ConexionBD(); // creo un objeto de la clase Conexion
    $conexion->conectar();  //conectar a la base de datos

    //verificar que la conexión se haya establecido correctamente
    if ($conexion->conectar() === false) {
        die("Error al conectar a la base de datos: " . mysqli_connect_error());
    }






}

























?>