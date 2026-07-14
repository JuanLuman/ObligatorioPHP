<?php
require_once __DIR__ . "/../includes/validar_sesion.php";

// solo administradores acceden a esta pagina
if ($_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../clases/Usuario.php";
require_once __DIR__ . "/../clases/Equipo.php";

// mensajes para mostrar despues de procesar el form
$ok    = isset($_GET['ok']);
$error = isset($_GET['error']) ? $_GET['error'] : "";

// solo funcionarios pueden recibir un prestamo
$funcionarios = array_filter((new Usuario())->listarTodos(), fn($u) => $u->esFuncionario());

// solo equipos disponibles pueden prestarse (el admin puede elegir de cualquier sucursal)
$equipos = array_filter((new Equipo())->listarTodos(), fn($e) => $e->getEstado() === Equipo::ESTADO_DISPONIBLE);
?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Préstamo</title>
</head>
<body>
    <fieldset>
        <legend align="center">Registrar Préstamo</legend>

        <?php if ($ok): ?>
            <p align="center"><b>Préstamo registrado correctamente.</b></p>
        <?php endif; ?>

        <?php if ($error != ""): ?>
            <p align="center"><b>Error: <?php echo htmlspecialchars($error); ?></b></p>
        <?php endif; ?>

        <table align="center">
            <tr><td>
                <form action="procesarPrestamo.php" method="POST">
                    <div>
                        Funcionario:
                        <p>
                            <select name="id_funcionario" required>
                                <option value="">Seleccione un funcionario</option>
                                <?php foreach ($funcionarios as $f): ?>
                                    <option value="<?php echo $f->getCi(); ?>">
                                        <?php echo htmlspecialchars($f->getPrimerNombre() . ' ' . $f->getPrimerApellido() . ' (' . $f->getCi() . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </p>
                    </div>
                    <div>
                        Equipo:
                        <p>
                            <select name="id_equipo" required>
                                <option value="">Seleccione un equipo</option>
                                <?php foreach ($equipos as $e): ?>
                                    <option value="<?php echo $e->getIdEquipo(); ?>">
                                        <?php echo htmlspecialchars($e->getCodigoInventario() . ' - ' . $e->getMarca() . ' ' . $e->getModelo()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </p>
                    </div>
                    <div>
                        Fecha préstamo:
                        <p><input type="date" name="fecha_prestamo" required></p>
                    </div>
                    <div>
                        Fecha devolución prevista:
                        <p><input type="date" name="fecha_devolucion_prevista" required></p>
                    </div>
                    <div>
                        Observaciones:
                        <p><textarea name="observaciones" rows="4" cols="40"></textarea></p>
                    </div>
                    <input type="submit" name="Guardar" value="Guardar">
                </form>
            </td></tr>
        </table>
    </fieldset>

    <p align="center"><a href="inicio.php">Volver al menú principal</a></p>

</body>
</html>
