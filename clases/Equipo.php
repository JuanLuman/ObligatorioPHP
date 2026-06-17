<?php

require_once __DIR__ . "/../Conexion.php";

class Equipo {

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
        $conexion = new ConexionBD();
        $conexion->conectar();

        if ($this->idEquipo === null) {
            $consulta = "INSERT INTO equipos
                         (codigo_inventario, marca, modelo, anio_adquisicion, valor_estimado, tipo, estado, id_sucursal, foto)
                         VALUES
                         ('$this->codigoInventario', '$this->marca', '$this->modelo',
                          $this->anioAdquisicion, $this->valorEstimado,
                          '$this->tipo', '$this->estado', $this->idSucursal, '$this->foto')";
        } else {
            $consulta = "UPDATE equipos SET
                            codigo_inventario = '$this->codigoInventario',
                            marca = '$this->marca',
                            modelo = '$this->modelo',
                            anio_adquisicion = $this->anioAdquisicion,
                            valor_estimado = $this->valorEstimado,
                            tipo = '$this->tipo',
                            estado = '$this->estado',
                            id_sucursal = $this->idSucursal,
                            foto = '$this->foto'
                         WHERE id_equipo = $this->idEquipo";
        }

        $resultado = $conexion->ejecutarConsulta($consulta);
        $conexion->cerrarConexion();
        return $resultado;
    }



    // cargar: trae los datos del equipo por id
    public function cargar($id) {
        $conexion = new ConexionBD();
        $conexion->conectar();

        $consulta = "SELECT * FROM equipos WHERE id_equipo = $id";
        $resultado = $conexion->ejecutarConsulta($consulta);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $fila = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
            $this->idEquipo         = $fila['id_equipo'];
            $this->codigoInventario = $fila['codigo_inventario'];
            $this->marca            = $fila['marca'];
            $this->modelo           = $fila['modelo'];
            $this->anioAdquisicion  = $fila['anio_adquisicion'];
            $this->valorEstimado    = $fila['valor_estimado'];
            $this->tipo             = $fila['tipo'];
            $this->estado           = $fila['estado'];
            $this->idSucursal       = $fila['id_sucursal'];
            $this->foto             = $fila['foto'];
            $conexion->cerrarConexion();
            return true;
        }

        $conexion->cerrarConexion();
        return false;
    }



    // eliminar el equipo
    public function eliminar() {
        if ($this->idEquipo === null) {
            return false;
        }
        $conexion = new ConexionBD();
        $conexion->conectar();
        $consulta = "DELETE FROM equipos WHERE id_equipo = $this->idEquipo";
        $resultado = $conexion->ejecutarConsulta($consulta);
        $conexion->cerrarConexion();
        return $resultado;
    }



    // cambia el estado del equipo y guarda
    public function cambiarEstado($nuevoEstado) {
        $this->estado = $nuevoEstado;
        return $this->guardar();
    }



    // listar todos los equipos
    public static function listarTodos() {
        $conexion = new ConexionBD();
        $conexion->conectar();

        $consulta = "SELECT * FROM equipos ORDER BY id_equipo";
        $resultado = $conexion->ejecutarConsulta($consulta);

        $lista = self::resultadoAArray($resultado);
        $conexion->cerrarConexion();
        return $lista;
    }



    // listar solo equipos disponibles de una sucursal (lo usa el funcionario al solicitar prestamo)
    public static function listarDisponibles($idSucursal) {
        $conexion = new ConexionBD();
        $conexion->conectar();

        $estado = self::ESTADO_DISPONIBLE;
        $consulta = "SELECT * FROM equipos
                     WHERE estado = '$estado' AND id_sucursal = $idSucursal
                     ORDER BY marca, modelo";
        $resultado = $conexion->ejecutarConsulta($consulta);

        $lista = self::resultadoAArray($resultado);
        $conexion->cerrarConexion();
        return $lista;
    }



    // helper privado para mapear filas a objetos Equipo
    private static function resultadoAArray($resultado) {
        $lista = [];
        while ($fila = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
            $e = new Equipo();
            $e->setIdEquipo($fila['id_equipo']);
            $e->setCodigoInventario($fila['codigo_inventario']);
            $e->setMarca($fila['marca']);
            $e->setModelo($fila['modelo']);
            $e->setAnioAdquisicion($fila['anio_adquisicion']);
            $e->setValorEstimado($fila['valor_estimado']);
            $e->setTipo($fila['tipo']);
            $e->setEstado($fila['estado']);
            $e->setIdSucursal($fila['id_sucursal']);
            $e->setFoto($fila['foto']);
            $lista[] = $e;
        }
        return $lista;
    }

}

?>
