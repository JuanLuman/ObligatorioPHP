<?php

// Los reportes son una funcionalidad exclusiva del administrador, por lo tanto,
// debo verificar que el usuario sea un administrador antes de mostrar la página de reportes

//debo invocar session_start() antes de cualquier salida al navegador para poder usar   // el array superglobal $_SESSION
//session_start();

//verifico que el usuario sea un administrador, si no lo es, lo redirijo a la página de login
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.html");
    exit();
}
//test

// incluyo el archivo de conexión a la base de datos para poder consultar los datos necesarios para los reportes
require_once "Conexion.php";


echo "<h2>Reportes - TechRent</h2>";

// consultar a la base por los equipos mas prestados

function EquiposMasPrestados() {

// crear un objeto de la clase ConexionBD
$conexion = new ConexionBD(); // creo un objeto de la clase Conexion
$conexion->conectar();  //conectar a la base de datos

//verificar que la conexión se haya establecido correctamente
if ($conexion->conectar() === false) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// consultar a la base por los equipos mas prestados
$consulta = "SELECT e.id_equipo, COUNT(p.id_prestamo) AS cantidad_prestamos  //cuento la cantidad de prestamos por cada equipo
             FROM prestamos p 
             INNER JOIN equipos e ON p.id_equipo = e.id_equipo 
             GROUP BY e.id_equipo 
             ORDER BY cantidad_prestamos DESC";

$resultado = $conexion->ejecutarConsulta($consulta);

// Mostrar los resultados en una tabla HTML
echo "<fieldset><legend align='center'>Equipo mas prestado</legend>";
echo "<table border='1' align='center'>";
echo "<tr><th>ID Equipo</th><th>
              Cantidad de prestamos</th></tr>";

while ($fila = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
    echo "<tr><td>" . $fila['id_equipo'] . "</td><td>" . 
                      $fila['cantidad_prestamos'] . "</td></tr>";
}
echo "</table>";
echo "</fieldset>";

// cerrar la conexión a la base de datos
$conexion->cerrarConexion();

}






// consultar a la base por los funcionarios con mas prestamos 

function FuncionariosConMasPrestamos() {

// crear un objeto de la clase ConexionBD
$conexion = new ConexionBD(); // creo un objeto de la clase Conexion
$conexion->conectar();  //conectar a la base de datos

//verificar que la conexión se haya establecido correctamente
if ($conexion->conectar() === false) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// consultar a la base por los funcionarios con mas prestamos
$consulta = "SELECT u.id_usuario, u.primer_nombre, u.primer_apellido, COUNT(p.id_prestamo) AS cantidad_prestamos  //cuento la cantidad de prestamos por cada funcionario
             FROM usuarios u
             INNER JOIN prestamos p ON u.id_usuario = p.id_funcionario
             GROUP BY u.id_usuario 
             ORDER BY cantidad_prestamos DESC";

$resultado = $conexion->ejecutarConsulta($consulta);

// Mostrar los resultados en una tabla HTML
echo "<fieldset><legend align='center'>Funcionarios con mas prestamos</legend>";
echo "<table border='1' align='center'>";
echo "<tr><th>ID Funcionario</th><th>      
              Primer Nombre</th><th> 
              Primer Apellido</th><th> 
              Cantidad de prestamos</th></tr>";

while ($fila = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
    echo "<tr><td>" . $fila['id_usuario'] . "</td><td>" . 
                      $fila['primer_nombre'] . "</td><td>" . 
                      $fila['primer_apellido'] . "</td><td>" . 
                      $fila['cantidad_prestamos'] . "</td></tr>";
}
echo "</table>";
echo "</fieldset>";

// cerrar la conexión a la base de datos
$conexion->cerrarConexion();

}





