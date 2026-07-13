<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_unset();
session_destroy();
setcookie('ultimo_acceso', '', time() - 3600, '/');

// Ruta absoluta hacia login.php: logout.php puede ser incluido desde
// administrador/ o funcionario/ (vía validar_sesion.php), donde una
// ubicación relativa como "login.php" resolvería mal.
// Normalizamos las barras antes de restar DOCUMENT_ROOT: en Windows, __DIR__
// usa '\' mientras que DOCUMENT_ROOT usa '/', y el str_replace no hacía match.
$dirActual = str_replace('\\', '/', __DIR__);
$documentRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$rutaBase = str_replace($documentRoot, '', $dirActual);
$query = isset($motivoLogout) ? "?error=" . urlencode($motivoLogout) : "";
header("Location: " . $rutaBase . "/login.php" . $query);
exit;

?>