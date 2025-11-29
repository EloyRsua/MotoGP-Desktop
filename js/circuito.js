// Clase Circuito para gestionar la información del circuito
class Circuito {
    constructor() {
        this.comprobarApiFile();
    }

    comprobarApiFile() {
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            console.log("API File soportada");
        } else {
            const mensaje = document.createElement("p");
            mensaje.textContent = "Tu navegador no soporta la API File. Por favor, actualiza tu navegador.";
            const section = document.querySelector("section");
            if (section) {
                section.appendChild(mensaje);
            }
        }
    }

    leerArchivoHTML(input) {
        const archivo = input.files[0];
        
        if (!archivo) {
            alert("No se ha seleccionado ningún archivo");
            return;
        }

        const lector = new FileReader();
        
        lector.onload = (evento) => {
            const contenido = evento.target.result;
            this.procesarArchivoHTML(contenido);
        };
        
        lector.onerror = () => {
            alert("Error al leer el archivo");
        };
        
        lector.readAsText(archivo);
    }

   procesarArchivoHTML(contenido) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(contenido, "text/html");
    
    const mainLeido = doc.querySelector("main");

    if (mainLeido) {
        const h2 = mainLeido.querySelector("section > h2");
        if (h2) {
            h2.remove();
        }
    }

    if (mainLeido) {
        let mainDestino = document.querySelector("main");

        if (!mainDestino) {
            mainDestino = document.createElement("main");
            const section = document.querySelector("section");
            if (section && section.parentNode) {
                section.parentNode.insertBefore(mainDestino, section);
            } else {
                document.body.appendChild(mainDestino);
            }
        }
        
        const secciones = mainLeido.querySelectorAll("section");
        secciones.forEach(seccion => {
            const seccionClonada = seccion.cloneNode(true);
            mainDestino.appendChild(seccionClonada);
        });
        
        const seccionDesarrollo = document.querySelector("section");
        if (seccionDesarrollo && seccionDesarrollo.textContent.includes("en desarrollo")) {
            seccionDesarrollo.remove();
        }
    }
}

}

// Clase CargadorSVG para gestionar archivos SVG
class CargadorSVG {
    constructor() {
        // Constructor vacío
    }

    leerArchivoSVG(input) {
        const archivo = input.files[0];
        
        if (!archivo) {
            alert("No se ha seleccionado ningún archivo SVG");
            return;
        }

        const lector = new FileReader();
        
        lector.onload = (evento) => {
            const contenido = evento.target.result;
            this.insertarSVG(contenido);
        };
        
        lector.onerror = () => {
            alert("Error al leer el archivo SVG");
        };
        
        lector.readAsText(archivo);
    }

    insertarSVG(contenido) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(contenido, "image/svg+xml");
        
        const svg = doc.querySelector("svg");
        
        if (svg) {
            let mainDestino = document.querySelector("main");
            
            if (!mainDestino) {
                mainDestino = document.createElement("main");
                document.body.appendChild(mainDestino);
            }
            
            const seccionSVG = document.createElement("section");
            const titulo = document.createElement("h3");
            titulo.textContent = "Perfil de altimetría del circuito";
            
            seccionSVG.appendChild(titulo);
            seccionSVG.appendChild(svg);
            
            mainDestino.appendChild(seccionSVG);
        } else {
            alert("El archivo no contiene un SVG válido");
        }
    }
}

// Clase CargadorKML para gestionar archivos KML y mapas con MapBox
class CargadorKML {
    constructor() {
        this.mapa = null;
        this.coordenadas = [];
        this.puntoOrigen = null;
        // Reemplaza con tu Access Token de MapBox
        this.accessToken = 'pk.eyJ1IjoiZWxveXJ1YmlvIiwiYSI6ImNtM2VpNTlhbjBiOXAybHF4aDU4aTJtMzUifQ.olXVnEliJ4FR9MdMHTb8pA';
    }

    leerArchivoKML(input) {
        const archivo = input.files[0];
        
        if (!archivo) {
            alert("No se ha seleccionado ningún archivo KML");
            return;
        }

        const lector = new FileReader();
        
        lector.onload = (evento) => {
            const contenido = evento.target.result;
            this.procesarKML(contenido);
        };
        
        lector.onerror = () => {
            alert("Error al leer el archivo KML");
        };
        
        lector.readAsText(archivo);
    }

    procesarKML(contenido) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(contenido, "text/xml");
        
        const coordinates = doc.querySelector("coordinates");
        
        if (coordinates) {
            const coordenadasTexto = coordinates.textContent.trim();
            const lineas = coordenadasTexto.split("\n");
            
            this.coordenadas = lineas.map(linea => {
                const partes = linea.trim().split(",");
                if (partes.length >= 2) {
                    return [parseFloat(partes[0]), parseFloat(partes[1])];
                }
                return null;
            }).filter(coord => coord !== null);
            
            if (this.coordenadas.length > 0) {
                this.puntoOrigen = this.coordenadas[0];
                this.inicializarMapa();
            }
        }
    }

    inicializarMapa() {
    mapboxgl.accessToken = this.accessToken;
    
    let mapDiv = document.querySelector("body > div");

    if (!document.querySelector("#header-mapa")) {
        const header = document.createElement("h2");
        header.id = "header-mapa";
        header.textContent = "Mapa del circuito";
        header.style.textAlign = "center";
        header.style.marginTop = "20px";
        header.style.marginBottom = "10px";

        const main = document.querySelector("main");
        if (main) {
            main.parentNode.insertBefore(header, main.nextSibling);
        } else {
            document.body.appendChild(header);
        }
    }

    if (!mapDiv) {
        mapDiv = document.createElement("div");
        mapDiv.style.width = "100%";
        mapDiv.style.height = "500px";
        mapDiv.style.marginBottom = "20px";

        const header = document.querySelector("#header-mapa");
        if (header) {
            header.insertAdjacentElement("afterend", mapDiv);
        } else {
            document.body.appendChild(mapDiv);
        }
    }

    this.mapa = new mapboxgl.Map({
        container: mapDiv,
        style: 'mapbox://styles/mapbox/satellite-v9',
        center: this.puntoOrigen,
        zoom: 14
    });

    this.mapa.on('load', () => {
        this.insertarCapaKML();
    });
}


    insertarCapaKML() {
        // Añadir marcador en el punto origen
        new mapboxgl.Marker()
            .setLngLat(this.puntoOrigen)
            .setPopup(new mapboxgl.Popup().setHTML('<p>Punto de origen del circuito</p>'))
            .addTo(this.mapa);

        // Crear la polilínea con el recorrido del circuito
        this.mapa.addSource('route', {
            type: 'geojson',
            data: {
                type: 'Feature',
                properties: {},
                geometry: {
                    type: 'LineString',
                    coordinates: this.coordenadas
                }
            }
        });

        this.mapa.addLayer({
            id: 'route',
            type: 'line',
            source: 'route',
            layout: {
                'line-join': 'round',
                'line-cap': 'round'
            },
            paint: {
                'line-color': '#FF0000',
                'line-width': 4
            }
        });
    }
}