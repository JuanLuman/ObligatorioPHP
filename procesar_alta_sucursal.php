<?php
session_start();

// validar que es admin antes de procesar
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.html");
    exit();
}

require_once "clases/Sucursal.php";
require_once "clases/Validaciones.php";


if (isset($_POST['Guardar'])) {

    // valido campos requeridos con la clase Validaciones
    $errores = Validaciones::validarFormulario($_POST, ['nombre', 'direccion']);

    if (!empty($errores)) {
        header("Location: alta_sucursal.php?error=" . urlencode($errores[0]));
        exit();
    }

    // crear el objeto y setear los datos del form
    $sucursal = new Sucursal();
    $sucursal->setNombre($_POST['nombre']);
    $sucursal->setDireccion($_POST['direccion']);
    $sucursal->setTelefono($_POST['telefono']);

    // guardar en la base
    $resultado = $sucursal->guardar();

    if ($resultado) {
        header("Location: alta_sucursal.php?ok=1");
    } else {
        header("Location: alta_sucursal.php?error=" . urlencode("No se pudo guardar la sucursal"));
    }
    exit();
}

// si no llegamos por POST, redirigir al form
header("Location: alta_sucursal.php");
exit();
?>
