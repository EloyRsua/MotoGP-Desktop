import xml.etree.ElementTree as ET
import os

def generar_html_circuito(xml_path):
    """Genera un HTML del circuito con secciones para fotos, videos, tabla de clasificación y referencias a recursos en la carpeta padre."""
    try:
        tree = ET.parse(xml_path)
        root = tree.getroot()
    except (IOError, ET.ParseError) as e:
        print(f"❌ Error al leer el XML: {e}")
        return

    ns = {'uniovi': 'http://www.uniovi.es'}

    # Datos principales
    nombre = root.findtext('uniovi:nombre', 'Desconocido', ns)
    localidad = root.findtext('uniovi:localidad', '?', ns)
    pais = root.findtext('uniovi:pais', '?', ns)
    patrocinador = root.findtext('uniovi:patrocinador', '?', ns)
    longitud = root.findtext('uniovi:longitudTrazado', '?', ns)
    anchura = root.findtext('uniovi:anchura', '?', ns)
    vueltas = root.findtext('uniovi:numero_vueltas', '?', ns)
    fecha = root.findtext('uniovi:fecha', '?', ns)
    hora = root.findtext('uniovi:hora', '?', ns)
    vencedor = root.findtext('uniovi:vencedor', '?', ns)

    # Multimedia
    fotos = [f.text.replace("\\", "/") for f in root.findall('uniovi:galeria_fotografias/uniovi:fotografia', ns)]
    videos = [v.text.replace("\\", "/") for v in root.findall('uniovi:galeria_videos/uniovi:video', ns)]

    # Referencias
    referencias = [ref.text for ref in root.findall('uniovi:referencias/uniovi:referencia', ns)]

    # Clasificación
    clasificacion = []
    for piloto in root.findall('uniovi:clasificacion/uniovi:piloto', ns):
        pos = piloto.attrib.get('pos', '?')
        clasificacion.append((pos, piloto.text))

    # Contenido HTML
    html_content = f"""<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="author" content="Eloy Rubio Suárez"/>
    <meta name="description" content="Página del circuito {nombre}"/>
    <meta name="keywords" content="motos,motor,deporte,inicio,carrera,gran premio,mundial,2025,{nombre},{localidad},{pais}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotoGP-Circuito</title>
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css"/>
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css"/>
    <link rel="icon" href="../multimedia/icon.png" type="image/png"/>
</head>
<body>
<header>
    <h1><a href="index.html">MotoGP Desktop</a></h1>
    <nav>
        <a href="index.html">Inicio</a>
        <a href="piloto.html">Piloto</a>
        <a href="circuito.html" class="active">Circuito</a>
        <a href="meteorologia.html">Meteorología</a>
        <a href="clasificaciones.html">Clasificaciones</a>
        <a href="juegos.html">Juegos</a>
        <a href="ayuda.html">Ayuda</a>
    </nav>
</header>

<p><a href="index.html">Inicio</a> &gt;&gt; <strong>Circuito</strong></p>

<main>
<section>
<h2>{nombre}</h2>

<h3>Datos Generales</h3>
<p>Localidad: {localidad}</p>
<p>País: {pais}</p>
<p>Patrocinador: {patrocinador}</p>
<p>Longitud: {longitud} m</p>
<p>Anchura: {anchura} m</p>
<p>Número de vueltas: {vueltas}</p>
<p>Fecha: {fecha}</p>
<p>Hora: {hora}</p>
<p>Vencedor: {vencedor}</p>
</section>
"""

    # Clasificación como tabla
    if clasificacion:
        html_content += """
<section>
<h3>Clasificación</h3>
<table>
    <thead>
        <tr>
            <th>Posición</th>
            <th>Piloto</th>
        </tr>
    </thead>
    <tbody>
"""
        for pos, piloto in clasificacion[:3]:  # solo los 3 primeros
            html_content += f"        <tr><td>{pos}</td><td>{piloto}</td></tr>\n"
        html_content += """    </tbody>
</table>
</section>
"""

    # Sección Fotos
    if fotos:
        html_content += "<section>\n<h3>Fotos</h3>\n"
        for foto in fotos:
            html_content += f"""
<picture>
    <source media="(min-width: 800px)" srcset="../{foto}"/>
    <source media="(max-width: 799px)" srcset="../{foto}"/>
    <img src="../{foto}" alt="Foto del circuito {nombre}"/>
</picture>
"""
        html_content += "</section>\n"

    # Sección Videos
    if videos:
        html_content += "<section>\n<h3>Videos</h3>\n"
        for video in videos:
            html_content += f"""
<video controls preload="auto" width="600">
    <source src="../{video}" type="video/mp4"/>
    <source src="../{video}" type="video/webm"/>
    Tu navegador no soporta el video.
</video>
"""
        html_content += "</section>\n"

    # Sección Referencias
    if referencias:
        html_content += "<section>\n<h3>Referencias</h3>\n<ul>\n"
        for ref in referencias:
            html_content += f"<li><a href='{ref}'>{ref}</a></li>\n"
        html_content += "</ul>\n</section>\n"

    html_content += "</main>\n</body>\n</html>"

    # Guardar archivo en el mismo directorio que el XML
    archivoHTML = os.path.join(os.path.dirname(os.path.abspath(xml_path)), 'infocircuito.html')
    with open(archivoHTML, 'w', encoding='utf-8') as f:
        f.write(html_content)

    print(f"✅ HTML generado correctamente: {archivoHTML}")


# === Uso ===
if __name__ == "__main__":
    archivoXML = r"C:\Users\Eloy\Desktop\SEW\MotoGP-Desktop\xml\circuitoEsquema.xml"
    generar_html_circuito(archivoXML)
