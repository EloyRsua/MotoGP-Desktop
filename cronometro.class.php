<?php
class Cronometro
{
    private $tiempo;
    private $inicio;

    public function __construct()
    {
        $this->tiempo = 0;
        $this->inicio = 0;
    }

    public function arrancar()
    {
        $this->inicio = microtime(true);
    }

    public function parar()
    {
        if ($this->inicio > 0) {
            $fin = microtime(true);
            $this->tiempo = $fin - $this->inicio;
        }
    }

    public function mostrar()
    {
        $totalSegundos = $this->tiempo;
        $min = floor($totalSegundos / 60);
        $seg = floor($totalSegundos % 60);
        $decima = floor(($totalSegundos - floor($totalSegundos)) * 10);
        return sprintf("%02d:%02d.%d", $min, $seg, $decima);
    }
    public function getTiempo()
    {
        return $this->tiempo;
    }
}
?>