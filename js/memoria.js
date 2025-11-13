class Memoria {

    // Atributos privados
    #tableroBloqueado = false;
    #primeraCarta = null;
    #segundaCarta = null;
    #cronometro = null;

    constructor() {
        this.#cronometro = new Cronometro();
        this.#cronometro.arrancar();

        this.#barajarCartas();
        this.#asignarListeners();
    }

    // Método público
    voltearCarta(carta) {
        if (this.#tableroBloqueado) return;
        if (carta.dataset.estado === "revelada" || carta.dataset.estado === "volteada") return;

        carta.dataset.estado = "volteada";

        if (!this.#primeraCarta) {
            this.#primeraCarta = carta;
            return;
        }

        this.#segundaCarta = carta;
        this.#tableroBloqueado = true;

        setTimeout(() => this.#comprobarPareja(), 400);
    }

    // Métodos privados
    #comprobarPareja() {
        const carta1 = this.#primeraCarta.children[1].getAttribute("src");
        const carta2 = this.#segundaCarta.children[1].getAttribute("src");

        if (carta1 === carta2) {
            this.#deshabilitarCartas();
        } else {
            this.#cubrirCartas();
        }
    }

    #deshabilitarCartas() {
        this.#primeraCarta.dataset.estado = "revelada";
        this.#segundaCarta.dataset.estado = "revelada";

        this.#comprobarJuego();
        this.#reiniciarAtributos();
        this.#tableroBloqueado = false;
    }

    #cubrirCartas() {
        setTimeout(() => {
            this.#primeraCarta.removeAttribute('data-estado');
            this.#segundaCarta.removeAttribute('data-estado');

            this.#reiniciarAtributos();
            this.#tableroBloqueado = false;
        }, 1500);
    }

    #reiniciarAtributos() {
        this.#primeraCarta = null;
        this.#segundaCarta = null;
    }

    #comprobarJuego() {
        const cartas = document.querySelectorAll("main article");
        const todasReveladas = Array.from(cartas).every(
            carta => carta.dataset.estado === "revelada"
        );

        if (todasReveladas) {
            this.#cronometro.parar();
        }
    }

    #barajarCartas() {
        const contenedor = document.querySelector("main");
        const cartas = Array.from(contenedor.querySelectorAll("article"));
        cartas.sort(() => Math.random() - 0.5);
        cartas.forEach(carta => contenedor.appendChild(carta));
    }

    #asignarListeners() {
        const cartas = document.querySelectorAll("main article");
        cartas.forEach(carta => carta.addEventListener("click", () => this.voltearCarta(carta)));
    }
}

const memoria = new Memoria();

const cartas = document.querySelectorAll("main article");
cartas.forEach(carta => {
  carta.addEventListener("click", event => {
    memoria.voltearCarta(carta);
  });
});