class Ciudad {
    constructor(nombre, pais, gentilicio) {
        this.nombre = nombre;
        this.pais = pais;
        this.gentilicio = gentilicio;
        this.poblacion = 0;
        this.coordenadas = { latitud: 0, longitud: 0 };
    }

    setDatos(poblacion, latitud, longitud) {
        this.poblacion = poblacion;
        this.coordenadas.latitud = latitud;
        this.coordenadas.longitud = longitud;
    }

    getNombreCiudad() {
        return this.nombre;
    }

    getNombrePais() {
        return this.pais;
    }

    getInfoSecundaria() {
        return `
      <ul>
        <li>Gentilicio: ${this.gentilicio}</li>
        <li>Población: ${this.poblacion.toLocaleString()} habitantes</li>
      </ul>
    `;
    }

    escribirCoordenadas() {
        const seccion = document.currentScript.parentElement;
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
        const seccion = document.currentScript.parentElement;
        const ul = document.createElement("ul");

        const li1 = document.createElement("li");
        li1.textContent = `Gentilicio: ${this.gentilicio}`;

        const li2 = document.createElement("li");
        li2.textContent = `Población: ${this.poblacion.toLocaleString()} habitantes`;

        ul.appendChild(li1);
        ul.appendChild(li2);
        seccion.appendChild(ul);
    }

    // Ejercicio 3 - Tarea 3: Obtener datos meteorológicos del día de la carrera
    getMeteorologiaCarrera(fecha) {
        const url = `https://archive-api.open-meteo.com/v1/archive?latitude=${this.coordenadas.latitud}&longitude=${this.coordenadas.longitud}&start_date=${fecha}&end_date=${fecha}&hourly=temperature_2m,apparent_temperature,rain,relative_humidity_2m,wind_speed_10m,wind_direction_10m&daily=sunrise,sunset&timezone=auto`;

        $.ajax({
            dataType: "json",
            url: url,
            method: 'GET',
            success: this.procesarJSONCarrera.bind(this),
            error: function(error) {
                $("section").append("<p>Error al obtener los datos meteorológicos de la carrera: " + error.status + " " + error.statusText + "</p>");
            }
        });
    }

    // Ejercicio 3 - Tarea 4: Procesar información del JSON de la carrera
    procesarJSONCarrera(datos) {
        // Extraer datos horarios
        const hourly = datos.hourly;
        const daily = datos.daily;

        // Crear estructura HTML para mostrar los datos
        const article = $("<article></article>");
        article.append("<h4>Meteorología del día de la carrera</h4>");

        // Información diaria (sunrise/sunset)
        const pSunrise = $("<p></p>").text(`Amanecer: ${daily.sunrise[0]}`);
        const pSunset = $("<p></p>").text(`Puesta de sol: ${daily.sunset[0]}`);
        article.append(pSunrise);
        article.append(pSunset);

        // Crear tabla con datos horarios
        const table = $("<table></table>");
        const thead = $("<thead></thead>");
        const headerRow = $("<tr></tr>");
        
        headerRow.append("<th>Hora</th>");
        headerRow.append("<th>Temperatura (°C)</th>");
        headerRow.append("<th>Sensación térmica (°C)</th>");
        headerRow.append("<th>Lluvia (mm)</th>");
        headerRow.append("<th>Humedad (%)</th>");
        headerRow.append("<th>Velocidad viento (km/h)</th>");
        headerRow.append("<th>Dirección viento (°)</th>");
        
        thead.append(headerRow);
        table.append(thead);

        const tbody = $("<tbody></tbody>");

        // Recorrer datos horarios
        for (let i = 0; i < hourly.time.length; i++) {
            const row = $("<tr></tr>");
            
            // Extraer solo la hora del timestamp
            const hora = hourly.time[i].split('T')[1];
            
            row.append($("<td></td>").text(hora));
            row.append($("<td></td>").text(hourly.temperature_2m[i]));
            row.append($("<td></td>").text(hourly.apparent_temperature[i]));
            row.append($("<td></td>").text(hourly.rain[i]));
            row.append($("<td></td>").text(hourly.relative_humidity_2m[i]));
            row.append($("<td></td>").text(hourly.wind_speed_10m[i]));
            row.append($("<td></td>").text(hourly.wind_direction_10m[i]));
            
            tbody.append(row);
        }

        table.append(tbody);
        article.append(table);

        // Añadir al documento
        $("section").append(article);
    }

