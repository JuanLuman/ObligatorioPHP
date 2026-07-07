<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.html");
    exit();
}

require_once "clases/Usuario.php";
require_once "clases/Validaciones.php";


if (isset($_POST['Guardar'])) {

    // valido requeridos, email y password minimo 8 con Validaciones
    $errores = Validaciones::validarFormulario($_POST,
        ['ci', 'primer_nombre', 'primer_apellido', 'email', 'password', 'tipo_usuario', 'id_sucursal']);

    if (!empty($errores)) {
        header("Location: alta_usuario.php?error=" . urlencode($errores[0]));
        exit();
    }

    // crear el usuario
    $usuario = new Usuario();
    $usuario->setCi($_POST['ci']);
    $usuario->setPrimerNombre($_POST['primer_nombre']);
    $usuario->setSegundoNombre($_POST['segundo_nombre']);
    $usuario->setPrimerApellido($_POST['primer_apellido']);
    $usuario->setSegundoApellido($_POST['segundo_apellido']);
    $usuario->setFechaNacimiento($_POST['fecha_nacimiento']);
    $usuario->setEmail($_POST['email']);
    $usuario->setPassword($_POST['password']);   // el setter hashea internamente
    $usuario->setTipoUsuario($_POST['tipo_usuario']);
    $usuario->setIdSucursal($_POST['id_sucursal']);
    $usuario->setActivo(1);

    $resultado = $usuario->guardar();

    if ($resultado) {
        header("Location: alta_usuario.php?ok=1");
    } else {
        header("Location: alta_usuario.php?error=" . urlencode("No se pudo guardar el usuario"));
    }
    exit();
}

header("Location: alta_usuario.php");
exit();
?>