// Consulto a la base por equipos actualmente vencidos
function EquiposVencidos() {

// crear un objeto de la clase ConexionBD
$conexion = new ConexionBD(); // creo un objeto de la clase Conexion
$conexion->conectar();  //conectar a la base de datos

//verificar que la conexión se haya establecido correctamente
if ($conexion->conectar() === false) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// consultar a la base por los equipos actualmente vencidos
//sin devolucion real y con fecha prevista ya pasada
$consulta = "SELECT e.id_equipo, 
                    e.marca, 
                    e.modelo, 
                    p.fecha_devolucion_prevista,
                    DATEDIFF(CURDATE(), p.fecha_devolucion_prevista) AS dias_vencidos  //calculo la cantidad de dias vencidos restando la fecha prevista de devolucion a la fecha actual
                FROM prestamos p
                INNER JOIN equipos e ON p.id_equipo = e.id_equipo
                WHERE p.fecha_devolucion_prevista < CURDATE() AND p.fecha_devolucion_real IS NULL
                ORDER BY dias_vencidos DESC";


$resultado = $conexion->ejecutarConsulta($consulta);


//verifico si hay resultados, si no los hay, muestro un mensaje indicando que no hay equipos vencidos
if (mysqli_num_rows($resultado) == 0) {
    echo "<p align='center'>No hay equipos vencidos actualmente.</p>";
    $conexion->cerrarConexion();
    return;
}   


// Mostrar los resultados en una tabla HTML
echo "<fieldset><legend align='center'>Equipos vencidos</legend>";
echo "<table border='1' align='center'>";
echo "<tr><th>ID Equipo</th><th>      
              Marca</th><th> 
              Modelo</th><th> 
              Fecha Devolucion Prevista</th><th> 
              Dias Vencidos</th></tr>";

while ($fila = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
    echo "<tr><td>" . $fila['id_equipo'] . "</td><td>" . 
                      $fila['marca'] . "</td><td>" . 
                      $fila['modelo'] . "</td><td>" . 
                      $fila['fecha_devolucion_prevista'] . "</td><td>" . 
                      $fila['dias_vencidos'] . "</td></tr>";
}
echo "</table>";
echo "</fieldset>";

// cerrar la conexión a la base de datos
$conexion->cerrarConexion();
}




// prestamos por sucursal
function PrestamosPorSucursal() {
// crear un objeto de la clase ConexionBD
$conexion = new ConexionBD(); // creo un objeto de la clase Conexion
$conexion->conectar();  //conectar a la base de datos

//verificar que la conexión se haya establecido correctamente
if ($conexion->conectar() === false) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}


// consultar a la base por la cantidad de prestamos por sucursal
$consulta = "SELECT s.id_sucursal, s.nombre_sucursal, COUNT(p.id_prestamo) AS cantidad_prestamos  //cuento la cantidad de prestamos por cada sucursal
             FROM sucursales s
             INNER JOIN prestamos p ON s.id_sucursal = p.id_sucursal
             GROUP BY s.id_sucursal 
             ORDER BY cantidad_prestamos DESC";

$resultado = $conexion->ejecutarConsulta($consulta);    

// si no hay resultados, muestro un mensaje indicando que no hay prestamos registrados
if (mysqli_num_rows($resultado) == 0) {
    echo "<p align='center'>No hay prestamos registrados.</p>";
    $conexion->cerrarConexion();
    return;
}    

// Mostrar los resultados en una tabla HTML
echo "<fieldset><legend align='center'> Prestamos por sucursal </legend>";
echo "<table border='1' align='center'>";
echo "<tr><th>ID Sucursal</th><th>      
              Nombre Sucursal</th><th> 
              Cantidad de prestamos</th></tr>";

while ($fila = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
    echo "<tr><td>" . $fila['id_sucursal'] . "</td><td>" . 
                      $fila['nombre_sucursal'] . "</td><td>" . 
                      $fila['cantidad_prestamos'] . "</td></tr>";
}
echo "</table>";
echo "</fieldset>";

// cerrar la conexión a la base de datos
$conexion->cerrarConexion();
}












?>