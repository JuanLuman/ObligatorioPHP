<?php
// establecer la conexión a la base de datos en una clase para poder reutilizarla en otros archivos

class ConexionBD {
    private $localhost = "localhost";
    private $base = "techrent";
    private $usuario = "root";
    private $password = "";
    private $conn = null;


    // constructor
    public function __construct() {
        $this->conn = null;
    }


    //me conecto usando PDO
    public function conectarPDO() {
        $conn= new PDO("mysql:host=$this->localhost;dbname=$this->base", $this->usuario, $this->password);
        $conn>setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }


    // metodo para preparar una consulta en PDO
    public function prepararConsulta($consulta) {
        $conn  = $this->conectarPDO();
        $stmt = $conexion->prepare($consulta);
        return $stmt;
    }


    //metodo para ejecutar una consulta en PDO
    public function ejecutarConsulta($consulta) {
        if($this->conn === null) {
            $this->conectarPDO();
        }
        $stmt = $this->prepararConsulta($consulta);
        $stmt->execute();
        return $stmt;
    }





// cerrar la conexion
public function cerrarConexion() {
    if ($this->conn !== null) {
        mysqli_close($this->conn);
    }
}

}





?>