    // Ejercicio 3 - Tarea 6: Obtener datos meteorológicos de los días de entrenamientos
    getMeteorologiaEntrenos(fechaInicio, fechaFin) {
        const url = `https://archive-api.open-meteo.com/v1/archive?latitude=${this.coordenadas.latitud}&longitude=${this.coordenadas.longitud}&start_date=${fechaInicio}&end_date=${fechaFin}&hourly=temperature_2m,rain,wind_speed_10m,relative_humidity_2m&timezone=auto`;

        $.ajax({
            dataType: "json",
            url: url,
            method: 'GET',
            success: this.procesarJSONEntrenos.bind(this),
            error: function(error) {
                $("section").append("<p>Error al obtener los datos meteorológicos de entrenamientos: " + error.status + " " + error.statusText + "</p>");
            }
        });
    }

    // Ejercicio 3 - Tarea 7: Procesar información del JSON de entrenamientos
    procesarJSONEntrenos(datos) {
        const hourly = datos.hourly;
        
        // Agrupar datos por día
        const datosPorDia = {};
        
        for (let i = 0; i < hourly.time.length; i++) {
            const fecha = hourly.time[i].split('T')[0];
            
            if (!datosPorDia[fecha]) {
                datosPorDia[fecha] = {
                    temperaturas: [],
                    lluvias: [],
                    velocidadesViento: [],
                    humedades: []
                };
            }
            
            datosPorDia[fecha].temperaturas.push(hourly.temperature_2m[i]);
            datosPorDia[fecha].lluvias.push(hourly.rain[i]);
            datosPorDia[fecha].velocidadesViento.push(hourly.wind_speed_10m[i]);
            datosPorDia[fecha].humedades.push(hourly.relative_humidity_2m[i]);
        }

        // Calcular medias para cada día
        const medias = {};
        
        for (const fecha in datosPorDia) {
            const dia = datosPorDia[fecha];
            
            medias[fecha] = {
                temperatura: this.calcularMedia(dia.temperaturas),
                lluvia: this.calcularMedia(dia.lluvias),
                velocidadViento: this.calcularMedia(dia.velocidadesViento),
                humedad: this.calcularMedia(dia.humedades)
            };
        }

        // Mostrar resultados
        const article = $("<article></article>");
        article.append("<h4>Meteorología de los días de entrenamientos</h4>");

        const table = $("<table></table>");
        const thead = $("<thead></thead>");
        const headerRow = $("<tr></tr>");
        
        headerRow.append("<th>Fecha</th>");
        headerRow.append("<th>Temperatura media (°C)</th>");
        headerRow.append("<th>Lluvia media (mm)</th>");
        headerRow.append("<th>Velocidad viento media (km/h)</th>");
        headerRow.append("<th>Humedad media (%)</th>");
        
        thead.append(headerRow);
        table.append(thead);

        const tbody = $("<tbody></tbody>");

        for (const fecha in medias) {
            const row = $("<tr></tr>");
            const media = medias[fecha];
            
            row.append($("<td></td>").text(fecha));
            row.append($("<td></td>").text(media.temperatura));
            row.append($("<td></td>").text(media.lluvia));
            row.append($("<td></td>").text(media.velocidadViento));
            row.append($("<td></td>").text(media.humedad));
            
            tbody.append(row);
        }

        table.append(tbody);
        article.append(table);

        $("section").append(article);
    }

    // Método auxiliar para calcular la media con 2 decimales
    calcularMedia(valores) {
        const suma = valores.reduce((acc, val) => acc + val, 0);
        const media = suma / valores.length;
        return media.toFixed(2);
    }
}