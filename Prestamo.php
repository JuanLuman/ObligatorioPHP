<?php

class Prestamo
{
    private $idPrestamo;
    private $idEquipo;
    private $idFuncionario;
    private $fechaPrestamo;
    private $fechaDevolucionPrevista;
    private $fechaDevolucionReal;
    private $observaciones;
    private $fechaCreacion;


    public function __construct(
        $idEquipo, $idFuncionario, $fechaPrestamo, $fechaDevolucionPrevista, $observaciones = "") {
        $this->idEquipo = $idEquipo;
        $this->idFuncionario = $idFuncionario;
        $this->fechaPrestamo = $fechaPrestamo;
        $this->fechaDevolucionPrevista = $fechaDevolucionPrevista;
        $this->observaciones = $observaciones;
        $this->fechaCreacion = date('Y-m-d H:i:s');
        $this->fechaDevolucionReal = null;
    }


    /* ==================== GETTERS ==================== */

    public function getIdPrestamo() { return $this->idPrestamo; }
    public function getEquipo() { return $this->idEquipo; }
    public function getFuncionario() { return $this->idFuncionario; }
    public function getFechaPrestamo() { return $this->fechaPrestamo; }
    public function getFechaDevolucionPrevista() { return $this->fechaDevolucionPrevista; }
    public function getFechaDevolucionReal() { return $this->fechaDevolucionReal; }
    public function getObservaciones() { return $this->observaciones; }
    public function getFechaCreacion() { return $this->fechaCreacion; }

    /* ==================== SETTERS ==================== */

    public function setIdPrestamo($idPrestamo) { $this->idPrestamo = $idPrestamo; }
    public function setEquipo($idEquipo) { $this->idEquipo = $idEquipo; }
    public function setFuncionario($idFuncionario) { $this->idFuncionario = $idFuncionario; }
    public function setFechaPrestamo($fechaPrestamo) { $this->fechaPrestamo = $fechaPrestamo; }
    public function setFechaDevolucionPrevista($fecha) { $this->fechaDevolucionPrevista = $fecha; }
    public function setFechaDevolucionReal($fecha) { $this->fechaDevolucionReal = $fecha; }
    public function setObservaciones($observaciones) { $this->observaciones = $observaciones; }



    public function registrarPrestamo()
{
    require_once "Conexion.php";

    $conexion = new ConexionBD();
    $conexion->conectar();

    // Validación de fechas
    if ($this->fechaPrestamo >= $this->fechaDevolucionPrevista)
    {
        $conexion->cerrarConexion();
        return "La fecha de devolución prevista debe ser posterior a la fecha del préstamo.";
    }

    $consulta = "INSERT INTO prestamos
                 (id_equipo, id_funcionario, fecha_prestamo,
                  fecha_devolucion_prevista, observaciones)
                 VALUES
                 ($this->idEquipo,
                  $this->idFuncionario,
                  '$this->fechaPrestamo',
                  '$this->fechaDevolucionPrevista',
                  '$this->observaciones')";

    $resultado = $conexion->ejecutarConsulta($consulta);

    if ($resultado)
    {
        // Actualizar el estado del equipo
        $consulta = "UPDATE equipos
                     SET estado='Prestado'
                     WHERE id_equipo=$this->idEquipo";

        $conexion->ejecutarConsulta($consulta);

        $conexion->cerrarConexion();
        return true;
    }

    $conexion->cerrarConexion();

    return "No fue posible registrar el préstamo.";
}
?>
