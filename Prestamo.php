<?php

class Prestamo
{
    private $idPrestamo;
    private $equipo;
    private $funcionario;
    private $fechaPrestamo;
    private $fechaDevolucionPrevista;
    private $fechaDevolucionReal;
    private $observaciones;
    private $fechaCreacion;


    public function __construct(
        $equipo, $funcionario, $fechaPrestamo, $fechaDevolucionPrevista, $observaciones = "") {
        $this->equipo = $equipo;
        $this->funcionario = $funcionario;
        $this->fechaPrestamo = $fechaPrestamo;
        $this->fechaDevolucionPrevista = $fechaDevolucionPrevista;
        $this->observaciones = $observaciones;
        $this->fechaCreacion = date('Y-m-d H:i:s');
        $this->fechaDevolucionReal = null;
    }


    /* ==================== GETTERS ==================== */

    public function getIdPrestamo() { return $this->idPrestamo; }
    public function getEquipo() { return $this->equipo; }
    public function getFuncionario() { return $this->funcionario; }
    public function getFechaPrestamo() { return $this->fechaPrestamo; }
    public function getFechaDevolucionPrevista() { return $this->fechaDevolucionPrevista; }
    public function getFechaDevolucionReal() { return $this->fechaDevolucionReal; }
    public function getObservaciones() { return $this->observaciones; }
    public function getFechaCreacion() { return $this->fechaCreacion; }

    /* ==================== SETTERS ==================== */

    public function setIdPrestamo($idPrestamo) { $this->idPrestamo = $idPrestamo; }
    public function setEquipo($equipo) { $this->equipo = $equipo; }
    public function setFuncionario($funcionario) { $this->funcionario = $funcionario; }
    public function setFechaPrestamo($fechaPrestamo) { $this->fechaPrestamo = $fechaPrestamo; }
    public function setFechaDevolucionPrevista($fecha) { $this->fechaDevolucionPrevista = $fecha; }
    public function setFechaDevolucionReal($fecha) { $this->fechaDevolucionReal = $fecha; }
    public function setObservaciones($observaciones) { $this->observaciones = $observaciones; }






    
}
?>
