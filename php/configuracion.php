<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
/**
 * Clase Configuracion (POO)
 * Gestiona el mantenimiento de la base de datos UO298184_DB.
 */

class Configuracion {
    
    private $mysqli;
    private $dbName = 'UO298184_DB';
    private $user = 'DBUSER2025';
    private $password = 'DBPSWD2025';
    private $host = 'localhost';

    public function __construct() {
        // Conexión sin seleccionar BD para permitir operaciones como DROP DATABASE.
        $this->mysqli = new mysqli($this->host, $this->user, $this->password);

        if ($this->mysqli->connect_error) {
            die('Error de Conexión (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
        }
    }

    public function __destruct() {
        if ($this->mysqli) {
            $this->mysqli->close();
        }
    }

    private function ejecutarConsulta($sql) {
        return $this->mysqli->query($sql);
    }

    /**
     * Reinicia la Base de Datos, borrando todos los datos.
     */
    public function reiniciarBaseDeDatos() {
        if (!$this->mysqli->select_db($this->dbName)) {
            return "Error: La base de datos no existe para reiniciar.";
        }

        $tablas = ['Observacion', 'Prueba', 'Usuario', 'PericiaInformatica'];
        $this->ejecutarConsulta("SET FOREIGN_KEY_CHECKS = 0;");
        
        $mensaje = "Base de datos'$this->dbName'reiniciada:<br>";
        
        foreach ($tablas as $tabla) {
            $sql = "TRUNCATE TABLE $tabla";
            if (!$this->ejecutarConsulta($sql)) {
                $mensaje .= "- Error al truncar la tabla $tabla.<br>";
            }
        }
        
        $this->ejecutarConsulta("INSERT INTO PericiaInformatica (nivelPericia) VALUES ('Principiante'), ('Intermedio'), ('Avanzado'), ('Experto');");
        $this->ejecutarConsulta("SET FOREIGN_KEY_CHECKS = 1;");
        return $mensaje;
    }

    /**
     * Elimina la base de datos completa.
     */
    public function eliminarBaseDeDatos() {
        $sql = "DROP DATABASE IF EXISTS $this->dbName";
        if ($this->ejecutarConsulta($sql)) {
            return "Base de datos '$this->dbName' y todos sus datos han sido ELIMINADOS correctamente.";
        } else {
            return "Error al eliminar la base de datos: " . $this->mysqli->error;
        }
    }

    /**
     * Exporta los datos de la tabla Prueba en formato CSV.
     */
    public function exportarDatosCSV() {
        if (!$this->mysqli->select_db($this->dbName)) {
            echo "Error: La base de datos no existe para exportar.";
            return;
        }
        
        $tabla = 'Prueba';
        $csv = '';
        
        // Obtener encabezados
        $campos = $this->mysqli->query("SHOW COLUMNS FROM $tabla");
        $header = [];
        while ($campo = $campos->fetch_assoc()) {
            $header[] = $campo['Field'];
        }
        $csv .= implode(";", $header) . "\n";

        // Obtener datos
        $datos = $this->mysqli->query("SELECT * FROM $tabla");
        
        if ($datos === FALSE) {
            echo "Error al consultar la tabla $tabla.";
            return;
        }

        while ($fila = $datos->fetch_assoc()) {
            $linea = array_map(function($value) {
                return '"' . str_replace('"', '""', $value) . '"'; 
            }, array_values($fila));
            $csv .= implode(";", $linea) . "\n";
        }

        // Envío del archivo CSV al navegador
        $fileName = "resultados_test_" . date("Ymd_His") . ".csv";
        header('Content-Type: text/csv');
        header('Content-disposition: attachment; filename=' . $fileName);
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo $csv;
        exit;
    }
}
?>