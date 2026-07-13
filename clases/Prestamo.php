<?php

require_once __DIR__ . "/../conexion/Conexion.php";

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


    /* ==================== LÓGICA DE ESTADO ==================== */

    private static function calcularEstado($fechaDevolucionPrevista) {
        $hoy = new DateTime();
        $hoy->setTime(0, 0, 0);
        $fechaPrevista = new DateTime($fechaDevolucionPrevista);

        if ($hoy > $fechaPrevista) {
            return "Vencido";
        }

        $diasRestantes = $hoy->diff($fechaPrevista)->days;
        if ($diasRestantes <= 3) {
            return "Próximo a vencer";
        }

        return "En fecha";
    }


    /* ==================== CONSULTAS ====================
       Estas tienen valores variables (ci, id), así que preparamos y
       ejecutamos directo sobre la conexión PDO cruda, sin pasar por
       ejecutarConsulta() (que solo sirve para SQL fijo). */

    public static function obtenerPrestamosActivos($ciFuncionario) {
        $conn = (new ConexionBD())->conectar();

        $sql = "SELECT p.id_prestamo,
                       p.id_equipo,
                       p.fecha_prestamo,
                       p.fecha_devolucion_prevista,
                       p.fecha_devolucion_real,
                       e.foto,
                       e.codigo_inventario,
                       e.marca,
                       e.modelo
                FROM prestamos p
                INNER JOIN equipos e ON p.id_equipo = e.id_equipo
                WHERE p.id_funcionario = ?
                  AND p.fecha_devolucion_real IS NULL";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$ciFuncionario]);

        $prestamos = $stmt->fetchAll();
        foreach ($prestamos as &$fila) {
            $fila['estado'] = self::calcularEstado($fila['fecha_devolucion_prevista']);
        }

        return $prestamos;
    }

    public static function obtenerHistorialPorFuncionario($ciFuncionario) {
        $conn = (new ConexionBD())->conectar();

        $sql = "SELECT p.id_prestamo,
                       p.fecha_prestamo,
                       p.fecha_devolucion_prevista,
                       p.fecha_devolucion_real,
                       e.foto,
                       e.codigo_inventario,
                       e.marca,
                       e.modelo
                FROM prestamos p
                INNER JOIN equipos e ON p.id_equipo = e.id_equipo
                WHERE p.id_funcionario = ?
                ORDER BY p.fecha_devolucion_prevista DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$ciFuncionario]);

        $prestamos = $stmt->fetchAll();
        foreach ($prestamos as &$fila) {
            $fila['estado'] = $fila['fecha_devolucion_real'] !== null
                ? "Devuelto"
                : self::calcularEstado($fila['fecha_devolucion_prevista']);
        }

        return $prestamos;
    }

    // todos los prestamos activos, de cualquier funcionario (lo usa el administrador
    // para poder registrar la devolucion de cualquiera, no solo la propia)
    public static function obtenerTodosActivos() {
        $conexion = new ConexionBD();

        $sql = "SELECT p.id_prestamo,
                       p.id_equipo,
                       p.fecha_prestamo,
                       p.fecha_devolucion_prevista,
                       u.ci,
                       u.primer_nombre,
                       u.primer_apellido,
                       e.codigo_inventario,
                       e.marca,
                       e.modelo
                FROM prestamos p
                INNER JOIN equipos e ON p.id_equipo = e.id_equipo
                INNER JOIN usuarios u ON p.id_funcionario = u.ci
                WHERE p.fecha_devolucion_real IS NULL
                ORDER BY p.fecha_devolucion_prevista";

        $stmt = $conexion->ejecutarConsulta($sql);

        $prestamos = $stmt->fetchAll();
        foreach ($prestamos as &$fila) {
            $fila['estado'] = self::calcularEstado($fila['fecha_devolucion_prevista']);
        }

        return $prestamos;
    }

    // Sin valores variables: puede usar ejecutarConsulta() directo
    public static function obtenerHistorialCompleto() {
        $conexion = new ConexionBD();

        $sql = "SELECT p.id_prestamo,
                       p.fecha_prestamo,
                       p.fecha_devolucion_prevista,
                       p.fecha_devolucion_real,
                       u.primer_nombre,
                       u.primer_apellido,
                       e.codigo_inventario,
                       e.marca,
                       e.modelo
                FROM prestamos p
                INNER JOIN equipos e ON p.id_equipo = e.id_equipo
                INNER JOIN usuarios u ON p.id_funcionario = u.ci
                ORDER BY p.fecha_devolucion_prevista DESC";

        $stmt = $conexion->ejecutarConsulta($sql);

        $prestamos = $stmt->fetchAll();
        foreach ($prestamos as &$fila) {
            $fila['estado'] = $fila['fecha_devolucion_real'] !== null
                ? "Devuelto"
                : self::calcularEstado($fila['fecha_devolucion_prevista']);
        }

        return $prestamos;
    }

    public static function obtenerDetalle($idPrestamo) {
        $conn = (new ConexionBD())->conectar();

        $sql = "SELECT p.*,
                       u.primer_nombre, u.primer_apellido,
                       e.codigo_inventario, e.marca, e.modelo, e.foto
                FROM prestamos p
                INNER JOIN equipos e ON p.id_equipo = e.id_equipo
                INNER JOIN usuarios u ON p.id_funcionario = u.ci
                WHERE p.id_prestamo = ?";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$idPrestamo]);
        $fila = $stmt->fetch();

        if ($fila) {
            $fila['estado'] = $fila['fecha_devolucion_real'] !== null
                ? "Devuelto"
                : self::calcularEstado($fila['fecha_devolucion_prevista']);
        }

        return $fila; // false si no existe
    }

    public static function contarPrestamosActivos($ciFuncionario) {
        $conn = (new ConexionBD())->conectar();

        $stmt = $conn->prepare(
            "SELECT COUNT(*) AS total FROM prestamos
             WHERE id_funcionario = ? AND fecha_devolucion_real IS NULL"
        );
        $stmt->execute([$ciFuncionario]);

        return (int) $stmt->fetch()['total'];
    }

    private static function equipoTienePrestamoActivo($idEquipo) {
        $conn = (new ConexionBD())->conectar();

        $stmt = $conn->prepare(
            "SELECT COUNT(*) AS total FROM prestamos
             WHERE id_equipo = ? AND fecha_devolucion_real IS NULL"
        );
        $stmt->execute([$idEquipo]);

        return (int) $stmt->fetch()['total'] > 0;
    }


    /* ==================== ALTA Y DEVOLUCIÓN ==================== */

    public function procesarPrestamo() {
        $errores = $this->validarReglasNegocio();
        if (!empty($errores)) {
            return $errores;
        }

        $conn = (new ConexionBD())->conectar();

        $sql = "INSERT INTO prestamos
                    (id_equipo, id_funcionario, fecha_prestamo, fecha_devolucion_prevista, observaciones, created_at)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $this->equipo,
            $this->funcionario,
            $this->fechaPrestamo,
            $this->fechaDevolucionPrevista,
            $this->observaciones,
            $this->fechaCreacion,
        ]);

        $this->idPrestamo = $conn->lastInsertId();

        // El equipo pasa a estado "Prestado"
        require_once __DIR__ . '/Equipo.php';
        $equipo = new Equipo();
        if ($equipo->cargar($this->equipo)) {
            $equipo->cambiarEstado(Equipo::ESTADO_PRESTADO);
        }

        return [];
    }

    public static function registrarDevolucion($idPrestamo) {
        $conn = (new ConexionBD())->conectar();

        $stmt = $conn->prepare(
            "SELECT id_equipo FROM prestamos WHERE id_prestamo = ? AND fecha_devolucion_real IS NULL"
        );
        $stmt->execute([$idPrestamo]);
        $fila = $stmt->fetch();

        if (!$fila) {
            return ["El préstamo no existe o ya fue devuelto"];
        }

        $stmt = $conn->prepare("UPDATE prestamos SET fecha_devolucion_real = ?, estado = 'devuelto' WHERE id_prestamo = ?");
        $stmt->execute([date('Y-m-d'), $idPrestamo]);

        require_once __DIR__ . '/Equipo.php';
        $equipo = new Equipo();
        if ($equipo->cargar($fila['id_equipo'])) {
            $equipo->cambiarEstado(Equipo::ESTADO_DISPONIBLE);
        }

        return [];
    }

    private function validarReglasNegocio() {
        $errores = [];

        if (self::contarPrestamosActivos($this->funcionario) >= 3) {
            $errores[] = "El funcionario ya tiene 3 préstamos activos";
        }

        if (self::equipoTienePrestamoActivo($this->equipo)) {
            $errores[] = "El equipo ya está asociado a un préstamo activo";
        }

        if (strtotime($this->fechaDevolucionPrevista) <= strtotime($this->fechaPrestamo)) {
            $errores[] = "La fecha de devolución prevista debe ser posterior a la fecha de préstamo";
        }

        require_once __DIR__ . '/Equipo.php';
        $equipo = new Equipo();
        if ($equipo->cargar($this->equipo)) {
            if ($equipo->getEstado() === Equipo::ESTADO_MANTENIMIENTO) {
                $errores[] = "No se puede solicitar un equipo en mantenimiento";
            }
            if ($equipo->getEstado() === Equipo::ESTADO_BAJA) {
                $errores[] = "No se puede solicitar un equipo dado de baja";
            }
        } else {
            $errores[] = "El equipo indicado no existe";
        }

        return $errores;
    }
}

?>