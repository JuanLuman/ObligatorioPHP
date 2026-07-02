<?php
session_start();

// validar que es admin antes de procesar
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.html");
    exit();
}

require_once "clases/Sucursal.php";


if (isset($_POST['Guardar'])) {

    // validacion server-side basica: nombre y direccion requeridos
    if (empty($_POST['nombre']) || empty($_POST['direccion'])) {
        header("Location: alta_sucursal.php?error=Nombre y direccion son obligatorios");
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
        header("Location: alta_sucursal.php?error=No se pudo guardar la sucursal");
    }
    exit();
}

// si no llegamos por POST, redirigir al form
header("Location: alta_sucursal.php");
exit();
?>
