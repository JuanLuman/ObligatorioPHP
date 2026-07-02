<?php

class Funcionario extends Usuario{

    // Constructor de la clase Funcionario
    public function __construct($id_usuario, $primer_nombre, $primer_apellido, $email, $password, $foto) {
        parent::__construct($id_usuario, $primer_nombre, $primer_apellido, $email, $password, $foto);
    }

    //metodo solicitarPrestamo: solicita un prestamo de un equipo, recibe el id del equipo y la fecha de devolucion
    public function solicitarPrestamo($id_equipo, $fecha_devolucion){

    // llamo a al archivo solicitarPrestamo.php para mostrar el formulario de solicitud de prestamo
    require_once __DIR__ . '/../funcionario/solicitarPrestamo.php';
    }

    //metodo obtenerPrestamosActivos: obtiene los prestamos activos del funcionario
    public function obtenerPrestamosActivos(){}

    //metodo obtenerHistorialPrestamos: obtiene el historial de prestamos del funcionario
    public function obtenerHistorialPrestamos(){}

    //metodo contarPrestamosActivos: cuenta la cantidad de prestamos activos del funcionario
    public function contarPrestamosActivos(){} // para validar la regla de maximo 3 prestamos activos


















}
?>