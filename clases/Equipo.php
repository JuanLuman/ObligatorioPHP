<?php

require_once __DIR__ . "/../conexion/Conexion.php";
//equipo herreda de la conexion


class Equipo extends ConexionBD {

    // constantes para evitar typos en los estados
    const ESTADO_DISPONIBLE    = 'Disponible';
    const ESTADO_PRESTADO      = 'Prestado';
    const ESTADO_MANTENIMIENTO = 'Mantenimiento';
    const ESTADO_BAJA          = 'Baja';


    private $idEquipo;
    private $codigoInventario;
    private $marca;
    private $modelo;
    private $anioAdquisicion;
    private $valorEstimado;
    private $tipo;
    private $estado;
    private $idSucursal;
    private $foto;


    public function __construct() {
        $this->idEquipo         = null;
        $this->codigoInventario = "";
        $this->marca            = "";
        $this->modelo           = "";
        $this->anioAdquisicion  = null;
        $this->valorEstimado    = 0;
        $this->tipo             = "";
        $this->estado           = self::ESTADO_DISPONIBLE;
        $this->idSucursal       = null;
        $this->foto             = "";
    }


    // getters
    public function getIdEquipo()         { return $this->idEquipo; }
    public function getCodigoInventario() { return $this->codigoInventario; }
    public function getMarca()            { return $this->marca; }
    public function getModelo()           { return $this->modelo; }
    public function getAnioAdquisicion()  { return $this->anioAdquisicion; }
    public function getValorEstimado()    { return $this->valorEstimado; }
    public function getTipo()             { return $this->tipo; }
    public function getEstado()           { return $this->estado; }
    public function getIdSucursal()       { return $this->idSucursal; }
    public function getFoto()             { return $this->foto; }

    // setters
    public function setIdEquipo($id)            { $this->idEquipo = $id; }
    public function setCodigoInventario($c)     { $this->codigoInventario = $c; }
    public function setMarca($m)                { $this->marca = $m; }
    public function setModelo($m)               { $this->modelo = $m; }
    public function setAnioAdquisicion($a)      { $this->anioAdquisicion = $a; }
    public function setValorEstimado($v)        { $this->valorEstimado = $v; }
    public function setTipo($t)                 { $this->tipo = $t; }
    public function setEstado($e)               { $this->estado = $e; }
    public function setIdSucursal($id)          { $this->idSucursal = $id; }
    public function setFoto($f)                 { $this->foto = $f; }



