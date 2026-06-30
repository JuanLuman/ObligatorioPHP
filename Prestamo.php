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


public static function listarPrestamosActivos($idFuncionario)
{
    require_once "Conexion.php";

    $conexion = new ConexionBD();
    $conexion->conectar();

    $consulta = "SELECT
                    p.id_prestamo,
                    p.id_equipo,
                    p.id_funcionario,
                    p.fecha_prestamo,
                    p.fecha_devolucion_prevista,
                    e.codigo_inventario,
                    e.marca,
                    e.modelo
                 FROM prestamos p
                 INNER JOIN equipos e
                    ON p.id_equipo = e.id_equipo
                 WHERE p.id_funcionario = $idFuncionario
                   AND p.fecha_devolucion_real IS NULL
                 ORDER BY p.fecha_prestamo";

    $resultado = $conexion->ejecutarConsulta($consulta);

    $lista = array();

    while ($fila = mysqli_fetch_assoc($resultado))
    {
        $lista[] = $fila;
    }

    $conexion->cerrarConexion();

    return $lista;
}

// Registrar la devolución de un préstamo
public function registrarDevolucion()
{
    // Incluyo la clase de conexión
    require_once "Conexion.php";

    // Creo el objeto conexión
    $conexion = new ConexionBD();
    $conexion->conectar();


    // Registrar la fecha real de devolución
    $consulta = "
        UPDATE prestamos
        SET fecha_devolucion_real = CURDATE()
        WHERE id_prestamo = $this->idPrestamo
          AND fecha_devolucion_real IS NULL
    ";

    $resultado = $conexion->ejecutarConsulta($consulta);

    // Si no se pudo actualizar el préstamo
    if (!$resultado)
    {
        $conexion->cerrarConexion();
        return "No fue posible registrar la devolución.";
    }


    // Volver a dejar el equipo disponible

    $consulta = "
        UPDATE equipos
        SET estado = 'Disponible'
        WHERE id_equipo = $this->idEquipo
    ";

    $resultado = $conexion->ejecutarConsulta($consulta);

    // Cierro la conexión
    $conexion->cerrarConexion();

    // Verifico el resultado
    if ($resultado)
    {
        return true;
    }

    return "No fue posible actualizar el estado del equipo.";
}
}
?>
