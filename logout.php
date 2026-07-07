<?php
session_start();
session_unset();
session_destroy();
setcookie('ultimo_acceso', '', time() - 3600, '/');

// Ruta absoluta hacia login.php: logout.php puede ser incluido desde
// administrador/ o funcionario/ (vía validar_sesion.php), donde una
// ubicación relativa como "login.php" resolvería mal.
$rutaBase = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__));
$query = isset($motivoLogout) ? "?error=" . urlencode($motivoLogout) : "";
header("Location: " . $rutaBase . "/login.php" . $query);
exit;

?>