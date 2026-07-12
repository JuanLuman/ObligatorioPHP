<?php

// Inicio la sesión para obtener los datos del usuario logueado
require_once __DIR__ . "/../includes/validar_sesion.php";

// Solo administradores pueden registrar préstamos desde acá
if ($_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: ../login.php");
    exit();
}

// Incluyo la clase Prestamo (ya valida las reglas de negocio y usa PDO con prepared statements)
require_once __DIR__ . "/../clases/Prestamo.php";


// Recupero los datos enviados desde el formulario.
// A diferencia del funcionario (que solicita para sí mismo), acá el
// administrador elige explícitamente a qué funcionario le presta el equipo.
$idEquipo = $_POST['id_equipo'];
$idFuncionario = $_POST['id_funcionario'];
$fechaPrestamo = $_POST['fecha_prestamo'];
$fechaDevolucionPrevista = $_POST['fecha_devolucion_prevista'];
$observaciones = $_POST['observaciones'];

$prestamo = new Prestamo($idEquipo, $idFuncionario, $fechaPrestamo, $fechaDevolucionPrevista, $observaciones);

// procesarPrestamo() valida las reglas de negocio (fechas, máximo de préstamos
// activos, disponibilidad del equipo, etc.) y devuelve los errores si las hay
$errores = $prestamo->procesarPrestamo();

if (empty($errores)) {
    echo "<h3>Préstamo registrado correctamente.</h3>";
} else {
    echo "<h3>Error:</h3>";
    echo implode("<br>", array_map('htmlspecialchars', $errores));
}

?>
