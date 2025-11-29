<?php
/**
 * EJERCICIO 1: Clase Cronómetro en PHP
 * 
 * Este archivo implementa una clase Cronómetro que permite medir
 * el tiempo transcurrido entre dos momentos (arrancar y parar).
 * 
 * IMPORTANTE: No usa sesiones PHP, almacena el estado en el propio objeto
 * durante la ejecución del script.
 */

// =============================================================================
// TAREA 2: Creación de la clase Cronómetro
// =============================================================================

class Cronometro {
    
    // Atributos privados de la clase
    private $tiempo;  // Almacena el tiempo transcurrido en segundos
    private $inicio;  // Almacena el timestamp del momento de arranque
    
    /**
     * TAREA 2: Constructor de la clase
     * Inicializa el atributo tiempo al valor cero
     */
    public function __construct() {
        $this->tiempo = 0;
    }
    
    /**
     * TAREA 3: Método arrancar
     * Marca el momento temporal en el que se inicia el cronómetro
     * Usa microtime(true) que devuelve el timestamp Unix con microsegundos
     */
    public function arrancar() {
        $this->inicio = microtime(true);
    }
    
    /**
     * TAREA 4: Método parar
     * Detiene el cronómetro y calcula el tiempo transcurrido
     * 
     * Funcionamiento:
     * 1. Obtiene el momento actual con microtime(true)
     * 2. Calcula la diferencia entre el momento actual y el inicio
     * 3. Guarda el resultado en el atributo $tiempo
     */
    public function parar() {
        $fin = microtime(true);
        $this->tiempo = $fin - $this->inicio;
    }
    
    /**
     * TAREA 5: Método mostrar
     * Devuelve el tiempo transcurrido en formato mm:ss.s
     * 
     * Formato explicado:
     * - mm: minutos (dos dígitos)
     * - ss: segundos (dos dígitos)
     * - s: décimas de segundo (un dígito)
     * 
     * Ejemplo: 02:45.7 significa 2 minutos, 45 segundos y 7 décimas
     * 
     * @return string Tiempo formateado como mm:ss.s
     */
    public function mostrar() {
        // Calcular minutos: dividir tiempo total entre 60 y redondear hacia abajo
        $minutos = floor($this->tiempo / 60);
        
        // Calcular segundos: resto de dividir tiempo entre 60
        $segundosTotal = $this->tiempo % 60;
        
        // Extraer la parte entera de los segundos
        $segundos = floor($segundosTotal);
        
        // Calcular décimas: tomar la parte decimal y multiplicar por 10
        // ($segundosTotal - $segundos) da la parte decimal
        // Multiplicar por 10 y redondear hacia abajo da las décimas
        $decimas = floor(($segundosTotal - $segundos) * 10);
        
        // Formatear con sprintf:
        // %02d = número entero con mínimo 2 dígitos (rellena con 0 a la izquierda)
        // %d = número entero sin formato especial
        return sprintf("%02d:%02d.%d", $minutos, $segundos, $decimas);
    }
}

// =============================================================================
// TAREA 6: Gestión de botones y lógica del interfaz
// =============================================================================

// Crear una nueva instancia del cronómetro
// NOTA: En PHP sin sesiones, el cronómetro se reinicia en cada petición
// Para mantener el estado entre peticiones necesitarías sesiones (no pedido)
$cronometro = new Cronometro();

// Variable para almacenar el resultado a mostrar
$resultado = "";

// Verificar qué botón se ha pulsado usando $_POST
// $_POST es un array asociativo que contiene los datos enviados por el formulario

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Si se pulsó el botón "arrancar"
    if (isset($_POST["arrancar"])) {
        $cronometro->arrancar();
        // NOTA: Como no usamos sesiones, el tiempo de inicio se pierde
        // Este es un ejemplo básico. Para funcionar correctamente necesitarías:
        // 1. Sesiones PHP, o
        // 2. Enviar el tiempo de inicio como campo oculto en el formulario
    }
    
    // Si se pulsó el botón "parar"
    if (isset($_POST["parar"])) {
        // NOTA: Sin sesiones, no podemos recuperar el tiempo de inicio
        // Este código solo sirve de demostración de la estructura
        $cronometro->parar();
    }
    
    // Si se pulsó el botón "mostrar"
    if (isset($_POST["mostrar"])) {
        $resultado = $cronometro->mostrar();
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Metadatos del documento -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="Tu Nombre Apellidos" />
    <meta name="description" content="Cronómetro PHP para el proyecto MotoGP Desktop" />
    <meta name="keywords" content="MotoGP, cronómetro, PHP" />
    
    <!-- TAREA 1: Título correcto del documento -->
    <title>MotoGP - Cronómetro PHP</title>
    
    <!-- TAREA 6: Enlaces a las hojas de estilo -->
    <link rel="stylesheet" type="text/css" href="estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="estilo/layout.css" />
</head>
<body>
    <!-- TAREA 6: Estructura general del proyecto MotoGP-Desktop -->
    
    <!-- Header con h1 y nav -->
    <header>
        <h1><a href="index.html" title="Página de inicio">MotoGP Desktop</a></h1>
        
        <!-- Menú de navegación -->
        <nav>
            <a href="index.html" >Inicio</a>
            <a href="piloto.html" >Piloto</a>
            <a href="circuito.html" >Circuito</a>
            <a href="meteorologia.html" >Meteorología</a>
            <a href="clasificaciones.php" >Clasificaciones</a>
            <a href="juegos.html"class="active">Juegos</a>
            <a href="ayuda.html">Ayuda</a>
        </nav>
    </header>
    
    <!-- Migas de navegación -->
    <p>Estás en: <a href="index.html" title="Inicio">Inicio</a> &gt;&gt; 
       <a href="juegos.html" title="Juegos">Juegos</a> &gt;&gt; 
       <strong>Cronómetro PHP</strong></p>
    
    <!-- Contenido principal -->
    <main>
        <section>
            <h2>Cronómetro PHP</h2>
            
            <h3>Control del Cronómetro</h3>
            
            <!-- TAREA 6: Formulario con botones para controlar el cronómetro -->
            <!-- 
                Explicación del formulario:
                - method="post": Los datos se envían usando el método POST
                - action="cronometro.php": Al enviar, recarga esta misma página
                - Cada botón tiene un atributo name único para identificarlo
            -->
            <form method="post" action="cronometro.php">
                <button type="submit" name="arrancar">Arrancar Cronómetro</button>
                <button type="submit" name="parar">Parar Cronómetro</button>
                <button type="submit" name="mostrar">Mostrar Tiempo</button>
            </form>
            
            <!-- Mostrar el resultado si existe -->
            <?php if ($resultado !== ""): ?>
                <h3>Tiempo Transcurrido</h3>
                <p><strong><?php echo $resultado; ?></strong></p>
            <?php endif; ?>
            
            <!-- TAREA 7: Información para la prueba unitaria -->
            <h3>Instrucciones de uso</h3>
            <p>Para probar el cronómetro:</p>
            <ol>
                <li>Pulsa el botón "Arrancar Cronómetro"</li>
                <li>Espera unos segundos</li>
                <li>Pulsa el botón "Parar Cronómetro"</li>
                <li>Pulsa el botón "Mostrar Tiempo" para ver el resultado</li>
            </ol>
            
            <p><strong>Nota:</strong> Este cronómetro básico no mantiene el estado entre 
            peticiones. Para un cronómetro funcional que mantenga el tiempo de inicio, 
            sería necesario usar sesiones PHP o JavaScript.</p>
        </section>
    </main>
    
</body>
</html>