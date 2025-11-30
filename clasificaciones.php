<?php
/**
 * EJERCICIO 2: Lectura de archivos XML en el servidor con PHP
 */

class Clasificacion {
    private $documento;
    private $xml;

    public function __construct() {
        $this->documento = "xml/circuitoEsquema.xml";
    }

    public function consultar() {
        if (!file_exists($this->documento)) {
            echo "<p>Error: No se encuentra el archivo " . htmlspecialchars($this->documento) . "</p>";
            return false;
        }

        $this->xml = simplexml_load_file($this->documento);

        if ($this->xml === false) {
            echo "<p>Error: No se pudo cargar el archivo XML</p>";
            foreach(libxml_get_errors() as $error) {
                echo "<p>Error XML: " . htmlspecialchars($error->message) . "</p>";
            }
            return false;
        }

        $this->xml->registerXPathNamespace('ns', 'http://www.uniovi.es');
        return true;
    }

    public function mostrarGanador() {
        if ($this->xml === null) {
            return "<p>Error: Primero debe llamar al método consultar()</p>";
        }

        $vencedores = $this->xml->xpath("//ns:vencedor");
        if (empty($vencedores)) {
            return "<p>No se encontró información del ganador de la carrera</p>";
        }

        $nombreVencedor = (string)$vencedores[0];

        $html = "<section>";
        $html .= "<h3>Ganador de la Carrera</h3>";
        $html .= "<p>Piloto Vencedor: " . htmlspecialchars($nombreVencedor) . "</p>";

        $nombreCircuito = $this->xml->xpath("//ns:nombre");
        if (!empty($nombreCircuito)) {
            $html .= "<p>Circuito: " . htmlspecialchars((string)$nombreCircuito[0]) . "</p>";
        }

        $fecha = $this->xml->xpath("//ns:fecha");
        if (!empty($fecha)) {
            $html .= "<p>Fecha: " . htmlspecialchars((string)$fecha[0]) . "</p>";
        }

        $html .= "</section>";
        return $html;
    }

    public function mostrarClasificacion() {
        if ($this->xml === null) {
            return "<p>Error: Primero debe llamar al método consultar()</p>";
        }

        $pilotos = $this->xml->xpath("//ns:clasificacion/ns:piloto");
        if (empty($pilotos)) {
            return "<p>No se encontró información de la clasificación del mundial</p>";
        }

        $html = "<section>";
        $html .= "<h3>Clasificación del Mundial tras la Carrera</h3>";

        foreach ($pilotos as $piloto) {
            $posicion = (string)$piloto['pos'];
            $nombre = (string)$piloto;
            $html .= "<p>Posición " . htmlspecialchars($posicion) . ": " . htmlspecialchars($nombre) . "</p>";
        }

        $html .= "</section>";
        return $html;
    }
}

$clasificacion = new Clasificacion();
$cargaExitosa = $clasificacion->consultar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="author" content="Eloy Rubio Suárez" />
    <meta name="description" content="Página de clasifcaciones de MotoGP" />
    <meta name="keywords" content="motos,motor,deporte,inicio,carrera,gran premio,mundial,2025,noticias,clasificación" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MotoGP Desktop - Clasificaciones</title>
    <link rel="stylesheet" type="text/css" href="estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="estilo/layout.css" />
    <link rel="icon" href="multimedia/icon.png" type="image/png" />
</head>
<body>
<header>
    <h1><a href="index.html" title="Página de inicio">MotoGP Desktop</a></h1>
    <nav>
        <a href="index.html" title="Página de inicio">Inicio</a>
        <a href="piloto.html" title="Información del piloto">Piloto</a>
        <a href="circuito.html" title="Información del circuito">Circuito</a>
        <a href="meteorologia.html" title="Información meteorológica">Meteorología</a>
        <a href="clasificaciones.php" title="Clasificaciones" class="active">Clasificaciones</a>
        <a href="juegos.html" title="Juegos">Juegos</a>
        <a href="ayuda.html" title="Ayuda">Ayuda</a>
    </nav>
</header>

<p><a href="index.html">Inicio</a> &gt;&gt; <strong>Clasificaciones</strong></p>

<main>
    <h2>Clasificaciones de MotoGP</h2>

    <?php if ($cargaExitosa): ?>
        <?php echo $clasificacion->mostrarGanador(); ?>
        <?php echo $clasificacion->mostrarClasificacion(); ?>
    <?php else: ?>
        <p>No se pudo cargar la información de clasificaciones.</p>
        <p>Verifique que:</p>
        <ul>
            <li>El archivo xml/circuitoEsquema.xml existe</li>
            <li>El XML tiene formato válido</li>
            <li>La ruta es correcta</li>
        </ul>
    <?php endif; ?>
</main>

</body>
</html>
