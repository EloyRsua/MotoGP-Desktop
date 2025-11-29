<?php
/**
 * EJERCICIO 2: Lectura de archivos XML en el servidor con PHP
 * 
 * Este archivo implementa una clase Clasificacion que lee información
 * del archivo circuitoEsquema.xml y la muestra en formato HTML.
 * 
 * Estructura del XML:
 * - <circuito> (raíz con namespace)
 *   - <vencedor>nombre del ganador</vencedor>
 *   - <clasificacion>
 *       <piloto pos="1">Nombre Piloto</piloto>
 *       <piloto pos="2">Nombre Piloto</piloto>
 *   </clasificacion>
 */

// =============================================================================
// TAREA 2: Creación de la clase Clasificacion
// =============================================================================

class Clasificacion {
    
    // Atributos privados
    private $documento;  // Ruta al archivo XML
    private $xml;        // Objeto SimpleXMLElement con los datos cargados
    
    /**
     * TAREA 2: Constructor de la clase
     * Inicializa el atributo documento con la ruta al archivo XML
     */
    public function __construct() {
        // Ruta relativa al archivo XML desde este documento PHP
        $this->documento = "xml/circuitoEsquema.xml";
    }
    
    /**
     * TAREA 3: Método consultar
     * Lee el contenido del documento XML y lo almacena en el objeto
     * 
     * IMPORTANTE: El XML usa namespaces (xmlns="http://www.uniovi.es")
     * Por eso necesitamos registrar el namespace antes de usar XPath
     * 
     * @return bool True si se cargó correctamente, false en caso contrario
     */
    public function consultar() {
        // Verificar que el archivo existe
        if (!file_exists($this->documento)) {
            echo "<p>Error: No se encuentra el archivo " . htmlspecialchars($this->documento) . "</p>";
            return false;
        }
        
        // Cargar el archivo XML
        // simplexml_load_file() devuelve un objeto SimpleXMLElement
        // o false si hay un error
        $this->xml = simplexml_load_file($this->documento);
        
        if ($this->xml === false) {
            echo "<p>Error: No se pudo cargar el archivo XML</p>";
            // Mostrar errores de XML si los hay
            foreach(libxml_get_errors() as $error) {
                echo "<p>Error XML: " . htmlspecialchars($error->message) . "</p>";
            }
            return false;
        }
        
        // IMPORTANTE: Registrar el namespace para poder usar XPath
        // El XML tiene xmlns="http://www.uniovi.es"
        // Le damos un prefijo "ns" para usarlo en las consultas XPath
        $this->xml->registerXPathNamespace('ns', 'http://www.uniovi.es');
        
        return true;
    }
    
    /**
     * TAREA 4: Método para mostrar el ganador de la carrera
     * 
     * En el XML el ganador está en el elemento <vencedor>
     * Estructura: <vencedor>Raúl Fernández</vencedor>
     * 
     * Como el XML usa namespace, debemos usar XPath con el prefijo "ns:"
     * Consulta XPath: //ns:vencedor
     * 
     * @return string HTML con la información del ganador
     */
    public function mostrarGanador() {
        if ($this->xml === null) {
            return "<p>Error: Primero debe llamar al método consultar()</p>";
        }
        
        // Buscar el elemento vencedor usando XPath con namespace
        // IMPORTANTE: Usar "ns:" porque registramos el namespace con ese prefijo
        $vencedores = $this->xml->xpath("//ns:vencedor");
        
        if (empty($vencedores)) {
            return "<p>No se encontró información del ganador de la carrera</p>";
        }
        
        // Obtener el nombre del vencedor
        // En el XML es: <vencedor>Raúl Fernández</vencedor>
        $nombreVencedor = (string)$vencedores[0];
        
        // Construir el HTML
        $html = "<section>";
        $html .= "<h3>Ganador de la Carrera</h3>";
        $html .= "<p><strong>Piloto Vencedor:</strong> " . htmlspecialchars($nombreVencedor) . "</p>";
        
        // Información adicional del circuito (opcional)
        $nombreCircuito = $this->xml->xpath("//ns:nombre");
        if (!empty($nombreCircuito)) {
            $html .= "<p><strong>Circuito:</strong> " . htmlspecialchars((string)$nombreCircuito[0]) . "</p>";
        }
        
        $fecha = $this->xml->xpath("//ns:fecha");
        if (!empty($fecha)) {
            $html .= "<p><strong>Fecha:</strong> " . htmlspecialchars((string)$fecha[0]) . "</p>";
        }
        
        $html .= "</section>";
        
        return $html;
    }
    
