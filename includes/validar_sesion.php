<?php
session_start();

// Si no hay sesión iniciada, no puede estar acá
if (!isset($_SESSION['tipo_usuario'])) {
    header("Location: ../login.php ? error=sin_sesion");
    exit;
}

// Límite de inactividad según el rol (regla de negocio 10 del enunciado)
$limite = ($_SESSION['tipo_usuario'] === 'administrador') ? 3600 : 900;

$tiempoTranscurrido = time() - $_SESSION['ultimo_acceso'];

if ($tiempoTranscurrido > $limite) {
   //llamo a logout.php para destruir la sesión y redirigir a login.php con mensaje de error
    include_once __DIR__ . "/../logout.php";
    exit;
}

// Renovamos el contador de actividad y calculamos cuánto tiempo le queda
$_SESSION['ultimo_acceso'] = time();
$tiempoRestante = $limite - $tiempoTranscurrido;

// Mostrar el tiempo restante
echo "Tiempo de inactividad: " . gmdate("H:i:s", $tiempoRestante);




?>


