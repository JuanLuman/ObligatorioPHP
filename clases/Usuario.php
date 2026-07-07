<?php

require_once __DIR__ . "/../Conexion.php";

class Usuario {

    // constantes para los tipos de usuario
    const TIPO_ADMINISTRADOR = 'administrador';
    const TIPO_FUNCIONARIO   = 'funcionario';


    // id_usuario es la PK real segun el schema. ci es UNIQUE pero no PK.
    private $idUsuario;
    private $ci;
    private $primerNombre;
    private $segundoNombre;
    private $primerApellido;
    private $segundoApellido;
    private $fechaNacimiento;
    private $email;
    private $password;
    private $tipoUsuario;
    private $idSucursal;
    private $activo;


    public function __construct() {
        $this->idUsuario       = null;
        $this->ci              = null;
        $this->primerNombre    = "";
        $this->segundoNombre   = "";
        $this->primerApellido  = "";
        $this->segundoApellido = "";
        $this->fechaNacimiento = null;
        $this->email           = "";
        $this->password        = "";
        $this->tipoUsuario     = self::TIPO_FUNCIONARIO;
        $this->idSucursal      = null;
        $this->activo          = 1;
    }


    // getters
    public function getIdUsuario()       { return $this->idUsuario; }
    public function getCi()              { return $this->ci; }
    public function getPrimerNombre()    { return $this->primerNombre; }
    public function getSegundoNombre()   { return $this->segundoNombre; }
    public function getPrimerApellido()  { return $this->primerApellido; }
    public function getSegundoApellido() { return $this->segundoApellido; }
    public function getFechaNacimiento() { return $this->fechaNacimiento; }
    public function getEmail()           { return $this->email; }
    public function getPassword()        { return $this->password; }
    public function getTipoUsuario()     { return $this->tipoUsuario; }
    public function getIdSucursal()      { return $this->idSucursal; }
    public function getActivo()          { return $this->activo; }

    // setters
    public function setIdUsuario($id)        { $this->idUsuario = $id; }
    public function setCi($ci)               { $this->ci = $ci; }
    public function setPrimerNombre($n)      { $this->primerNombre = $n; }
    public function setSegundoNombre($n)     { $this->segundoNombre = $n; }
    public function setPrimerApellido($a)    { $this->primerApellido = $a; }
    public function setSegundoApellido($a)   { $this->segundoApellido = $a; }
    public function setFechaNacimiento($f)   { $this->fechaNacimiento = $f; }
    public function setEmail($e)             { $this->email = $e; }
    public function setTipoUsuario($t)       { $this->tipoUsuario = $t; }
    public function setIdSucursal($id)       { $this->idSucursal = $id; }
    public function setActivo($a)            { $this->activo = $a; }

    // password: se hashea al setear (consistente con el login del integrante 1 que usa md5)
    public function setPassword($pass) {
        $this->password = md5($pass);
    }

    // setea un password ya hasheado (lo usa cargar() para no re-hashear)
    public function setPasswordHash($hash) {
        $this->password = $hash;
    }



    // guardar: INSERT si el objeto no tiene id_usuario, UPDATE si ya lo tiene
    public function guardar() {
        $conexion = new ConexionBD();
        $conexion->conectar();

        if ($this->idUsuario === null) {
            $consulta = "INSERT INTO usuarios
                         (ci, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido,
                          fecha_nacimiento, email, password, tipo_usuario, id_sucursal, activo)
                         VALUES
                         ('$this->ci', '$this->primerNombre', '$this->segundoNombre',
                          '$this->primerApellido', '$this->segundoApellido',
                          '$this->fechaNacimiento', '$this->email', '$this->password',
                          '$this->tipoUsuario', $this->idSucursal, $this->activo)";
        } else {
            $consulta = "UPDATE usuarios SET
                            ci               = '$this->ci',
                            primer_nombre    = '$this->primerNombre',
                            segundo_nombre   = '$this->segundoNombre',
                            primer_apellido  = '$this->primerApellido',
                            segundo_apellido = '$this->segundoApellido',
                            fecha_nacimiento = '$this->fechaNacimiento',
                            email            = '$this->email',
                            password         = '$this->password',
                            tipo_usuario     = '$this->tipoUsuario',
                            id_sucursal      = $this->idSucursal,
                            activo           = $this->activo
                         WHERE id_usuario = $this->idUsuario";
        }

        $resultado = $conexion->ejecutarConsulta($consulta);
        $conexion->cerrarConexion();

        // si fue un INSERT y hace falta trabajar despues con el id_usuario asignado,
        // el consumidor puede volver a cargar el objeto llamando cargarPorCi($this->ci)
        return $resultado;
    }



