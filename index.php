<?php
//Punto de entrada; redirige a login.php si no hay sesión activa, o al inicio.php correspondiente según el tipo de usuario.
session_start();

if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'funcionario') {
    header("Location: funcionario/inicio.php");
} else if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'administrador') {
    header("Location: administrador/inicio.php");
} else {
    header("Location: login.php");
}




?>