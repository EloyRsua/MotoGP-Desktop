<?php
require_once 'cronometro.class.php';

session_start();

if (!isset($_SESSION["cronometro"])) {
    $_SESSION["cronometro"] = new Cronometro();
}

$crono = $_SESSION["cronometro"];
$resultado = "";
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["arrancar"])) {
        $crono->arrancar();
        $mensaje = "Cronómetro arrancado";
    }
    if (isset($_POST["parar"])) {
        $crono->parar();
        $mensaje = "Cronómetro detenido";
    }
    if (isset($_POST["mostrar"])) {
        $resultado = $crono->mostrar();
    }
    if (isset($_POST["reiniciar"])) {
        $_SESSION["cronometro"] = new Cronometro();
        $crono = $_SESSION["cronometro"];
        $mensaje = "Cronómetro reiniciado";
        $resultado = "";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="author" content="Eloy Rubio Suárez" />
    <meta name="description" content="Cronómetro PHP" />
    <meta name="keywords"
        content="motos,motor,deporte,inicio,carrera,gran premio,mundial,2025,noticias,cronómetro,juego" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MotoGP Desktop - Cronómetro PHP</title>
    <link rel="stylesheet" href="estilo/estilo.css" />
    <link rel="stylesheet" href="estilo/layout.css" />
    <link rel="icon" href="multimedia/icon.png" />
</head>

<body>
    <header>
        <h1><a href="index.html">MotoGP Desktop</a></h1>
        <nav>
            <a href="index.html">Inicio</a>
            <a href="piloto.html">Piloto</a>
            <a href="circuito.html">Circuito</a>
            <a href="meteorologia.html">Meteorología</a>
            <a href="clasificaciones.php">Clasificaciones</a>
            <a href="juegos.html" class="active">Juegos</a>
            <a href="ayuda.html">Ayuda</a>
        </nav>
    </header>
    <p><a href="index.html">Inicio</a> &gt;&gt; <a href="juegos.html">Juegos</a> &gt;&gt; Cronómetro PHP</p>
    <main>
        <section>
            <h2>Cronómetro PHP</h2>
            <h3>Control del Cronómetro</h3>
            <form method="post" action="cronometro.php">
                <button type="submit" name="arrancar">Arrancar</button>
                <button type="submit" name="parar">Parar</button>
                <button type="submit" name="mostrar">Mostrar Tiempo</button>
                <button type="submit" name="reiniciar">Reiniciar</button>
            </form>
            <?php if ($mensaje !== ""): ?>
                <p><?php echo htmlspecialchars($mensaje); ?></p>
            <?php endif; ?>
            <?php if ($resultado !== ""): ?>
                <h3>Tiempo Transcurrido</h3>
                <p>Tiempo: <?= $resultado ?></p>
            <?php endif; ?>
            <h3>Instrucciones</h3>
            <ol>
                <li>Pulsa Arrancar para iniciar el cronómetro</li>
                <li>Espera el tiempo que desees medir</li>
                <li>Pulsa Parar para detener el cronómetro</li>
                <li>Pulsa Mostrar Tiempo para ver el resultado</li>
                <li>Pulsa Reiniciar para volver a empezar</li>
            </ol>
            <p>Nota técnica: Este cronómetro usa sesiones PHP para mantener el estado entre peticiones al servidor.</p>
        </section>
    </main>
</body>

</html>