import xml.etree.ElementTree as ET
import os

class SVGGenerator:
    def __init__(self, width=800, height=400, margin=50):
        self.width = width
        self.height = height
        self.margin = margin
        self.svg_elements = []

    def add_polygon(self, points, stroke_color="blue", fill_color="lightblue", stroke_width=2):
        """Añade un polígono cerrado que representa el perfil altimétrico."""
        points_str = " ".join([f"{x},{y}" for x, y in points])
        polygon = f'<polygon points="{points_str}" stroke="{stroke_color}" fill="{fill_color}" stroke-width="{stroke_width}" />'
        self.svg_elements.append(polygon)

    def save_svg(self, filename):
        """Genera y guarda el archivo SVG con los elementos añadidos."""
        svg_content = self._create_svg_content()
        with open(filename, 'w', encoding='utf-8') as file:
            file.write(svg_content)
        print(f"✅ Archivo SVG generado: {filename}")

    def _create_svg_content(self):
        """Crea el contenido SVG a partir de los elementos añadidos."""
        svg_content = f'<svg xmlns="http://www.w3.org/2000/svg" width="{self.width}" height="{self.height}">\n'
        svg_content += "\n".join(self.svg_elements)
        svg_content += "\n</svg>"
        return svg_content


def parse_xml(archivoXML):
    """Parses the XML file and returns the root element."""
    try:
        arbol = ET.parse(archivoXML)
        return arbol.getroot()
    except IOError:
        print(f'❌ No se encuentra el archivo: {archivoXML}')
        return None
    except ET.ParseError:
        print(f"❌ Error procesando el archivo XML: {archivoXML}")
        return None


def extraer_alturas(raiz):
    """Extrae las altitudes de los tramos del elemento raíz."""
    alturas = []
    max_altura = float('-inf')

    ns = {'uniovi': 'http://www.uniovi.es'}

    # Buscar cada tramo en el XML
    for tramo in raiz.findall('.//uniovi:tramo', ns):
        altitud_elem = tramo.find('.//uniovi:coordenadas/uniovi:altitude', ns)
        if altitud_elem is not None and altitud_elem.text:
            try:
                altitud = float(altitud_elem.text)
                alturas.append(altitud)
                max_altura = max(max_altura, altitud)
            except ValueError:
                print(f"⚠️ Altitud no válida en tramo: {altitud_elem.text}")

    return alturas, max_altura


def normalizar_alturas(alturas, max_altura, ancho_svg=800, alto_svg=400, margen=50):
    """Normaliza las alturas para que encajen en el gráfico SVG."""
    if not alturas:
        return []

    escala_altura = (alto_svg - 2 * margen) / max_altura if max_altura != 0 else 1
    puntos_normalizados = []

    for i, altura in enumerate(alturas):
        x = margen + i * ((ancho_svg - 2 * margen) / (len(alturas) - 1))
        y = alto_svg - margen - altura * escala_altura  # Altura desde la base
        puntos_normalizados.append((x, y))

    # Cerrar el polígono con el "suelo"
    puntos_normalizados.append((ancho_svg - margen, alto_svg - margen))
    puntos_normalizados.insert(0, (margen, alto_svg - margen))
    puntos_normalizados.append(puntos_normalizados[0])

    return puntos_normalizados


def extraer_alturas_y_generar_svg(archivoXML):
    """Función principal para extraer alturas del XML y generar el SVG."""
    raiz = parse_xml(archivoXML)
    if raiz is None:
        return

    alturas, max_altura = extraer_alturas(raiz)
    if not alturas:
        print("❌ No se encontraron tramos con altitudes.")
        return

    puntos_normalizados = normalizar_alturas(alturas, max_altura)

    svg_gen = SVGGenerator(width=800, height=400)
    svg_gen.add_polygon(puntos_normalizados, stroke_color="red", fill_color="none", stroke_width=2)

    archivoSVG = os.path.join(os.path.dirname(os.path.abspath(archivoXML)), 'altimetria.svg')
    svg_gen.save_svg(archivoSVG)


# --- Uso del script ---
if __name__ == "__main__":
    archivoXML = r"C:\Users\Eloy\Desktop\SEW\MotoGP-Desktop\xml\circuitoEsquema.xml"
    extraer_alturas_y_generar_svg(archivoXML)