    // cargar por id_usuario (PK real)
    public function cargar($idUsuario) {
        return $this->cargarPorCampo("id_usuario", $idUsuario, true);
    }

    // cargar por ci (util para el login y editar perfil que trabaja con ci en sesion)
    public function cargarPorCi($ci) {
        return $this->cargarPorCampo("ci", $ci, false);
    }

    // cargar por email (util para el proceso de login)
    public function cargarPorEmail($email) {
        return $this->cargarPorCampo("email", $email, false);
    }


    // helper interno usado por los distintos cargar*
    private function cargarPorCampo($campo, $valor, $esNumerico) {
        $conexion = new ConexionBD();
        $conexion->conectar();

        // los campos numericos van sin comillas, los string entre comillas
        $valorSql = $esNumerico ? $valor : "'" . $valor . "'";
        $consulta = "SELECT * FROM usuarios WHERE $campo = $valorSql";
        $resultado = $conexion->ejecutarConsulta($consulta);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $fila = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
            $this->llenarDesdeFila($fila);
            $conexion->cerrarConexion();
            return true;
        }

        $conexion->cerrarConexion();
        return false;
    }


    // llena los atributos del objeto desde una fila de la base
    private function llenarDesdeFila($fila) {
        $this->idUsuario       = $fila['id_usuario'];
        $this->ci              = $fila['ci'];
        $this->primerNombre    = $fila['primer_nombre'];
        $this->segundoNombre   = $fila['segundo_nombre'];
        $this->primerApellido  = $fila['primer_apellido'];
        $this->segundoApellido = $fila['segundo_apellido'];
        $this->fechaNacimiento = $fila['fecha_nacimiento'];
        $this->email           = $fila['email'];
        $this->password        = $fila['password'];
        $this->tipoUsuario     = $fila['tipo_usuario'];
        $this->idSucursal      = $fila['id_sucursal'];
        $this->activo          = $fila['activo'];
    }



    // eliminar: baja logica (marca activo = 0), no DELETE fisico
    public function eliminar() {
        if ($this->idUsuario === null) {
            return false;
        }
        $conexion = new ConexionBD();
        $conexion->conectar();
        $consulta = "UPDATE usuarios SET activo = 0 WHERE id_usuario = $this->idUsuario";
        $resultado = $conexion->ejecutarConsulta($consulta);
        $conexion->cerrarConexion();
        $this->activo = 0;
        return $resultado;
    }



    // autenticar: valida email + password y devuelve el usuario si matchea
    public static function autenticar($email, $passwordPlano) {
        $conexion = new ConexionBD();
        $conexion->conectar();

        $hash = md5($passwordPlano);
        $consulta = "SELECT * FROM usuarios
                     WHERE email = '$email' AND password = '$hash' AND activo = 1";
        $resultado = $conexion->ejecutarConsulta($consulta);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $fila = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
            $u = new Usuario();
            $u->llenarDesdeFila($fila);
            $conexion->cerrarConexion();
            return $u;
        }

        $conexion->cerrarConexion();
        return null;
    }



    // cambiar password: recibe el plano, lo hashea y guarda
    public function cambiarPassword($passwordPlano) {
        $this->setPassword($passwordPlano);
        return $this->guardar();
    }



    // listar todos los usuarios activos
    public static function listarTodos() {
        $conexion = new ConexionBD();
        $conexion->conectar();

        $consulta = "SELECT * FROM usuarios WHERE activo = 1 ORDER BY primer_apellido, primer_nombre";
        $resultado = $conexion->ejecutarConsulta($consulta);

        $lista = [];
        while ($fila = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
            $u = new Usuario();
            $u->llenarDesdeFila($fila);
            $lista[] = $u;
        }

        $conexion->cerrarConexion();
        return $lista;
    }

}

?>
