<?php
session_start();

// solo administradores acceden a esta pagina
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.html");
    exit();
}

// mensajes para mostrar despues de procesar el form
$ok    = isset($_GET['ok']);
$error = isset($_GET['error']) ? $_GET['error'] : "";
?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de Sucursal</title>
</head>
<body>
    <fieldset>
        <legend align="center">Alta de Sucursal</legend>

        <?php if ($ok): ?>
            <p align="center"><b>Sucursal creada correctamente.</b></p>
        <?php endif; ?>

        <?php if ($error != ""): ?>
            <p align="center"><b>Error: <?php echo $error; ?></b></p>
        <?php endif; ?>

        <table align="center">
            <tr><td>
                <form action="procesar_alta_sucursal.php" method="POST">
                    <div>
                        Nombre:
                        <p><input type="text" id="nombre" name="nombre" placeholder="Nombre de la sucursal" required></p>
                    </div>
                    <div>
                        Direccion:
                        <p><input type="text" id="direccion" name="direccion" placeholder="Direccion" required></p>
                    </div>
                    <div>
                        Telefono:
                        <p><input type="text" id="telefono" name="telefono" placeholder="Telefono (opcional)"></p>
                    </div>
                    <input type="submit" name="Guardar" value="Guardar">
                </form>
            </td></tr>
        </table>
    </fieldset>
</body>
</html>
