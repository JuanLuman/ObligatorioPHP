<?php
require_once __DIR__ . "/../includes/validar_sesion.php";

// validar que es admin antes de procesar
if ($_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../clases/Equipo.php";

if (isset($_POST['Guardar'])) {

    // validacion server-side basica: campos obligatorios
    if (empty($_POST['codigo_inventario']) || empty($_POST['marca']) || empty($_POST['modelo'])
        || empty($_POST['tipo_equipo']) || empty($_POST['anio_adquisicion'])
        || $_POST['valor_estimado'] === '' || empty($_POST['id_sucursal'])) {
        header("Location: alta_equipo.php?error=Todos los campos son obligatorios (excepto la foto)");
        exit();
    }

    // la foto es opcional: si se subio un archivo valido, lo movemos a fotos/
    $nombreFoto = "";
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $extensionesPermitidas)) {
            header("Location: alta_equipo.php?error=La foto debe ser jpg, jpeg, png o gif");
            exit();
        }

        $nombreFoto = basename($_POST['codigo_inventario']) . '_' . time() . '.' . $extension;
        $destino = __DIR__ . '/../fotos/' . $nombreFoto;

        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
            header("Location: alta_equipo.php?error=No se pudo guardar la foto");
            exit();
        }
    }

    // crear el objeto y setear los datos del form
    $equipo = new Equipo();
    $equipo->setCodigoInventario($_POST['codigo_inventario']);
    $equipo->setMarca($_POST['marca']);
    $equipo->setModelo($_POST['modelo']);
    $equipo->setTipo($_POST['tipo_equipo']);
    $equipo->setAnioAdquisicion($_POST['anio_adquisicion']);
    $equipo->setValorEstimado($_POST['valor_estimado']);
    $equipo->setIdSucursal($_POST['id_sucursal']);
    $equipo->setEstado(Equipo::ESTADO_DISPONIBLE);
    $equipo->setFoto($nombreFoto);

    // guardar en la base
    $resultado = $equipo->guardar();

    if ($resultado) {
        header("Location: alta_equipo.php?ok=1");
    } else {
        header("Location: alta_equipo.php?error=No se pudo guardar el equipo");
    }
    exit();
}

// si no llegamos por POST, redirigir al form
header("Location: alta_equipo.php");
exit();
?>