    /**
     * TAREA 5: Método para mostrar la clasificación del mundial
     * 
     * En el XML la clasificación está así:
     * <clasificacion>
     *   <piloto pos="1">Marc Márquez</piloto>
     *   <piloto pos="2">Alex Márquez</piloto>
     *   <piloto pos="3">Marco Bezzecchi</piloto>
     * </clasificacion>
     * 
     * Cada piloto tiene:
     * - Un atributo "pos" con la posición
     * - El nombre como contenido del elemento
     * 
     * Usamos párrafos en lugar de tablas para mejor accesibilidad
     * 
     * @return string HTML con la clasificación en párrafos
     */
    public function mostrarClasificacion() {
        if ($this->xml === null) {
            return "<p>Error: Primero debe llamar al método consultar()</p>";
        }
        
        // Buscar todos los pilotos en la clasificación
        // Usamos XPath con namespace: //ns:clasificacion/ns:piloto
        $pilotos = $this->xml->xpath("//ns:clasificacion/ns:piloto");
        
        if (empty($pilotos)) {
            return "<p>No se encontró información de la clasificación del mundial</p>";
        }
        
        // Construir el HTML con párrafos
        $html = "<section>";
        $html .= "<h3>Clasificación del Mundial tras la Carrera</h3>";
        
        // Iterar sobre cada piloto
        // En PHP, foreach permite recorrer arrays y objetos iterables
        foreach ($pilotos as $piloto) {
            // Obtener el atributo "pos" del piloto
            // Sintaxis: $elemento['nombre_atributo']
            $posicion = (string)$piloto['pos'];
            
            // Obtener el nombre del piloto (contenido del elemento)
            // Convertir a string para obtener el valor
            $nombre = (string)$piloto;
            
            // Agregar párrafo con la información del piloto
            // Formato: "Posición 1: Marc Márquez"
            $html .= "<p><strong>Posición " . htmlspecialchars($posicion) . ":</strong> ";
            $html .= htmlspecialchars($nombre) . "</p>";
        }
        
        $html .= "</section>";
        
        return $html;
    }
}

// =============================================================================
// Uso de la clase Clasificacion
// =============================================================================

// Crear una instancia de la clase
$clasificacion = new Clasificacion();

// Consultar (leer) el archivo XML
$cargaExitosa = $clasificacion->consultar();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Metadatos del documento -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="Tu Nombre Apellidos" />
    <meta name="description" content="Clasificaciones de MotoGP Desktop" />
    <meta name="keywords" content="MotoGP, clasificaciones, resultados, mundial" />
    
    <!-- TAREA 1: Título del documento -->
    <title>MotoGP - Clasificaciones</title>
    
    <!-- Enlaces a hojas de estilo -->
    <link rel="stylesheet" type="text/css" href="estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="estilo/layout.css" />
</head>
<body>
    <!-- Header con h1 y nav -->
    <header>
        <h1><a href="index.html" title="Página de inicio">MotoGP Desktop</a></h1>
        
        <!-- TAREA 1: Menú de navegación actualizado -->
        <!-- IMPORTANTE: Todos los enlaces a clasificaciones.html deben cambiarse a clasificaciones.php -->
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
    
    <!-- Migas de navegación -->
    <p>Estás en: <a href="index.html" title="Inicio">Inicio</a> &gt;&gt; 
       <strong>Clasificaciones</strong></p>
    
    <!-- Contenido principal -->
    <main>
        <section>
            <h2>Clasificaciones de MotoGP</h2>
            
            <?php if ($cargaExitosa): ?>
                
                <!-- TAREA 4: Mostrar el ganador de la carrera -->
                <?php echo $clasificacion->mostrarGanador(); ?>
                
                <!-- TAREA 5: Mostrar la clasificación del mundial -->
                <?php echo $clasificacion->mostrarClasificacion(); ?>
                
            <?php else: ?>
                
                <p>No se pudo cargar la información de clasificaciones.</p>
                <p>Verifique que:</p>
                <ul>
                    <li>El archivo <code>xml/circuitoEsquema.xml</code> existe</li>
                    <li>El archivo XML tiene el formato correcto</li>
                    <li>La ruta al archivo es correcta</li>
                </ul>
                
            <?php endif; ?>
        </section>
    </main>
    
    <!-- TAREA 6: Validación -->
    <!-- 
        Para validar este documento:
        
        PASO 1: Acceder al documento
        - Abre en el navegador: http://localhost/MotoGP-Desktop/clasificaciones.php
        - Asegúrate de que XAMPP está activo con Apache arrancado
        
        PASO 2: Obtener el HTML generado dinámicamente
        - Presiona F12 para abrir las herramientas de desarrollador
        - Ve a la pestaña "Inspector" (Firefox) o "Elements" (Chrome)
        - Haz clic derecho en el elemento <html>
        - Selecciona "Copiar" > "Elemento exterior" o "Copy outerHTML"
        
        PASO 3: Validar el código
        - Ve a: https://validator.w3.org/#validate_by_input
        - Pega el código HTML copiado en el área de texto
        - Haz clic en "Check"
        - Corrige cualquier error que aparezca
        
        IMPORTANTE:
        - Debes validar el HTML DESPUÉS de que PHP lo genere
        - No puedes validar el archivo .php directamente
        - El validador debe mostrar 0 errores y 0 advertencias
    -->
</body>
</html>