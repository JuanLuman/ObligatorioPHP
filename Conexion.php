<?php
// establecer la conexión a la base de datos en una clase para poder reutilizarla en otros archivos

class ConexionBD {
    private $localhost = "localhost";
    private $usuario = "root";
    private $password = "";
    private $base = "obligatorio2026";
    private $conn = null;


public function conectar() {
    // crear la conexión
    $this->conn = mysqli_connect($this->localhost, $this->usuario, $this->password, $this->base);
}


// getter y setter para $base
public function getBase() {
    return $this->base;
}

public function setBase($base) {
    $this->base = $base;
}




// ejecutar una consulta
public function ejecutarConsulta($consulta) {
    if ($this->conn === null) { // si no hay conexión, la creo
        $this->conectar();
    }
    $resultado = mysqli_query($this->conn, $consulta);
    return $resultado;
}



// cerrar la conexion
public function cerrarConexion() {
    if ($this->conn !== null) {
        mysqli_close($this->conn);
    }
}

}

echo "Hola";



?>