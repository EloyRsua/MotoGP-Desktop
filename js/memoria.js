class Memoria {

    constructor(tablero_bloqueado,primera_carta,segunda_carta) {
        this.tablero_bloqueado=tablero_bloqueado;
        this.primera_carta=primera_carta;
        this.segunda_carta=segunda_carta;
    }

    // 
    voltearCarta(carta){
        carta.dataset.estado="volteada";
    }
    barajarCartas(){

    }
    reiniciarAtribuytos(){

    }
    // Deshabilita las cartas que ya han sido emparejadas
    deshabilitarCartas(){

    }
    // Devuelve si el juego ha terminado
    //Se llama en deshabilitarCartas
    comprobarJuego(){

    }
    
    // Cuando las cartas no son iguales se vuelven a cubrir pero tiene un retardo
    // usamos timeout como ejemplo 1.5s
    //Durante toda la ejecución de este metodo el tablero tiene que estar bloqueado
    cubrirCartas(){
    
    }

    //Comprueba si las dos cartas seleccionadas son iguales
    // tenemos que comparar el atributo src de las cartas
    comprobarParejas(){

    }
}