// Clase Carrusel para gestionar las imágenes del circuito desde Flickr
class Carrusel {
    constructor(circuito) {
        // Atributo privado para el término de búsqueda
        this.busqueda = circuito;
        // Índice de la foto actual
        this.actual = 0;
        // Número máximo de fotos (5 fotos: índices 0-4)
        this.maximo = 4;
        // Array para almacenar las URLs de las fotos
        this.fotos = [];
    }

    // Método para obtener las fotografías desde Flickr
    getFotografias() {
        // URL de la API pública de Flickr (sin necesidad de API key)
        const flickrAPI = "https://api.flickr.com/services/feeds/photos_public.gne?jsoncallback=?";
        
        console.log("Buscando fotos de: " + this.busqueda);
        
        $.getJSON(flickrAPI, {
            tags: this.busqueda,
            tagmode: "any",
            format: "json"
        })
        .done(this.procesarJSONFotografias.bind(this))
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Error al obtener las fotografías de Flickr");
            console.error("Status: " + textStatus);
            console.error("Error: " + errorThrown);
        });
    }

    // Método para procesar el JSON recibido de Flickr
    procesarJSONFotografias(data) {
        console.log("Datos recibidos de Flickr:", data);
        
        if (data.items && data.items.length > 0) {
            console.log("Número de fotos encontradas: " + data.items.length);
            
            // Extraer las URLs de las 5 primeras fotografías
            // Cambiar de tamaño _m (240px) a _z (640px) según requisitos
            for (let i = 0; i < 5 && i < data.items.length; i++) {
                // Cambiar el sufijo _m.jpg por _z.jpg para obtener imágenes de 640px
                const urlGrande = data.items[i].media.m.replace("_m.jpg", "_z.jpg");
                this.fotos.push(urlGrande);
                console.log("Foto " + (i+1) + ": " + urlGrande);
            }
            
            // Mostrar las fotografías
            this.mostrarFotografias();
        } else {
            console.error("No se encontraron fotos con esos tags");
        }
    }

    // Método para mostrar las fotografías en el HTML
    mostrarFotografias() {
        if (this.fotos.length === 0) {
            console.error("No hay fotografías para mostrar");
            return;
        }

        console.log("Mostrando fotografías en el DOM");

        // Crear el artículo contenedor con jQuery
        const articulo = $("<article></article>");
        
        // Crear el encabezado h2
        const titulo = $("<h2></h2>").text("Imágenes del circuito de Phillip Island");
        
        // Crear la imagen inicial
        const imagen = $("<img>")
            .attr("src", this.fotos[this.actual])
            .attr("alt", "Imagen " + (this.actual + 1) + " del circuito de Phillip Island")
        
        // Añadir el título y la imagen al artículo
        articulo.append(titulo);
        articulo.append(imagen);
        
        // Añadir el artículo al main del documento
        // Si no existe main, lo añade al body
        if ($("main").length > 0) {
            $("main").append(articulo);
        } else {
            $("body").append(articulo);
        }
        
        console.log("Artículo añadido al DOM");
        
        // Iniciar el temporizador para cambiar las fotos cada 3 segundos
        setInterval(this.cambiarFotografia.bind(this), 3000);
    }

    // Método para cambiar la fotografía mostrada
    cambiarFotografia() {
        // Incrementar el índice actual
        this.actual++;
        
        // Si llegamos al final, volver al inicio
        if (this.actual > this.maximo) {
            this.actual = 0;
        }
        
        console.log("Cambiando a foto " + (this.actual + 1));
        
        // Actualizar la imagen usando jQuery
        $("#carrusel-imagen")
            .attr("src", this.fotos[this.actual])
            .attr("alt", "Imagen " + (this.actual + 1) + " del circuito de Phillip Island");
    }
}

// Crear una instancia del carrusel cuando el documento esté listo
$(document).ready(function() {
    console.log("Documento listo, creando carrusel...");
    
    // Phillip Island + MotoGP para mejores resultados
    const carrusel = new Carrusel("Phillip Island Circuit");
    carrusel.getFotografias();
});