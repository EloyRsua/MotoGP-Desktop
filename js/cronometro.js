class Cronometro {

    //Se debe mostrar en pantalla el tiempo transcurrido en minutos segundos y decimas de segundo
    constructor() {
        this.tiempo = 0;
        this.inicio = 0;
    }

    arrancar() {
        if (this.corriendo) return; // ya está corriendo

        try {
            const ahora = Temporal.Now.instant();
            this.inicio = Temporal.Instant.fromEpochMilliseconds(ahora.epochMilliseconds - this.tiempo);
        } catch (error) {
            const ahora = new Date();
            this.inicio = new Date(ahora.getTime() - this.tiempo);
        }

        this.corriendo = setInterval(this.actualizar.bind(this), 100);
    }


    //Llamar actualizar cada decima de segundo
    actualizar() {
        let ahora;
        try {
            const diferencia = Temporal.Now.instant().epochMilliseconds - this.inicio.epochMilliseconds;
            this.tiempo = diferencia;
        } catch (error) {
            ahora = new Date();
            this.tiempo = ahora.getTime() - this.inicio.getTime();
        }

        this.mostrar();
    }
    parar() {
        if (this.corriendo) {
            clearInterval(this.corriendo);
            this.corriendo = false;
        }
    }
    reiniciar() {
        this.parar();
        this.tiempo = 0;
        this.inicio = 0;
        this.mostrar();

    }
    //Muestra 00:00.0
    mostrar() {
        const minutos = parseInt(this.tiempo / 60000);
        const segundos = parseInt((this.tiempo % 60000) / 1000);
        const decimas = parseInt((this.tiempo % 1000) / 100);

        // Formateamos con ceros a la izquierda
        const formato =
            String(minutos).padStart(2, '0') + ':' +
            String(segundos).padStart(2, '0') + '.' +
            String(decimas);

        const parrafo = document.querySelector('section p');
        if (parrafo) parrafo.textContent = formato;
    }

}