
<?php
session_start();  // 1. Siempre primero

if (isset($_POST["Ingresar"])) {

    $email = $_POST['email'];           // 2. Asignar variables
    $password = md5($_POST['password']); // 3. Encriptar

    require_once "Conexion.php";
    $conexion = new ConexionBD();
    $conexion->conectar();

    $consulta = "SELECT ci, tipo_usuario FROM usuarios 
                 WHERE email = '$email' AND password = '$password'";
    $resultado = $conexion->ejecutarConsulta($consulta);

    if ($resultado->num_rows > 0) {
        $fila = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
        $_SESSION['ci'] = $fila['ci'];
        $_SESSION['tipo_usuario'] = $fila['tipo_usuario'];
        $_SESSION['ultimo_acceso'] = time();

        if ($fila['tipo_usuario'] == 'administrador') {
            header("Location: principal_admin.php");
        } elseif ($fila['tipo_usuario'] == 'funcionario') {
            header("Location: principal_funcionario.php");
        } else {
            header("Location: index.php");
        }
    } else {
        echo "No hay resultados para el email y contraseña proporcionados.";
       // header("Location: login.html");  // sin echo antes de header
    }

    $conexion->cerrarConexion();
}
?>