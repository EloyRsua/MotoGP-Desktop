<?php
class Cronometro
{
    private $tiempo;
    private $inicio;

    public function __construct()
    {
        $this->tiempo = 0;
    }
    public function arrancar()
    {
        $this->inicio = microtime(true);
    }
    public function parar()
    {
        $fin = microtime(true);
        $this->tiempo = $fin - $this->inicio;
    }

    public function mostrar()
    {
        $minutos = floor($this->tiempo / 60);
        $segundos = $this->tiempo % 60;
        $decimas = ($segundos - floor($segundos)) * 100;
        $cadena = $minutos . " : " . $segundos . " . " . $decimas;
        return $cadena;
    }


}
$cronometro = new Cronometro();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Eloy Rubio Suárez">
    <meta name="description" content="Cronómetro PHP para MotoGP Desktop">
    <meta name="keywords" content="cronómetro, PHP, MotoGP">
    <title>MotoGP-Cronómetro</title>
    <link rel="stylesheet" href="estilo/estilo.css">
    <link rel="stylesheet" href="estilo/layout.css">
</head>

<body>
    <header>
        <h1>MotoGP Desktop</h1>
        <nav>
            <a href="index.html">Inicio</a>
            <a href="piloto.html">Piloto</a>
            <a href="circuito.html">Circuito</a>
            <a href="meteorologia.html">Meteorología</a>
            <a href="clasificaciones.php">Clasificaciones</a>
            <a href="juegos.html">Juegos</a>
            <a href="ayuda.html">Ayuda</a>
        </nav>
    </header>

    <p><a href="index.html">Inicio</a> &gt;&gt; <strong>Cronómetro PHP</strong></p>


    <main>
        <h2>Cronómetro PHP</h2>

        <section>
            <h3>Control del Cronómetro</h3>
            <form method="post" action="cronometro.php">
                <button type="submit" name="arrancar">Arrancar</button>
                <button type="submit" name="parar">Parar</button>
                <button type="submit" name="mostrar">Mostrar Tiempo</button>
            </form>

        </section>
    </main>
</body>

</html>