<?php
require_once __DIR__ . "/../includes/validar_sesion.php";

// validar que es admin antes de procesar
if ($_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../clases/Usuario.php";

if (isset($_POST['Guardar'])) {

    // validacion server-side basica: campos obligatorios
    if (empty($_POST['ci']) || empty($_POST['primer_nombre']) || empty($_POST['primer_apellido'])
        || empty($_POST['fecha_nacimiento']) || empty($_POST['email']) || empty($_POST['password'])
        || empty($_POST['tipo_usuario']) || empty($_POST['id_sucursal'])) {
        header("Location: alta_usuario.php?error=Todos los campos son obligatorios (excepto segundo nombre/apellido y la foto)");
        exit();
    }

    // no permitir dos usuarios con la misma cedula
    if ((new Usuario())->cargar($_POST['ci'])) {
        header("Location: alta_usuario.php?error=Ya existe un usuario con esa cedula");
        exit();
    }

    // la foto es opcional: si se subio un archivo valido, lo movemos a fotos/
    $nombreFoto = "";
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $extensionesPermitidas)) {
            header("Location: alta_usuario.php?error=La foto debe ser jpg, jpeg, png o gif");
            exit();
        }

        $nombreFoto = basename($_POST['ci']) . '_' . time() . '.' . $extension;
        $destino = __DIR__ . '/../fotos/' . $nombreFoto;

        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
            header("Location: alta_usuario.php?error=No se pudo guardar la foto");
            exit();
        }
    }

    // crear el objeto y setear los datos del form
    $usuario = new Usuario();
    $usuario->setCi($_POST['ci']);
    $usuario->setPrimerNombre($_POST['primer_nombre']);
    $usuario->setSegundoNombre($_POST['segundo_nombre']);
    $usuario->setPrimerApellido($_POST['primer_apellido']);
    $usuario->setSegundoApellido($_POST['segundo_apellido']);
    $usuario->setFechaNacimiento($_POST['fecha_nacimiento']);
    $usuario->setEmail($_POST['email']);
    $usuario->setPassword($_POST['password']); // se hashea con md5 dentro del setter
    $usuario->setTipoUsuario($_POST['tipo_usuario']);
    $usuario->setIdSucursal($_POST['id_sucursal']);
    $usuario->setFoto($nombreFoto);

    // guardar en la base
    $resultado = $usuario->guardar();

    if ($resultado) {
        header("Location: alta_usuario.php?ok=1");
    } else {
        header("Location: alta_usuario.php?error=No se pudo guardar el usuario");
    }
    exit();
}

// si no llegamos por POST, redirigir al form
header("Location: alta_usuario.php");
exit();
?>
