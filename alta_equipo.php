<?php
session_start();

// solo administradores acceden a esta pagina
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.html");
    exit();
}

require_once "clases/Sucursal.php";
require_once "clases/Equipo.php";

// traigo las sucursales de la base para llenar el select
$sucursales = Sucursal::listarTodas();

$ok    = isset($_GET['ok']);
$error = isset($_GET['error']) ? $_GET['error'] : "";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de Equipo</title>
</head>
<body>
    <fieldset>
        <legend align="center">Alta de Equipo</legend>

        <?php if ($ok): ?>
            <p align="center"><b>Equipo creado correctamente.</b></p>
        <?php endif; ?>

        <?php if ($error != ""): ?>
            <p align="center"><b>Error: <?php echo $error; ?></b></p>
        <?php endif; ?>

        <table align="center">
            <tr><td>
                <form action="procesar_alta_equipo.php" method="POST">

                    <div>
                        Codigo de inventario:
                        <p><input type="text" name="codigo_inventario" required></p>
                    </div>
                    <div>
                        Marca:
                        <p><input type="text" name="marca" required></p>
                    </div>
                    <div>
                        Modelo:
                        <p><input type="text" name="modelo" required></p>
                    </div>
                    <div>
                        Anio de adquisicion:
                        <p><input type="number" name="anio_adquisicion" min="1990" max="2100"></p>
                    </div>
                    <div>
                        Valor estimado:
                        <p><input type="number" step="0.01" name="valor_estimado"></p>
                    </div>
                    <div>
                        Tipo de equipo:
                        <p><input type="text" name="tipo" placeholder="Ej: Notebook, Proyector"></p>
                    </div>
                    <div>
                        Estado:
                        <p>
                            <select name="estado">
                                <option value="<?php echo Equipo::ESTADO_DISPONIBLE; ?>">Disponible</option>
                                <option value="<?php echo Equipo::ESTADO_PRESTADO; ?>">Prestado</option>
                                <option value="<?php echo Equipo::ESTADO_MANTENIMIENTO; ?>">Mantenimiento</option>
                                <option value="<?php echo Equipo::ESTADO_BAJA; ?>">Baja</option>
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
