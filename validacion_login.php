
<?php
session_start();  



if (isset($_POST["Ingresar"])) {

    $email = $_POST['email'];           //  
    $password = md5($_POST['password']); // Encriptar

    require_once "Conexion.php";
    $conexion = new ConexionBD();
    $conexion->conectar();

    $consulta = "SELECT ci, tipo_usuario FROM usuarios 
                 WHERE email = '$email' AND password = '$password'";
    $resultado = $conexion->ejecutarConsulta($consulta);


    if ($resultado && $resultado->mysqli_num_rows > 0) {
        
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
        if ($_SESSION['tipo_usuario'] == 'administrador') {
            header("Location: principal_admin.php");
            exit();
        } elseif ($_SESSION['tipo_usuario'] == 'funcionario') {
            header("Location: principal_funcionario.php");
            exit();
        } else {
            header("Location: login.html");  
            exit(); 

        }

    } else {

       // si no encuentra el usuario, borro las cookies
        if (isset($_COOKIE['email'])) {
            setcookie('email', '', time() - 3600); // borro la cookie expirándola
        }

        // redirijo al login con un mensaje de error
       header("Location: login.html?error=Email o contraseña incorrectos");
       exit();
    }



    $conexion->cerrarConexion();
}
?>