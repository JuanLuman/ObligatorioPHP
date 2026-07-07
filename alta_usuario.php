<?php
session_start();

// solo administradores
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.html");
    exit();
}

require_once "clases/Sucursal.php";
require_once "clases/Usuario.php";

// traigo las sucursales para el select
$sucursales = Sucursal::listarTodas();

$ok    = isset($_GET['ok']);
$error = isset($_GET['error']) ? $_GET['error'] : "";
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
            <p align="center"><b>Error: <?php echo $error; ?></b></p>
        <?php endif; ?>

        <table align="center">
            <tr><td>
                <form action="procesar_alta_usuario.php" method="POST">

                    <div>
                        CI:
                        <p><input type="text" name="ci" required></p>
                    </div>
                    <div>
                        Primer nombre:
                        <p><input type="text" name="primer_nombre" required></p>
                    </div>
                    <div>
                        Segundo nombre:
                        <p><input type="text" name="segundo_nombre"></p>
                    </div>
                    <div>
                        Primer apellido:
                        <p><input type="text" name="primer_apellido" required></p>
                    </div>
                    <div>
                        Segundo apellido:
                        <p><input type="text" name="segundo_apellido"></p>
                    </div>
                    <div>
                        Fecha de nacimiento:
                        <p><input type="date" name="fecha_nacimiento"></p>
                    </div>
                    <div>
                        Email:
                        <p><input type="email" name="email" required></p>
                    </div>
                    <div>
                        Password (minimo 8 caracteres):
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
                                <option value="">-- Elegir sucursal --</option>
                                <?php foreach ($sucursales as $s): ?>
                                    <option value="<?php echo $s->getIdSucursal(); ?>">
                                        <?php echo $s->getNombre(); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </p>
                    </div>

                    <input type="submit" name="Guardar" value="Guardar">
                </form>
            </td></tr>
        </table>
    </fieldset>
</body>
</html>
