<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.html");
    exit();
}

require_once "clases/Equipo.php";
require_once "clases/Validaciones.php";


if (isset($_POST['Guardar'])) {

    // validacion de requeridos
    $errores = Validaciones::validarFormulario($_POST, ['codigo_inventario', 'marca', 'modelo', 'id_sucursal']);

    if (!empty($errores)) {
        header("Location: alta_equipo.php?error=" . urlencode($errores[0]));
        exit();
    }

    // crear el equipo y llenar los datos del form
    $equipo = new Equipo();
    $equipo->setCodigoInventario($_POST['codigo_inventario']);
    $equipo->setMarca($_POST['marca']);
    $equipo->setModelo($_POST['modelo']);
    $equipo->setAnioAdquisicion($_POST['anio_adquisicion']);
    $equipo->setValorEstimado($_POST['valor_estimado']);
    $equipo->setTipo($_POST['tipo']);
    $equipo->setEstado($_POST['estado']);
    $equipo->setIdSucursal($_POST['id_sucursal']);

    $resultado = $equipo->guardar();

    if ($resultado) {
        header("Location: alta_equipo.php?ok=1");
    } else {
        header("Location: alta_equipo.php?error=" . urlencode("No se pudo guardar el equipo"));
    }
    exit();
}

header("Location: alta_equipo.php");
exit();
?>
