<?php
// debo verificar que el usuario sea un funcionario antes de mostrar la página principal de funcionario
session_start();

if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'funcionario') {
     //aca va el código para mostrar la pantalla principal de funcionario

     echo "<h2>Bienvenido $_SESSION[primer_nombre]</h2>";

// incluyo el archivo de conexión a la base de datos para poder consultar los préstamos activos del funcionario
require_once "Conexion.php";

// creo un objeto de la clase ConexionBD
$conexion = new ConexionBD(); 
$conexion->conectar();  

//verificar que la conexión se haya establecido correctamente
if ($conexion->conectar() === false) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}


// Verificar el tiempo de inactividad
$tiempo_transcurrido = time() - $_SESSION['ultimo_acceso'];
//mostrar cuánto tiempo le queda al usuario antes de que se cierre la sesión por inactividad

$limite = ($_SESSION['tipo'] == 'Administrador') ? 3600 : 900; 

$tiempo_restante = $limite - $tiempo_trancurrido; 

echo "Tiempo de inactividad: " . gmdate("H:i:s", $tiempo_restante);

if ($tiempo_transcurrido > $limite) {
    // Si superó el tiempo de inactividad, cerramos la sesión
    session_unset();
    session_destroy();
    header("Location: login.html?error=sesion_expirada");
    exit;
} else {
    // Si está activo, renovamos el tiempo 
    $_SESSION['ultimo_acceso'] = time();
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
             WHERE p.estado = 'activo' AND p.id_funcionario = " . $_SESSION['id_usuario'] . " "; 

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
        echo "<tr><td><img src='fotos_equipos/" . $fila['foto'] . "' alt='Imagen del equipo' width='100'></td><td>" . 
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
    
    echo "<h2>Bienvenido, $_SESSION[primer_nombre]</h2>";

    // incluyo el archivo de conexión a la base de datos para poder consultar los préstamos activos del funcionario
    require_once "Conexion.php";

    // creo un objeto de la clase ConexionBD
    $conexion = new ConexionBD(); // creo un objeto de la clase Conexion
    $conexion->conectar();  //conectar a la base de datos

    //verificar que la conexión se haya establecido correctamente
    if ($conexion->conectar() === false) {
        die("Error al conectar a la base de datos: " . mysqli_connect_error());
    }


// Verificar el tiempo de inactividad
$tiempo_transcurrido = time() - $_SESSION['ultimo_acceso'];
//mostrar cuánto tiempo le queda al usuario antes de que se cierre la sesión por inactividad

$limite = ($_SESSION['tipo_usuario'] == 'administrador') ? 3600 : 900; 

$tiempo_restante = $limite - $tiempo_transcurrido; 

echo "Tiempo de inactividad: " . gmdate("H:i:s", $tiempo_restante);

if ($tiempo_transcurrido > $limite) {
    // Si superó el tiempo de inactividad, cerramos la sesión
    session_unset();
    session_destroy();
    header("Location: login.html?error=sesion_expirada");
    exit;
} else {
    // Si está activo, renovamos el tiempo 
    $_SESSION['ultimo_acceso'] = time();
}





}

























?>