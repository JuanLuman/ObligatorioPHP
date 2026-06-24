<?php
// debo verificar que el usuario sea un funcionario antes de mostrar la página principal de funcionario
session_start();

if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'funcionario') {
     //aca va el código para mostrar la pantalla principal de funcionario

     echo "<h2>Bienvenido, $_SESSION[primer_nombre]</h2>";



// Verificar el tiempo de inactividad
$tiempo_transcurrido = time() - $_SESSION['ultimo_acceso'];
//mostrar cuánto tiempo le queda al usuario antes de que se cierre la sesión por inactividad

$limite = ($_SESSION['tipo'] == 'administrador') ? 3600 : 900; 

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





// debo interactuar con las clases de la carpeta clases 
require_once "Conexion.php";
require_once "Equipo.php";
require_once "Prestamo.php";

// en vez de crear un objeto de la clase ConexionBD, utilizo las clases de la carpeta clases
// como quiero mostrar los equipos prestados, creo un objeto de la clase Funcionario

// consultar a la base por los préstamos activos del funcionario
//esta consulta debe ir en la clase prestamo para poder reutilizarla en otros archivos

$prestamo_activo = new Prestamo();
//reutilizo funcion de la clase prestamo para obtener los prestamos activos del funcionario
$prestamo_activo->ObtenerPrestamosActivos($_SESSION['id_usuario']);
// no creo objeto de la clase conexion porque ya la estoy utilizando en la clase prestamo al usar require_once
//si el usuario no tiene prestamos activos, se le muestra un mensaje indicando que no tiene prestamos activos

if (empty($prestamo_activo->getObtenerPrestamosActivos())) { 
    echo "<p>Usted no posee préstamos activos.</p>";
}else
{
    //si tiene prestamos activos los muestro por pantalla en una tabla
    echo "<fieldset><legend align='center'>Préstamos activos</legend>";
    echo "<table border='1' align='center'>";
    echo "<tr><th>Imagen</th><th>
                  Código de inventario</th><th>
                  Marca y modelo</th><th>
                  Fecha préstamo</th><th>
                  Fecha devolución prevista</th><th>
                  Estado</th></tr>";
    foreach ($prestamo_activo->getObtenerPrestamosActivos() as $prestamo) {
        echo "<tr>";
        echo "<td><img src='fotos_equipos/" . $prestamo->getFoto() . "' alt='Imagen del equipo' width='100'></td>";
        echo "<td>" . $prestamo->getCodigoInventario() . "</td>";
        echo "<td>" . $prestamo->getMarca() . " " . $prestamo->getModelo() . "</td>";
        echo "<td>" . $prestamo->getFechaPrestamo() . "</td>";
        echo "<td>" . $prestamo->getFechaDevolucionPrevista() . "</td>";
        echo "<td>" . $prestamo->getEstado() . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</fieldset>";

    //cierro la conexion a la base de datos
    $conexion->CerrarConexion();


}







   

} 






// si es administrador, muestro la pantalla principal de administrador
if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'administrador') {
    
    echo "<h2>Bienvenido, $_SESSION[primer_nombre]</h2>";

   require_once "Conexion.php";

   
    $conexion = new ConexionBD(); 
    $conexion->conectar(); 


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



// mostrar todos los equipos prestados
$equipos_prestados = new Equipo();
$equipos_prestados->ObtenerEquiposPrestados();

if (empty($equipos_prestados->getObtenerEquiposPrestados()))    
    {
        echo "<p>No hay equipos prestados.</p>";
    }
    else
    {
        //si hay equipos prestados los muestro por pantalla en una tabla

    echo "<fieldset><legend align='center'>Equipos prestados</legend>";
    echo "<table border='1' align='center'>";
    echo "<tr><th>Imagen</th><th>
                Código de inventario</th><th>
                Marca y modelo</th><th>
                Fecha préstamo</th><th>
                Fecha devolución prevista</th><th>
                Estado</th></tr>";
            
    foreach ($equipos_prestados->getObtenerEquiposPrestados() as $equipo) {
        echo "<tr>";
        echo "<td><img src='fotos_equipos/" . $equipo->getFoto() . "' alt='Imagen del equipo' width='100'></td>";
        echo "<td>" . $equipo->getCodigoInventario() . "</td>";
        echo "<td>" . $equipo->getMarca() . " " . $equipo->getModelo() . "</td>";
        echo "<td>" . $equipo->getFechaPrestamo() . "</td>";
        echo "<td>" . $equipo->getFechaDevolucionPrevista() . "</td>";
        echo "<td>" . $equipo->getEstado() . "</td>";
        echo "</tr>";
    }
    }
    echo "</table>";
    echo "</fieldset>";

    //cierro la conexion a la base de datos
    $conexion->CerrarConexion();







}

























?>