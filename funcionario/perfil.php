<?php
require_once __DIR__ . "/../includes/validar_sesion.php";

if ($_SESSION['tipo_usuario'] !== 'funcionario') {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../clases/Usuario.php";

$usuario = new Usuario();
$usuario->cargar($_SESSION['id_usuario']);

$mensaje = "";
$error = "";

//=======================================================
// Edición de datos editables (nombres, apellidos, fecha nac.,
// correo electrónico, foto, contraseña), bloqueando CI y tipo de usuario
//=======================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['primer_nombre']) || empty($_POST['primer_apellido'])
        || empty($_POST['fecha_nacimiento']) || empty($_POST['email'])) {
        $error = "Nombre, apellido, fecha de nacimiento y email son obligatorios";
    } elseif ($_POST['password'] !== '' && strlen($_POST['password']) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres";
    } else {
        $usuario->setPrimerNombre($_POST['primer_nombre']);
        $usuario->setSegundoNombre($_POST['segundo_nombre']);
        $usuario->setPrimerApellido($_POST['primer_apellido']);
        $usuario->setSegundoApellido($_POST['segundo_apellido']);
        $usuario->setFechaNacimiento($_POST['fecha_nacimiento']);
        $usuario->setEmail($_POST['email']);

        if ($_POST['password'] !== '') {
            $usuario->setPassword($_POST['password']);
        }

        // la foto es opcional: si se subio un archivo valido, lo movemos a fotos/
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
            $extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

            if (!in_array($extension, $extensionesPermitidas)) {
                $error = "La foto debe ser jpg, jpeg, png o gif";
            } else {
                $nombreFoto = basename($usuario->getCi()) . '_' . time() . '.' . $extension;
                $destino = __DIR__ . '/../fotos/' . $nombreFoto;

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
                    $usuario->setFoto($nombreFoto);
                } else {
                    $error = "No se pudo guardar la foto";
                }
            }
        }

        if ($error === '') {
            if ($usuario->guardar()) {
                $mensaje = "Datos actualizados correctamente.";
                $_SESSION['primer_nombre'] = $usuario->getPrimerNombre();
            } else {
                $error = "No se pudieron guardar los cambios";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi perfil - TechRent</title>
</head>
<body>
    <fieldset>
        <legend align="center">Mi perfil</legend>

        <?php if ($mensaje !== ""): ?>
            <p align="center" style="color:green"><b><?php echo htmlspecialchars($mensaje); ?></b></p>
        <?php endif; ?>

        <?php if ($error !== ""): ?>
            <p align="center" style="color:red"><b><?php echo htmlspecialchars($error); ?></b></p>
        <?php endif; ?>

        <table align="center">
            <tr><td>
                <form method="POST" enctype="multipart/form-data">
                    <div>
                        Cédula (CI):
                        <p><?php echo htmlspecialchars($usuario->getCi()); ?></p>
                    </div>
                    <div>
                        Tipo de usuario:
                        <p><?php echo htmlspecialchars($usuario->getTipoUsuario()); ?></p>
                    </div>
                    <div>
                        Primer nombre:
                        <p><input type="text" name="primer_nombre" value="<?php echo htmlspecialchars($usuario->getPrimerNombre()); ?>" required></p>
                    </div>
                    <div>
                        Segundo nombre:
                        <p><input type="text" name="segundo_nombre" value="<?php echo htmlspecialchars($usuario->getSegundoNombre() ?? ''); ?>"></p>
                    </div>
                    <div>
                        Primer apellido:
                        <p><input type="text" name="primer_apellido" value="<?php echo htmlspecialchars($usuario->getPrimerApellido()); ?>" required></p>
                    </div>
                    <div>
                        Segundo apellido:
                        <p><input type="text" name="segundo_apellido" value="<?php echo htmlspecialchars($usuario->getSegundoApellido() ?? ''); ?>"></p>
                    </div>
                    <div>
                        Fecha de nacimiento:
                        <p><input type="date" name="fecha_nacimiento" value="<?php echo htmlspecialchars($usuario->getFechaNacimiento() ?? ''); ?>" required></p>
                    </div>
                    <div>
                        Email:
                        <p><input type="email" name="email" value="<?php echo htmlspecialchars($usuario->getEmail()); ?>" required></p>
                    </div>
                    <div>
                        Nueva contraseña (dejar vacío para no cambiarla):
                        <p><input type="password" name="password" minlength="8" placeholder="Mínimo 8 caracteres"></p>
                    </div>
                    <div>
                        Foto actual:
                        <p>
                            <?php if ($usuario->getFoto()): ?>
                                <img src="../fotos/<?php echo htmlspecialchars($usuario->getFoto()); ?>" width="100">
                            <?php else: ?>
                                Sin foto
                            <?php endif; ?>
                        </p>
                        Cambiar foto (opcional):
                        <p><input type="file" name="foto" accept="image/png, image/jpeg, image/gif"></p>
                    </div>
                    <input type="submit" value="Guardar cambios">
                </form>
            </td></tr>
        </table>
    </fieldset>

    <p align="center"><a href="inicio.php">Volver</a> | <a href="../logout.php">Salir</a></p>

</body>
</html>
