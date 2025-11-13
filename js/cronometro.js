class Cronometro {

    // Atributos privados
    #tiempo = 0;
    #inicio = 0;
    #corriendo = null;

    constructor() {}

    // Método público: arrancar cronómetro
    arrancar() {
        if (this.#corriendo) return; // ya está corriendo

        this.#inicio = Date.now() - this.#tiempo;
        this.#corriendo = setInterval(() => this.#actualizar(), 100);
    }

    // Método público: parar cronómetro
    parar() {
        if (this.#corriendo) {
            clearInterval(this.#corriendo);
            this.#corriendo = null;
        }
    }

    // Método público: reiniciar cronómetro
    reiniciar() {
        this.parar();
        this.#tiempo = 0;
        this.#inicio = 0;
        this.#mostrar();
    }

    // Método privado: actualizar tiempo
    #actualizar() {
        this.#tiempo = Date.now() - this.#inicio;
        this.#mostrar();
    }

    // Método privado: mostrar tiempo en pantalla
    #mostrar() {
        const minutos = Math.floor(this.#tiempo / 60000);
        const segundos = Math.floor((this.#tiempo % 60000) / 1000);
        const decimas = Math.floor((this.#tiempo % 1000) / 100);

        const formato =
            String(minutos).padStart(2, '0') + ':' +
            String(segundos).padStart(2, '0') + '.' +
            decimas;

        const parrafo = document.querySelector('section p');
        if (parrafo) parrafo.textContent = formato;
    }

    
}

const cronometro = new Cronometro();

// Seleccionamos todos los botones dentro de main
const botones = document.querySelectorAll('main section button');

// Asignamos los listeners según el orden de los botones
botones[0].addEventListener('click', () => cronometro.arrancar());
botones[1].addEventListener('click', () => cronometro.parar());
botones[2].addEventListener('click', () => cronometro.reiniciar());

