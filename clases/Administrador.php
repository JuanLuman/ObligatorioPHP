<?php

class Administrador extends Usuario
{

    // Constructor de la clase Administrador
    public function __construct($id_usuario, $primer_nombre, $primer_apellido, $email, $password, $foto) {
        parent::__construct($id_usuario, $primer_nombre, $primer_apellido, $email, $password, $foto); 
    }

    // metodo propio AltaSucursal: permite al administrador dar de alta una nueva sucursal
    public function AltaSucursal($nombre, $direccion, $telefono) {
        // Lógica para dar de alta una nueva sucursal
        // Aquí puedes implementar la lógica para insertar la sucursal en la base de datos
    }

    // metodo propio altaEquipo: permite al administrador dar de alta un nuevo equipo
    public function altaEquipo(){}

    // metodo propio altausuario: permite al administrador dar de alta un nuevo usuario
    public function altaUsuario(){}

    // metodo registrarPrestamo: permite al administrador registrar un prestamo de un equipo a un funcionario
    public function registrarPrestamo(){}

    // metodo registrarDevolucion: permite al administrador registrar la devolucion de un equipo prestado a un funcionario
    public function registrarDevolucion(){}

    // metodo obtenerHistorialCompletoPrestamos: permite al administrador obtener el historial completo de prestamos de todos los funcionarios
    public function obtenerHistorialCompletoPrestamos(){}

    // metodo generarReportePrestamos: permite al administrador generar un reporte de prestamos por rango de fechas
    public function generarReportePrestamos($tipoReporte){}
    

    







}
?>