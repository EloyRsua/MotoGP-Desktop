class Noticias {
    constructor() {
        // Término de búsqueda para MotoGP
        this.busqueda = "MotoGP";
        
        // URL base de la API de TheNewsApi
        this.url = "https://api.thenewsapi.com/v1/news/all";
        
        // Tu API key de TheNewsApi (debes reemplazar esto con tu propia clave)
        this.apikey = "4quOWBliNnQvnKB7WMHrxqktCJFrQ6mttaVt9y17";
    }

    // Ejercicio 4 - Tarea 4: Obtener noticias sobre MotoGP usando fetch()
    buscar() {
        // Construir la URL completa con los parámetros
        const urlCompleta = `${this.url}?api_token=${this.apikey}&search=${this.busqueda}&language=es&limit=5`;

        // Realizar la llamada usando fetch()
        fetch(urlCompleta)
            .then(response => {
                // Verificar que la respuesta sea exitosa
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                // Convertir la respuesta a JSON
                return response.json();
            })
            .then(datos => {
                // Procesar la información recibida
                this.procesarInformacion(datos);
            })
            .catch(error => {
                // Manejar errores
                console.error('Error al obtener las noticias:', error);
                $("main").append(`<p>Error al cargar las noticias: ${error.message}</p>`);
            });
    }

    // Ejercicio 4 - Tarea 4: Procesar información del objeto JSON
    procesarInformacion(datos) {
        // Verificar que hay noticias en la respuesta
        if (!datos.data || datos.data.length === 0) {
            $("main").append("<p>No se encontraron noticias sobre MotoGP</p>");
            return;
        }

        // Crear una sección para las noticias
        const seccionNoticias = $("<section></section>");
        seccionNoticias.append("<h3>Últimas noticias de MotoGP</h3>");

        // Procesar cada noticia
        datos.data.forEach(noticia => {
            // Crear un artículo para cada noticia
            const article = $("<article></article>");

            // Titular de la noticia
            const titular = $("<h4></h4>").text(noticia.title);
            article.append(titular);

            // Entradilla o descripción
            if (noticia.description) {
                const entradilla = $("<p></p>").text(noticia.description);
                article.append(entradilla);
            }

            // Información adicional (fecha y fuente)
            const info = $("<p></p>");
            
            if (noticia.published_at) {
                const fecha = new Date(noticia.published_at);
                const fechaFormateada = fecha.toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                info.append("Fecha: " + fechaFormateada);
            }

            if (noticia.source) {
                info.append(" | Fuente: " + noticia.source);
            }

            article.append(info);

            // Enlace a la noticia completa
            if (noticia.url) {
                const enlace = $("<p></p>");
                const link = $("<a></a>")
                    .attr("href", noticia.url)
                    .attr("target", "_blank")
                    .attr("rel", "noopener noreferrer")
                    .text("Leer noticia completa");
                enlace.append(link);
                article.append(enlace);
            }

            // Añadir el artículo a la sección
            seccionNoticias.append(article);
        });

        // Añadir la sección completa al main del documento
        $("main").append(seccionNoticias);
    }
}