<?php
require_once __DIR__ . "/../includes/validar_sesion.php";

// solo administradores acceden a esta pagina
if ($_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../clases/Sucursal.php";

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
    <title>Alta de Equipo</title>
</head>
<body>
    <fieldset>
        <legend align="center">Alta de Equipo</legend>

        <?php if ($ok): ?>
            <p align="center"><b>Equipo creado correctamente.</b></p>
        <?php endif; ?>

        <?php if ($error != ""): ?>
            <p align="center"><b>Error: <?php echo htmlspecialchars($error); ?></b></p>
        <?php endif; ?>

        <table align="center">
            <tr><td>
                <form action="procesar_alta_equipo.php" method="POST" enctype="multipart/form-data">
                    <div>
                        Codigo de inventario:
                        <p><input type="text" name="codigo_inventario" placeholder="Ej: EQ-004" required></p>
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
                        Tipo de equipo:
                        <p><input type="text" name="tipo_equipo" placeholder="Ej: Laptop, Impresora" required></p>
                    </div>
                    <div>
                        Año de adquisicion:
                        <p><input type="number" name="anio_adquisicion" min="1990" max="2100" required></p>
                    </div>
                    <div>
                        Valor estimado:
                        <p><input type="number" name="valor_estimado" step="0.01" min="0" required></p>
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

    <p align="center"><a href="inicio.php">Volver al menú principal</a></p>

</body>
</html>
