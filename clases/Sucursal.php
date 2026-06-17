<?php

require_once __DIR__ . "/../Conexion.php";

class Sucursal {

    private $idSucursal;
    private $nombre;
    private $direccion;
    private $telefono;


    public function __construct() {
        $this->idSucursal = null;
        $this->nombre = "";
        $this->direccion = "";
        $this->telefono = "";
    }


    // getters
    public function getIdSucursal() { return $this->idSucursal; }
    public function getNombre()     { return $this->nombre; }
    public function getDireccion()  { return $this->direccion; }
    public function getTelefono()   { return $this->telefono; }

    // setters
    public function setIdSucursal($id)   { $this->idSucursal = $id; }
    public function setNombre($n)        { $this->nombre = $n; }
    public function setDireccion($d)     { $this->direccion = $d; }
    public function setTelefono($t)      { $this->telefono = $t; }



    // guardar: si no tiene id, hace INSERT; si tiene, hace UPDATE
    public function guardar() {
        $conexion = new ConexionBD();
        $conexion->conectar();

        if ($this->idSucursal === null) {
            $consulta = "INSERT INTO sucursales (nombre, direccion, telefono)
                         VALUES ('$this->nombre', '$this->direccion', '$this->telefono')";
        } else {
            $consulta = "UPDATE sucursales
                         SET nombre = '$this->nombre',
                             direccion = '$this->direccion',
                             telefono = '$this->telefono'
                         WHERE id_sucursal = $this->idSucursal";
        }

        $resultado = $conexion->ejecutarConsulta($consulta);
        $conexion->cerrarConexion();
        return $resultado;
    }



    // cargar: trae los datos desde la base segun el id y los carga en el objeto
    public function cargar($id) {
        $conexion = new ConexionBD();
        $conexion->conectar();

        $consulta = "SELECT * FROM sucursales WHERE id_sucursal = $id";
        $resultado = $conexion->ejecutarConsulta($consulta);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $fila = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
            $this->idSucursal = $fila['id_sucursal'];
            $this->nombre    = $fila['nombre'];
            $this->direccion = $fila['direccion'];
            $this->telefono  = $fila['telefono'];
            $conexion->cerrarConexion();
            return true;
        }

        $conexion->cerrarConexion();
        return false;
    }



    // eliminar la sucursal actual (necesita id cargado)
    public function eliminar() {
        if ($this->idSucursal === null) {
            return false;
        }

        $conexion = new ConexionBD();
        $conexion->conectar();
        $consulta = "DELETE FROM sucursales WHERE id_sucursal = $this->idSucursal";
        $resultado = $conexion->ejecutarConsulta($consulta);
        $conexion->cerrarConexion();
        return $resultado;
    }



    // listarTodas: devuelve un array de objetos Sucursal con todas las sucursales
    public static function listarTodas() {
        $conexion = new ConexionBD();
        $conexion->conectar();

        $consulta = "SELECT * FROM sucursales ORDER BY nombre";
        $resultado = $conexion->ejecutarConsulta($consulta);

        $lista = [];
        while ($fila = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
            $s = new Sucursal();
            $s->setIdSucursal($fila['id_sucursal']);
            $s->setNombre($fila['nombre']);
            $s->setDireccion($fila['direccion']);
            $s->setTelefono($fila['telefono']);
            $lista[] = $s;
        }

        $conexion->cerrarConexion();
        return $lista;
    }

}

?>
