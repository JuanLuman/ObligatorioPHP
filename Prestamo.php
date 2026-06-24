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



    //creo el metodo para obtener los prestamos activos del funcionario
    public function ObtenerPrestamosActivos($id_funcionario) {

        $conexion = new ConexionBD();
        $conexion->conectar();

        $consulta = "SELECT p.id_prestamo,
                            p.id_funcionario,
                            p.id_equipo,
                            p.fecha_prestamo,
                            p.fecha_devolucion_prevista,
                            p.fecha_devolucion_real,
                            p.estado,
                            e.foto,
                            e.codigo_inventario,
                            e.marca,
                            e.modelo
        FROM prestamos p
        INNER JOIN equipos e ON p.id_equipo = e.id_equipo
        WHERE id_funcionario = $id_funcionario AND P.estado = 'activo'";

        $resultado = $conexion->ejecutarConsulta($consulta);

        $conexion->cerrarConexion();
        return $resultado;
    }


   

    
}
?>