    // guardar: INSERT si no tiene id, UPDATE si tiene
    public function guardar() {
        $conn = $this->conectar();

        if ($this->idEquipo === null) {
            $stmt = $conn->prepare("INSERT INTO equipos
                         (codigo_inventario, marca, modelo, anio_adquisicion, valor_estimado, tipo_equipo, estado, id_sucursal, foto)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $resultado = $stmt->execute([
                $this->codigoInventario, $this->marca, $this->modelo,
                $this->anioAdquisicion, $this->valorEstimado,
                $this->tipo, $this->estado, $this->idSucursal, $this->foto,
            ]);
        } else {
            $stmt = $conn->prepare("UPDATE equipos SET
                            codigo_inventario = ?,
                            marca = ?,
                            modelo = ?,
                            anio_adquisicion = ?,
                            valor_estimado = ?,
                            tipo_equipo = ?,
                            estado = ?,
                            id_sucursal = ?,
                            foto = ?
                         WHERE id_equipo = ?");
            $resultado = $stmt->execute([
                $this->codigoInventario, $this->marca, $this->modelo,
                $this->anioAdquisicion, $this->valorEstimado,
                $this->tipo, $this->estado, $this->idSucursal, $this->foto,
                $this->idEquipo,
            ]);
        }

        $this->cerrarConexion();
        return $resultado;
    }



    // cargar: trae los datos del equipo por id
    public function cargar($id) {
        $conn = $this->conectar();
        $stmt = $conn->prepare("SELECT * FROM equipos WHERE id_equipo = ?");
        $stmt->execute([$id]);
        $fila = $stmt->fetch();

        if ($fila) {
            $this->idEquipo         = $fila['id_equipo'];
            $this->codigoInventario = $fila['codigo_inventario'];
            $this->marca            = $fila['marca'];
            $this->modelo           = $fila['modelo'];
            $this->anioAdquisicion  = $fila['anio_adquisicion'];
            $this->valorEstimado    = $fila['valor_estimado'];
            $this->tipo             = $fila['tipo_equipo'];
            $this->estado           = $fila['estado'];
            $this->idSucursal       = $fila['id_sucursal'];
            $this->foto             = $fila['foto'];

            $this->cerrarConexion();
            return true;
        }

        $this->cerrarConexion();
        return false;
    }



    // eliminar el equipo
    public function eliminar() {
        if ($this->idEquipo === null) {
            return false;
        }

        $conn = $this->conectar();
        $stmt = $conn->prepare("DELETE FROM equipos WHERE id_equipo = ?");
        $resultado = $stmt->execute([$this->idEquipo]);

        $this->cerrarConexion();
        return $resultado;
    }



    // cambia el estado del equipo y guarda
    public function cambiarEstado($nuevoEstado) {
        $this->estado = $nuevoEstado;
        return $this->guardar();
    }



    // listar todos los equipos
    public function listarTodos() {
        $consulta = "SELECT * FROM equipos ORDER BY id_equipo";
        $resultado = $this->ejecutarConsulta($consulta);

        $lista = self::resultadoAArray($resultado);
        $this->cerrarConexion();
        return $lista;
    }



    // listar solo equipos disponibles de una sucursal (lo usa el funcionario al solicitar prestamo)
    public function listarDisponibles($idSucursal) {
        $conn = $this->conectar();
        $stmt = $conn->prepare("SELECT * FROM equipos
                     WHERE estado = ? AND id_sucursal = ?
                     ORDER BY marca, modelo");
        $stmt->execute([self::ESTADO_DISPONIBLE, $idSucursal]);

        $lista = self::resultadoAArray($stmt);
        $this->cerrarConexion();
        return $lista;
    }



    // listar equipos por estado (lo usa el administrador para ver prestados/en mantenimiento/etc)
    public function obtenerPorEstado($estado) {
        $conn = $this->conectar();
        $stmt = $conn->prepare("SELECT * FROM equipos WHERE estado = ? ORDER BY marca, modelo");
        $stmt->execute([$estado]);

        $lista = $stmt->fetchAll();
        $this->cerrarConexion();
        return $lista;
    }



    // equipos con un prestamo activo cuya fecha de devolucion prevista ya paso
    public function obtenerVencidos() {
        $conn = $this->conectar();
        $stmt = $conn->prepare(
            "SELECT e.* FROM equipos e
             INNER JOIN prestamos p ON p.id_equipo = e.id_equipo
             WHERE p.fecha_devolucion_real IS NULL
               AND p.fecha_devolucion_prevista < CURDATE()
             ORDER BY p.fecha_devolucion_prevista"
        );
        $stmt->execute();

        $lista = $stmt->fetchAll();
        $this->cerrarConexion();
        return $lista;
    }



    // helper privado para mapear filas a objetos Equipo
    private static function resultadoAArray($resultado) {
        $lista = [];
        
        while ($fila = $resultado->fetch()) {
            $e = new Equipo();
            $e->setIdEquipo($fila['id_equipo']);
            $e->setCodigoInventario($fila['codigo_inventario']);
            $e->setMarca($fila['marca']);
            $e->setModelo($fila['modelo']);
            $e->setAnioAdquisicion($fila['anio_adquisicion']);
            $e->setValorEstimado($fila['valor_estimado']);
            $e->setTipo($fila['tipo_equipo']);
            $e->setEstado($fila['estado']);
            $e->setIdSucursal($fila['id_sucursal']);
            $e->setFoto($fila['foto']);
            $lista[] = $e;
        }
        return $lista;
    }

}

?>
