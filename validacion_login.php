<?php
session_start();

if (isset($_POST["Ingresar"])) {

    $email = trim($_POST['email']); // quitar espacios al inicio y final de $_POST['email'];
    $password = md5($_POST['password']);

    require_once "Conexion.php";
    $conexion = new ConexionBD();
    $conexion->conectar();

    $consulta = "SELECT primer_nombre, id_usuario, ci, tipo_usuario FROM usuarios 
                 WHERE email = '$email' AND password = '$password'";

    $resultado = $conexion->ejecutarConsulta($consulta);

    


    if ($resultado && mysqli_num_rows($resultado) > 0) {

        echo "<p>Usuario encontrado, iniciando sesión...</p>";

        // obtener datos y guardar sesión
        $fila = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
        $_SESSION['ci'] =             $fila['ci'];
        $_SESSION['tipo_usuario'] =   $fila['tipo_usuario'];
        $_SESSION['ultimo_acceso'] =  time();
        $_SESSION['id_usuario'] =     $fila['id_usuario'];
        $_SESSION['primer_nombre'] =  $fila['primer_nombre'];



        echo "<p>Tipo de usuario: " . $_SESSION['tipo_usuario'] . "</p>"; 

        // 2. Manejar cookie de recordar email
        if (isset($_POST['recordar'])) {
            setcookie('email', $email, time() + (30 * 24 * 60 * 60)); // 30 días
        } else {
            if (isset($_COOKIE['email'])) {
                setcookie('email', '', time() - 3600); // borrar cookie
            }
        }



        // redirigir según tipo de usuario
        if ($_SESSION['tipo_usuario'] == 'administrador' || $_SESSION['tipo_usuario'] == 'funcionario') {
            header("Location: panel_principal.php");
            exit();
      
        } else {
            header("Location: login.html?error=Tipo de usuario no reconocido");
            exit();
        }


    } else {
        // Limpiar cookie si login falla
        if (isset($_COOKIE['email'])) {
            setcookie('email', '', time() - 3600);
        }
        header("Location: login.html? error = Usuario no encontrado");
        echo "<p>Email o contraseña incorrectos, redirigiendo al login...</p>";
        exit();
    }

    $conexion->cerrarConexion();
}
else{
    // si no encuentra el usuario, borro las cookies
        if (isset($_COOKIE['email'])) {
            setcookie('email', '', time() - 3600); // borro la cookie expirándola
        }

        // redirijo al login con un mensaje de error
       header("Location: login.html?error=Email o contraseña incorrectos");
       exit();
}




?>