class Memoria {

    constructor() {
        this.barajarCartas();
        this.tablero_bloqueado = false;
        this.primera_carta = null;
        this.segunda_carta = null;
    }

    voltearCarta(carta) {
        if (this.tablero_bloqueado) return;
        if (carta.dataset.estado === "revelada" || carta.dataset.estado === "volteada") return;

        carta.dataset.estado = "volteada";

        if (!this.primera_carta) {
            this.primera_carta = carta;
            return;
        }

        this.segunda_carta = carta;
        this.tablero_bloqueado = true;
        
        // Esperamos a que se vea la segunda carta antes de comprobar
        setTimeout(() => this.comprobarPareja(), 400);
    }

    comprobarPareja() {
        const carta1 = this.primera_carta.children[1].getAttribute("src");
        const carta2 = this.segunda_carta.children[1].getAttribute("src");

        if (carta1 === carta2) {
            this.deshabilitarCartas();
        } else {
            this.cubrirCartas();
        }
    }

    deshabilitarCartas() {
        this.primera_carta.dataset.estado = "revelada";
        this.segunda_carta.dataset.estado = "revelada";
        
        this.comprobarJuego();
        this.reiniciarAtributos();
        this.tablero_bloqueado = false;
    }

    cubrirCartas() {
        setTimeout(() => {
            this.primera_carta.removeAttribute('data-estado');
            this.segunda_carta.removeAttribute('data-estado');
            
            this.reiniciarAtributos();
            this.tablero_bloqueado = false;
        }, 1500);
    }

    reiniciarAtributos() {
        this.primera_carta = null;
        this.segunda_carta = null;
    }

    comprobarJuego() {
        const cartas = document.querySelectorAll("main article");
        const todasReveladas = Array.from(cartas).every(
            carta => carta.dataset.estado === "revelada"
        );

        if (todasReveladas) {
            alert("Has encontrado todas las parejas. Fin del juego.");
        }
    }

    barajarCartas() {
        const contenedor = document.querySelector("main");
        const cartas = Array.from(contenedor.querySelectorAll("article"));
        cartas.sort(() => Math.random() - 0.5);
        cartas.forEach(carta => contenedor.appendChild(carta));
    }
}

var memoria = new Memoria();