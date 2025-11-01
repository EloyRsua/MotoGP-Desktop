class Ciudad {



    constructor(nombre, pais, gentilicio) {
        this.nombre = nombre;
        this.pais = pais;
        this.gentilicio = gentilicio;
        this.poblacion = 0; // valor por defecto
        this.coordenadas = { latitud: 0, longitud: 0 }; // valor por defecto
    }

    // Método para rellenar el resto de atributos
    setDatos(poblacion, latitud, longitud) {
        this.poblacion = poblacion;
        this.coordenadas.latitud = latitud;
        this.coordenadas.longitud = longitud;
    }

    // Método que devuelve el nombre de la ciudad como texto
    getNombreCiudad() {
        return this.nombre;
    }

    // Método que devuelve el país como texto
    getNombrePais() {
        return this.pais;
    }

    // Método que devuelve información secundaria (gentilicio y población)
    getInfoSecundaria() {
        return `
      <ul>
        <li>Gentilicio: ${this.gentilicio}</li>
        <li>Población: ${this.poblacion.toLocaleString()} habitantes</li>
      </ul>
    `;
    }

    // Método que escribe en el documento la información de las coordenadas
    //PREGUNTAR A JAIME SI HAY QUE USAR DOCUMENT.WRITE INCLUSO SI ES MALA PRACTICA
    escribirCoordenadas() {
        const seccion = document.currentScript.parentElement;

        // Crear el elemento <p>
        const p = document.createElement("p");
        p.textContent = `Coordenadas del punto central de ${this.nombre}: (${this.coordenadas.latitud}, ${this.coordenadas.longitud})`;
        seccion.appendChild(p);
    }

    escribirInfo() {
        const seccion = document.currentScript.parentElement;

        const pciudad = document.createElement("p");
        pciudad.textContent = `Ciudad: ${this.getNombreCiudad()}`;
        seccion.appendChild(pciudad);

        const ppais = document.createElement("p");
        ppais.textContent = `País: ${this.getNombrePais()}`;
        seccion.appendChild(ppais);

    }

    escribirInfoSecundaria() {
        // Seleccionamos la sección donde está el script
        const seccion = document.currentScript.parentElement;

        // Creamos el <ul>
        const ul = document.createElement("ul");

        // Creamos los <li>
        const li1 = document.createElement("li");
        li1.textContent = `Gentilicio: australiano/a`;

        const li2 = document.createElement("li");
        li2.textContent = `Población: 6,500 habitantes`;

        // Añadimos los <li> al <ul>
        ul.appendChild(li1);
        ul.appendChild(li2);

        // Añadimos el <ul> a la sección
        seccion.appendChild(ul);

    }
}
