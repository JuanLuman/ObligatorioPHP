<?php

class Funcionario extends Usuario{

    // Constructor de la clase Funcionario (sin parametros, igual que Usuario: se cargan datos con setters o con cargar())
    public function __construct() {
        parent::__construct();
        $this->setTipoUsuario(self::TIPO_FUNCIONARIO);
    }

    //metodo obtenerPrestamosActivos: obtiene los prestamos activos del funcionario
    public function obtenerPrestamosActivos(){}

    //metodo obtenerHistorialPrestamos: obtiene el historial de prestamos del funcionario
    public function obtenerHistorialPrestamos(){}

    //metodo contarPrestamosActivos: cuenta la cantidad de prestamos activos del funcionario
    public function contarPrestamosActivos(){} // para validar la regla de maximo 3 prestamos activos


















}
?>