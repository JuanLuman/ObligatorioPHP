<?php
session_start();

// Si no hay sesión iniciada, no puede estar acá
if (!isset($_SESSION['tipo_usuario'])) {
    header("Location: ../login.php?error=sin_sesion");
    exit;
}

// Límite de inactividad según el rol (regla de negocio 10 del enunciado),
// controlado mediante una cookie ('ultimo_acceso') en lugar de la sesión.
$limite = ($_SESSION['tipo_usuario'] === 'administrador') ? 3600 : 900;

// Sin cookie no hay forma de saber hace cuánto fue el último acceso: se trata como expirada
if (!isset($_COOKIE['ultimo_acceso'])) {
    $motivoLogout = 'sesion_expirada';
    include_once __DIR__ . "/../logout.php";
    exit;
}

$tiempoTranscurrido = time() - (int) $_COOKIE['ultimo_acceso'];

if ($tiempoTranscurrido > $limite) {
   //llamo a logout.php para destruir la sesión y redirigir a login.php con mensaje de error
    $motivoLogout = 'sesion_expirada';
    include_once __DIR__ . "/../logout.php";
    exit;
}

// Renovamos la cookie de actividad y calculamos cuánto tiempo le queda
setcookie('ultimo_acceso', (string) time(), time() + $limite, '/');
$tiempoRestante = $limite - $tiempoTranscurrido;

// Mostrar el tiempo restante
echo "Tiempo de inactividad: " . gmdate("H:i:s", $tiempoRestante);




?>


