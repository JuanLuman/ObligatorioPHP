<?php
session_start();

if (isset($_POST["Ingresar"])) {

    $email = trim($_POST['email']); // quitar espacios al inicio y final de $_POST['email'];
    $password = md5($_POST['password']);

    require_once "Conexion.php";
    $conexion = new ConexionBD();
    $conexion->conectar();

    $consulta = "SELECT primer_nombre,id_usuario, ci, tipo_usuario FROM usuarios 
                 WHERE email = '$email' AND password = '$password'";
    $resultado = $conexion->ejecutarConsulta($consulta);


         // DEBUG TEMPORAL - borrar después
    echo "Email: " . $email . "<br>";
    echo "Password MD5: " . $password . "<br>";
    echo "Consulta: " . $consulta . "<br>";
    


    if ($resultado && mysqli_num_rows($resultado) > 0) {

        echo "<p>Usuario encontrado, iniciando sesión...</p>";

        // 1. PRIMERO obtener datos y guardar sesión
        $fila = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
        $_SESSION['ci'] = $fila['ci'];
        $_SESSION['tipo_usuario'] = $fila['tipo_usuario'];
        $_SESSION['ultimo_acceso'] = time();
        $_SESSION['id_usuario'] = $fila['id_usuario'];
        $_SESSION['primer_nombre'] = $fila['primer_nombre'];



        echo "<p>Tipo de usuario: " . $_SESSION['tipo_usuario'] . "</p>";

        // 2. Manejar cookie de recordar email
        if (isset($_POST['recordar'])) {
            setcookie('email', $email, time() + (30 * 24 * 60 * 60)); // 30 días
        } else {
            if (isset($_COOKIE['email'])) {
                setcookie('email', '', time() - 3600); // borrar cookie
            }
        }

        // 3. ÚLTIMO: redirigir según tipo de usuario
        if ($_SESSION['tipo_usuario'] == 'administrador') {
            header("Location: panel_principal.php");
            exit();
        } elseif ($_SESSION['tipo_usuario'] == 'funcionario') {
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
        header("Location: login.html? error=Email o contraseña incorrectos");
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
       header("Location: login.html?error=Email o contraseña incorrectos1");
       exit();
}


?>



    /** 
    if ($resultado && $resultado->mysqli_num_rows > 0) {

        echo "Usuario encontrado, iniciando sesión...";
        
        $fila = mysqli_fetch_array($resultado, MYSQLI_ASSOC); //Si hay resultados, asignar variables de sesión
        $_SESSION['ci'] = $fila['ci'];
        $_SESSION['tipo_usuario'] = $fila['tipo_usuario'];
        $_SESSION['ultimo_acceso'] = time(); // para controlar la sesión


       // si el usuario marca la opción de recordar su email, guardo el email en una cookie por 30 días
        if (isset($_POST['recordar'])) {
            setcookie('email', $email, time() + (30 * 24 * 60 * 60)); // 30 días
        } else {
            // si no marca la opción, borro la cookie si existe
            if (isset($_COOKIE['email'])) {
                setcookie('email', '', time() - 3600); // borro la cookie expirándola
            }
        }

 
        // Redirijo a la página principal según el tipo de usuario
        if ($_SESSION['tipo_usuario'] == 'administrador' || $_SESSION['tipo_usuario'] == 'funcionario') {
            header("Location: panel_principal.php");
            exit();
        
        } else {

            header("Location: login.html");  
            echo "Tipo de usuario no reconocido, redirigiendo al login...";
            exit(); 

        }

    } else {

       // si no encuentra el usuario, borro las cookies
        if (isset($_COOKIE['email'])) {
            setcookie('email', '', time() - 3600); // borro la cookie expirándola
        }

        // redirijo al login con un mensaje de error
       header("Location: login.html?error=Email o contraseña incorrectos1");
       exit();
    }

    

    $conexion->cerrarConexion();
//}
?>
**/