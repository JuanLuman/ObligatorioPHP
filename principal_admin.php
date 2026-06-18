<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'administrador') {
     header("Location: login.html?error=No tienes permisos para acceder a esta página");
     exit();
}























?>