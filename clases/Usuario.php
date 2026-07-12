<?php
session_start();

require_once __DIR__ . "/../conexion/Conexion.php"; 


class Usuario extends ConexionBD {

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

        // uso prepared statements (parametros con ?) para evitar inyeccion SQL,
        // ya que estos valores vienen de datos ingresados por el usuario
        $conn = $this->conectar();

        $check = $conn->prepare("SELECT ci FROM usuarios WHERE ci = ?");
        $check->execute([$this->ci]);
        $existe = $check->fetch() !== false;

        if (!$existe) {
            $stmt = $conn->prepare("INSERT INTO usuarios
                         (ci, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido,
                          fecha_nacimiento, email, password, foto, tipo_usuario, id_sucursal, activo)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $resultado = $stmt->execute([
                $this->ci, $this->primerNombre, $this->segundoNombre,
                $this->primerApellido, $this->segundoApellido,
                $this->fechaNacimiento, $this->email, $this->password,
                $this->foto, $this->tipoUsuario, $this->idSucursal, $this->activo,
            ]);
        } else {
            $stmt = $conn->prepare("UPDATE usuarios SET
                            primer_nombre    = ?,
                            segundo_nombre   = ?,
                            primer_apellido  = ?,
                            segundo_apellido = ?,
                            fecha_nacimiento = ?,
                            email            = ?,
                            password         = ?,
                            foto             = ?,
                            tipo_usuario     = ?,
                            id_sucursal      = ?,
                            activo           = ?
                         WHERE ci = ?");
            $resultado = $stmt->execute([
                $this->primerNombre, $this->segundoNombre,
                $this->primerApellido, $this->segundoApellido,
                $this->fechaNacimiento, $this->email, $this->password,
                $this->foto, $this->tipoUsuario, $this->idSucursal, $this->activo,
                $this->ci,
            ]);
        }

        $this->cerrarConexion();
        return $resultado;
    }



    // cargar: busca el usuario por ci y llena los atributos
    public function cargar($ci) {
        $conn = $this->conectar();
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE ci = ?");
        $stmt->execute([$ci]);
        $fila = $stmt->fetch();

        if ($fila) {
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

            $this->cerrarConexion();

            return true;
        }

        $this->cerrarConexion();
        return false;
    }



    // eliminar: baja logica (marca activo = 0), no DELETE fisico
    public function eliminar() {
        if ($this->ci === null) {
            return false;
        }

        $conn = $this->conectar();
        $stmt = $conn->prepare("UPDATE usuarios SET activo = 0 WHERE ci = ?");
        $resultado = $stmt->execute([$this->ci]);

        $this->cerrarConexion();
        $this->activo = 0;
        return $resultado;
    }



    // autenticar: verifica email + password (compara contra md5 igual que validacion_login)
    public function autenticar($email, $passwordPlano) {
        $conn = $this->conectar();

        $hash = md5($passwordPlano);
        $stmt = $conn->prepare("SELECT * FROM usuarios
                     WHERE email = ? AND password = ? AND activo = 1");
        $stmt->execute([$email, $hash]);
        $fila = $stmt->fetch();

        if ($fila) {
            $u = new Usuario();
            $u->cargar($fila['ci']);
            $this->cerrarConexion();
            return $u;
        }

        $this->cerrarConexion();
        return null;
    }



    // cambiar password: recibe el plano, lo hashea y guarda
    public function cambiarPassword($passwordPlano) {
        $this->setPassword($passwordPlano);
        return $this->guardar();
    }



    // listar todos los usuarios activos
    public function listarTodos() {
        $consulta = "SELECT * FROM usuarios WHERE activo = 1 ORDER BY primer_apellido, primer_nombre";
        $resultado = $this->ejecutarConsulta($consulta);

        $lista = [];
        while ($fila = $resultado->fetch()) { // utilizo fetch() en lugar de mysqli_fetch_array() para compatibilidad con PDO
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

        $this->cerrarConexion();
        return $lista;
    }




    //creo metodo para validar el tipo de usuario
    public function esFuncionario() {
        return $this->tipoUsuario === self::TIPO_FUNCIONARIO;
    }

}

?>
