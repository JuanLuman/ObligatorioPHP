<?php
session_start();

if (!isset($_SESSION['ci'])) {
    header("Location: login.html");
    exit();
}

require_once "clases/Usuario.php";
require_once "clases/Validaciones.php";


if (isset($_POST['Guardar'])) {

    // valido requeridos y formato email (password se valida despues porque es opcional)
    $errores = Validaciones::validarFormulario($_POST, ['primer_nombre', 'primer_apellido', 'email']);

    if (!empty($errores)) {
        header("Location: editar_perfil.php?error=" . urlencode($errores[0]));
        exit();
    }

    // password nueva es opcional (solo cambia si el usuario la escribio)
    if (!empty($_POST['password']) && !Validaciones::esPasswordValido($_POST['password'])) {
        header("Location: editar_perfil.php?error=" . urlencode("Password minimo 8 caracteres"));
        exit();
    }

    // cargo el usuario actual desde la base para preservar los campos que no se editan
    $usuario = new Usuario();
    if (!$usuario->cargarPorCi($_SESSION['ci'])) {
        header("Location: editar_perfil.php?error=" . urlencode("No se encontro el usuario"));
        exit();
    }

    // actualizo solo los campos editables (letra pag 5)
    $usuario->setPrimerNombre($_POST['primer_nombre']);
    $usuario->setSegundoNombre($_POST['segundo_nombre']);
    $usuario->setPrimerApellido($_POST['primer_apellido']);
    $usuario->setSegundoApellido($_POST['segundo_apellido']);
    $usuario->setFechaNacimiento($_POST['fecha_nacimiento']);
    $usuario->setEmail($_POST['email']);

    // si escribio password nueva, la cambio (setPassword hashea)
    if (!empty($_POST['password'])) {
        $usuario->setPassword($_POST['password']);
    }

    // guardo (Usuario::guardar detecta que existe y hace UPDATE)
    $resultado = $usuario->guardar();

    if ($resultado) {
        header("Location: editar_perfil.php?ok=1");
    } else {
        header("Location: editar_perfil.php?error=" . urlencode("No se pudo actualizar el perfil"));
    }
    exit();
}

header("Location: editar_perfil.php");
exit();
?>
