<?php
require_once __DIR__ . "/../includes/validar_sesion.php";

// solo administradores acceden a esta pagina
if ($_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../clases/Sucursal.php";
require_once __DIR__ . "/../clases/Usuario.php";

// mensajes para mostrar despues de procesar el form
$ok    = isset($_GET['ok']);
$error = isset($_GET['error']) ? $_GET['error'] : "";

$sucursales = (new Sucursal())->listarTodas();
?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de Usuario</title>
</head>
<body>
    <fieldset>
        <legend align="center">Alta de Usuario</legend>

        <?php if ($ok): ?>
            <p align="center"><b>Usuario creado correctamente.</b></p>
        <?php endif; ?>

        <?php if ($error != ""): ?>
            <p align="center"><b>Error: <?php echo htmlspecialchars($error); ?></b></p>
        <?php endif; ?>

        <table align="center">
            <tr><td>
                <form action="procesar_alta_usuario.php" method="POST" enctype="multipart/form-data">
                    <div>
                        Cedula (CI):
                        <p><input type="text" name="ci" required></p>
                    </div>
                    <div>
                        Primer nombre:
                        <p><input type="text" name="primer_nombre" required></p>
                    </div>
                    <div>
                        Segundo nombre (opcional):
                        <p><input type="text" name="segundo_nombre"></p>
                    </div>
                    <div>
                        Primer apellido:
                        <p><input type="text" name="primer_apellido" required></p>
                    </div>
                    <div>
                        Segundo apellido (opcional):
                        <p><input type="text" name="segundo_apellido"></p>
                    </div>
                    <div>
                        Fecha de nacimiento:
                        <p><input type="date" name="fecha_nacimiento" required></p>
                    </div>
                    <div>
                        Email:
                        <p><input type="email" name="email" required></p>
                    </div>
                    <div>
                        Contraseña:
                        <p><input type="password" name="password" minlength="8" required></p>
                    </div>
                    <div>
                        Tipo de usuario:
                        <p>
                            <select name="tipo_usuario" required>
                                <option value="<?php echo Usuario::TIPO_FUNCIONARIO; ?>">Funcionario</option>
                                <option value="<?php echo Usuario::TIPO_ADMINISTRADOR; ?>">Administrador</option>
                            </select>
                        </p>
                    </div>
                    <div>
                        Sucursal:
                        <p>
                            <select name="id_sucursal" required>
                                <option value="">Seleccione una sucursal</option>
                                <?php foreach ($sucursales as $s): ?>
                                    <option value="<?php echo $s->getIdSucursal(); ?>">
                                        <?php echo htmlspecialchars($s->getNombre()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </p>
                    </div>
                    <div>
                        Foto (opcional):
                        <p><input type="file" name="foto" accept="image/png, image/jpeg, image/gif"></p>
                    </div>
                    <input type="submit" name="Guardar" value="Guardar">
                </form>
            </td></tr>
        </table>
    </fieldset>
</body>
</html>
