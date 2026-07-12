<?php
// Conexión centralizada a la base de datos con PDO

class ConexionBD {
    private $host = "localhost";
    private $baseDatos = "obligatorio2026";
    private $usuario = "root";
    private $password = "";
    private $conn = null;

    public function __construct() {
        $this->conn = null;
    }

    // Conecta si todavía no hay conexión activa, y devuelve el objeto PDO.
    // Cada clase que la use hace su propio $conn->prepare(...) / ->execute([...])
    public function conectar() {
        if ($this->conn === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->baseDatos};charset=utf8mb4";
                $this->conn = new PDO($dsn, $this->usuario, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Error al conectar a la base de datos: " . $e->getMessage());
            }
        }
        return $this->conn;
    }

    // Ejecuta una consulta SIN parámetros dinámicos (ej: "SELECT * FROM sucursales ORDER BY nombre")
    public function ejecutarConsulta($consulta) {
        $conn = $this->conectar();
        return $conn->query($consulta); // devuelve un PDOStatement
        //no utilizo prepare/execute porque no hay parámetros dinámicos, y es más simple así
    }

    public function cerrarConexion() {
        $this->conn = null;
    }
}

?>