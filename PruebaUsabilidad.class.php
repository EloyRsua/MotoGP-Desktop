<?php
/**
 * Clase PruebaUsabilidad
 * Gestiona la prueba de usabilidad y su almacenamiento en BD
 */
class PruebaUsabilidad {
    private $mysqli;
    private $dbName = 'UO298184_DB';
    private $user = 'DBUSER2025';
    private $password = 'DBPSWD2025';
    private $host = 'localhost';
    
    public function __construct() {
        $this->mysqli = new mysqli($this->host, $this->user, $this->password, $this->dbName);
        if ($this->mysqli->connect_error) {
            die('Error de Conexión (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
        }
        $this->mysqli->set_charset("utf8mb4");
    }
    
    public function __destruct() {
        if ($this->mysqli) {
            $this->mysqli->close();
        }
    }
    
    /**
     * Guarda el usuario en la base de datos
     */
    public function guardarUsuario($idUsuario, $profesion, $edad, $genero, $idPericia) {
        // Verificar si el usuario ya existe
        $stmt = $this->mysqli->prepare("SELECT idUsuario FROM Usuario WHERE idUsuario = ?");
        $stmt->bind_param("s", $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $stmt->close();
            return true; // Usuario ya existe
        }
        $stmt->close();
        
        // Insertar nuevo usuario
        $stmt = $this->mysqli->prepare(
            "INSERT INTO Usuario (idUsuario, profesion, edad, genero, idPericia) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssisi", $idUsuario, $profesion, $edad, $genero, $idPericia);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }
    
    /**
     * Obtiene los niveles de pericia disponibles
     */
    public function obtenerPericias() {
        $result = $this->mysqli->query("SELECT idPericia, nivelPericia FROM PericiaInformatica ORDER BY idPericia");
        $pericias = [];
        while ($row = $result->fetch_assoc()) {
            $pericias[] = $row;
        }
        return $pericias;
    }
    
    /**
     * Guarda los resultados de la prueba en la base de datos
     */
    public function guardarPrueba($idUsuario, $dispositivo, $tiempoTotal, $tareaCompletada, $comentariosUsuario, $propuestasMejora, $valoracion) {
        $stmt = $this->mysqli->prepare(
            "INSERT INTO Prueba (idUsuario, dispositivo, tiempoTotal, tareaCompletada, comentariosUsuario, propuestasMejora, valoracion) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        
        $stmt->bind_param("ssdiisi", $idUsuario, $dispositivo, $tiempoTotal, $tareaCompletada, $comentariosUsuario, $propuestasMejora, $valoracion);
        
        if ($stmt->execute()) {
            $pruebaId = $this->mysqli->insert_id;
            $stmt->close();
            return $pruebaId;
        } else {
            $stmt->close();
            return false;
        }
    }
    
    /**
     * Guarda observaciones del facilitador
     */
    public function guardarObservacion($idPrueba, $comentariosFacilitador) {
        $stmt = $this->mysqli->prepare(
            "INSERT INTO Observacion (idPrueba, comentariosFacilitador) VALUES (?, ?)"
        );
        $stmt->bind_param("is", $idPrueba, $comentariosFacilitador);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }
}
?>