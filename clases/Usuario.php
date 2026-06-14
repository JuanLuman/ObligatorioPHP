<?php

require_once __DIR__ . "/../Conexion.php";

class Usuario {

    // constantes para los tipos de usuario
    const TIPO_ADMINISTRADOR = 'administrador';
    const TIPO_FUNCIONARIO   = 'funcionario';


    private $ci;
    private $primerNombre;
    private $segundoNombre;
    private $primerApellido;
    private $segundoApellido;
    private $fechaNacimiento;
    private $email;
    private $password;
    private $foto;
    private $tipoUsuario;
    private $idSucursal;
    private $activo;


    public function __construct() {
        $this->ci              = null;
        $this->primerNombre    = "";
        $this->segundoNombre   = "";
        $this->primerApellido  = "";
        $this->segundoApellido = "";
        $this->fechaNacimiento = null;
        $this->email           = "";
        $this->password        = "";
        $this->foto            = "";
        $this->tipoUsuario     = self::TIPO_FUNCIONARIO;
        $this->idSucursal      = null;
        $this->activo          = 1;
    }


    // getters
    public function getCi()              { return $this->ci; }
    public function getPrimerNombre()    { return $this->primerNombre; }
    public function getSegundoNombre()   { return $this->segundoNombre; }
    public function getPrimerApellido()  { return $this->primerApellido; }
    public function getSegundoApellido() { return $this->segundoApellido; }
    public function getFechaNacimiento() { return $this->fechaNacimiento; }
    public function getEmail()           { return $this->email; }
    public function getPassword()        { return $this->password; }
    public function getFoto()            { return $this->foto; }
    public function getTipoUsuario()     { return $this->tipoUsuario; }
    public function getIdSucursal()      { return $this->idSucursal; }
    public function getActivo()          { return $this->activo; }

    // setters
    public function setCi($ci)               { $this->ci = $ci; }
    public function setPrimerNombre($n)      { $this->primerNombre = $n; }
    public function setSegundoNombre($n)     { $this->segundoNombre = $n; }
    public function setPrimerApellido($a)    { $this->primerApellido = $a; }
    public function setSegundoApellido($a)   { $this->segundoApellido = $a; }
    public function setFechaNacimiento($f)   { $this->fechaNacimiento = $f; }
    public function setEmail($e)             { $this->email = $e; }
    public function setFoto($f)              { $this->foto = $f; }
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



    // guardar: INSERT si es nuevo, UPDATE si ya existe
    public function guardar() {
        $conexion = new ConexionBD();
        $conexion->conectar();

        // detectar si ya existe en la base buscando por ci
        $check = $conexion->ejecutarConsulta("SELECT ci FROM usuarios WHERE ci = '$this->ci'");
        $existe = $check && mysqli_num_rows($check) > 0;

        if (!$existe) {
            $consulta = "INSERT INTO usuarios
                         (ci, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido,
                          fecha_nacimiento, email, password, foto, tipo_usuario, id_sucursal, activo)
                         VALUES
                         ('$this->ci', '$this->primerNombre', '$this->segundoNombre',
                          '$this->primerApellido', '$this->segundoApellido',
                          '$this->fechaNacimiento', '$this->email', '$this->password',
                          '$this->foto', '$this->tipoUsuario', $this->idSucursal, $this->activo)";
        } else {
            $consulta = "UPDATE usuarios SET
                            primer_nombre    = '$this->primerNombre',
                            segundo_nombre   = '$this->segundoNombre',
                            primer_apellido  = '$this->primerApellido',
                            segundo_apellido = '$this->segundoApellido',
                            fecha_nacimiento = '$this->fechaNacimiento',
                            email            = '$this->email',
                            password         = '$this->password',
                            foto             = '$this->foto',
                            tipo_usuario     = '$this->tipoUsuario',
                            id_sucursal      = $this->idSucursal,
                            activo           = $this->activo
                         WHERE ci = '$this->ci'";
        }

        $resultado = $conexion->ejecutarConsulta($consulta);
        $conexion->cerrarConexion();
        return $resultado;
    }



    // cargar: busca el usuario por ci y llena los atributos
    public function cargar($ci) {
        $conexion = new ConexionBD();
        $conexion->conectar();

        $consulta = "SELECT * FROM usuarios WHERE ci = '$ci'";
        $resultado = $conexion->ejecutarConsulta($consulta);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $fila = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
            $this->ci              = $fila['ci'];
            $this->primerNombre    = $fila['primer_nombre'];
            $this->segundoNombre   = $fila['segundo_nombre'];
            $this->primerApellido  = $fila['primer_apellido'];
            $this->segundoApellido = $fila['segundo_apellido'];
            $this->fechaNacimiento = $fila['fecha_nacimiento'];
            $this->email           = $fila['email'];
            $this->password        = $fila['password'];
            $this->foto            = $fila['foto'];
            $this->tipoUsuario     = $fila['tipo_usuario'];
            $this->idSucursal      = $fila['id_sucursal'];
            $this->activo          = $fila['activo'];
            $conexion->cerrarConexion();
            return true;
        }

        $conexion->cerrarConexion();
        return false;
    }



    // eliminar: baja logica (marca activo = 0), no DELETE fisico
    public function eliminar() {
        if ($this->ci === null) {
            return false;
        }
        $conexion = new ConexionBD();
        $conexion->conectar();
        $consulta = "UPDATE usuarios SET activo = 0 WHERE ci = '$this->ci'";
        $resultado = $conexion->ejecutarConsulta($consulta);
        $conexion->cerrarConexion();
        $this->activo = 0;
        return $resultado;
    }



    // autenticar: verifica email + password (compara contra md5 igual que validacion_login)
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
            $u->cargar($fila['ci']);
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
            $u->setCi($fila['ci']);
            $u->setPrimerNombre($fila['primer_nombre']);
            $u->setSegundoNombre($fila['segundo_nombre']);
            $u->setPrimerApellido($fila['primer_apellido']);
            $u->setSegundoApellido($fila['segundo_apellido']);
            $u->setFechaNacimiento($fila['fecha_nacimiento']);
            $u->setEmail($fila['email']);
            $u->setPasswordHash($fila['password']);
            $u->setFoto($fila['foto']);
            $u->setTipoUsuario($fila['tipo_usuario']);
            $u->setIdSucursal($fila['id_sucursal']);
            $u->setActivo($fila['activo']);
            $lista[] = $u;
        }

        $conexion->cerrarConexion();
        return $lista;
    }

}

?>
