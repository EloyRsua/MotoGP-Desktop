#!/usr/bin/env python3
# xml2altimetria.py
# Genera altimetria.svg a partir de circuitoEsquema.xml
# Usa xml.etree.ElementTree y XPath (namespace-aware)
# Produce altimetria.svg listo para convertir a PDF

import xml.etree.ElementTree as ET
from decimal import Decimal, InvalidOperation
import math

NS = {'c': 'http://www.uniovi.es'}
XML_IN = 'circuitoEsquema.xml'
SVG_OUT = 'altimetria.svg'


class Svg:
    """Clase que genera un archivo SVG a partir de métodos."""

    def __init__(self, width=1200, height=480):
        # Dimensiones del dibujo SVG
        self.width = width
        self.height = height
        # Lista de fragmentos de texto (cada elemento es una línea del SVG)
        self.parts = []
        # Llamamos a una función interna para añadir el encabezado inicial
        self._start()

    def _start(self):
        """Escribe la cabecera XML y la etiqueta <svg> inicial"""
        self.parts.append('<?xml version="1.0" encoding="UTF-8"?>')
        # ViewBox = sistema de coordenadas
        self.parts.append(f'<svg xmlns="http://www.w3.org/2000/svg" '
                          f'xmlns:xlink="http://www.w3.org/1999/xlink" '
                          f'width="{self.width}" height="{self.height}" '
                          f'viewBox="0 0 {self.width} {self.height}">')

        # -------------------------------------------------------
        # Definición de estilos CSS embebidos dentro del SVG
        # -------------------------------------------------------
        self.parts.append('<style>')
        self.parts.append('.axis { stroke: #333; stroke-width:1 }')       # Ejes principales
        self.parts.append('.grid { stroke: #ddd; stroke-width:0.8 }')     # Cuadrícula
        self.parts.append('.profile-fill { fill: rgba(30,144,255,0.25); }') # Relleno bajo el perfil
        self.parts.append('.profile-stroke { stroke: #1e90ff; stroke-width:2; fill:none }') # Línea del perfil
        self.parts.append('.label { font-family: Arial, sans-serif; font-size:12px; fill:#111 }') # Textos
        self.parts.append('</style>')

    def add_title(self, text):
        """Dibuja el título centrado en la parte superior"""
        self.parts.append(f'<text x="{self.width/2}" y="28" class="label" '
                          f'text-anchor="middle" font-size="16">{text}</text>')

    def add_line(self, x1, y1, x2, y2, cls='axis'):
        """Añade una línea entre dos puntos"""
        self.parts.append(f'<line x1="{x1}" y1="{y1}" x2="{x2}" y2="{y2}" class="{cls}" />')

    def add_text(self, x, y, text, anchor='start', cls='label', rotate=None):
        """Escribe un texto en una posición concreta"""
        # Si se indica rotación, se añade un atributo transform
        rot = f' transform="rotate({rotate} {x} {y})"' if rotate is not None else ''
        self.parts.append(f'<text x="{x}" y="{y}" class="{cls}" '
                          f'text-anchor="{anchor}"{rot}>{text}</text>')

    def add_polyline(self, points, cls='profile-stroke', fill=False):
        """Dibuja una línea o polígono a partir de una lista de puntos"""
        pts = " ".join(f"{round(x,2)},{round(y,2)}" for x, y in points)
        if fill:
            # Si se pide relleno, se usa <polygon> y después una línea sobre él
            self.parts.append(f'<polygon points="{pts}" class="profile-fill" />')
            self.parts.append(f'<polyline points="{pts}" class="{cls}" style="fill:none" />')
        else:
            self.parts.append(f'<polyline points="{pts}" class="{cls}" />')

    def add_rect(self, x, y, w, h, fill='white', stroke='#999'):
        """Dibuja un rectángulo (usado para la leyenda)"""
        self.parts.append(f'<rect x="{x}" y="{y}" width="{w}" height="{h}" fill="{fill}" stroke="{stroke}" />')

    def finish(self):
        """Cierra el SVG con la etiqueta </svg>"""
        self.parts.append('</svg>')

    def save(self, filename):
        """Guarda todo el contenido acumulado en un archivo"""
        self.finish()  # cerramos el documento
        with open(filename, 'w', encoding='utf-8') as f:
            f.write("\n".join(self.parts))  # se escribe línea a línea


# ---------------------------------------------------------------
# Función parse_tramos()
# ---------------------------------------------------------------
# Esta función lee el XML “circuitoEsquema.xml” y extrae los valores
# de distancia (longitud de cada tramo) y altitud (del punto final).
# Usa expresiones XPath con el prefijo “c:” (definido en NS)
# ---------------------------------------------------------------

def parse_tramos(xmlfile):
    # Cargamos el XML en memoria
    tree = ET.parse(xmlfile)
    root = tree.getroot()

    # Buscamos todos los elementos <tramo> dentro de <tramos>
    tramos = root.findall('.//c:tramo', NS)

    # Listas donde guardaremos las distancias y altitudes
    distances = []
    altitudes = []

    # Recorremos cada <tramo> y obtenemos sus valores
    for tramo in tramos:
        # Obtenemos la distancia mediante XPath
        d_el = tramo.find('c:distancia', NS)
        # Obtenemos el bloque de coordenadas
        coord = tramo.find('c:coordenadas', NS)

        # Si falta alguno de los datos, se ignora este tramo
        if d_el is None or d_el.text is None or coord is None:
            continue

        # Intentamos convertir la distancia a número decimal
        try:
            d_val = Decimal(d_el.text.strip())
        except (InvalidOperation, AttributeError):
            continue

        # Obtenemos la altitud dentro de <coordenadas>
        alt_el = coord.find('c:altitude', NS)
        if alt_el is None or alt_el.text is None:
            continue

        # Intentamos convertir la altitud a número decimal
        try:
            alt_val = Decimal(alt_el.text.strip())
        except (InvalidOperation, AttributeError):
            continue

        # Añadimos los valores a las listas
        distances.append(d_val)
        altitudes.append(alt_val)

    return distances, altitudes


# ---------------------------------------------------------------
# Función build_profile()
# ---------------------------------------------------------------
# Convierte las distancias de cada tramo en distancias acumuladas,
# es decir, el eje X del perfil altimétrico.
# Ejemplo:
# tramos = [100, 200, 300]  ->  acumulado = [100, 300, 600]
# ---------------------------------------------------------------

def build_profile(distances):
    cumul = []           # Lista con distancias acumuladas
    total = Decimal('0') # Distancia total
    for d in distances:
        total += d
        cumul.append(total)
    return cumul, total


# ---------------------------------------------------------------
# Función make_svg()
# ---------------------------------------------------------------
# Genera el dibujo SVG del perfil altimétrico
# usando los valores de distancia y altitud.
# ---------------------------------------------------------------

def make_svg(cumul, altitudes, total_distance, circuit_name='Circuito'):
    # -----------------------------------------------------------
    # Configuración básica del dibujo (márgenes, tamaños)
    # -----------------------------------------------------------
    svg_w = 1200
    svg_h = 480
    margin_left = 80
    margin_right = 40
    margin_top = 40
    margin_bottom = 80
    inner_w = svg_w - margin_left - margin_right   # ancho del área útil
    inner_h = svg_h - margin_top - margin_bottom   # alto del área útil

    # -----------------------------------------------------------
    # Cálculo de mínimos y máximos de altitud
    # -----------------------------------------------------------
    min_alt = float(min(altitudes))
    max_alt = float(max(altitudes))

    # Si todas las altitudes son iguales, evitamos división por cero
    if max_alt == min_alt:
        max_alt += 1

    # Pequeño margen vertical (5%)
    vpad = (max_alt - min_alt) * 0.05
    min_plot = min_alt - vpad
    max_plot = max_alt + vpad

    # -----------------------------------------------------------
    # Funciones auxiliares para transformar valores a coordenadas
    # -----------------------------------------------------------
    def x_from_dist(d):
        """Convierte una distancia (m) en coordenada X del dibujo"""
        if total_distance == 0:
            return margin_left
        return margin_left + float(d / total_distance) * inner_w

    def y_from_alt(a):
        """Convierte una altitud (m) en coordenada Y del dibujo"""
        ratio = (float(a) - min_plot) / (max_plot - min_plot)
        return margin_top + (1.0 - ratio) * inner_h  # invertido porque Y crece hacia abajo

    # -----------------------------------------------------------
    # Creamos la lista de puntos (x,y) del perfil
    # -----------------------------------------------------------
    pts = []
    for d, a in zip(cumul, altitudes):
        x = x_from_dist(d)
        y = y_from_alt(a)
        pts.append((x, y))

    # -----------------------------------------------------------
    # Para cerrar la polilínea y crear el "efecto suelo"
    # añadimos dos puntos adicionales en la línea de base
    # -----------------------------------------------------------
    baseline_y = margin_top + inner_h  # altura del suelo
    polygon_pts = []
    if pts:
        polygon_pts.append((pts[0][0], baseline_y))   # comienzo en la base
        polygon_pts.extend(pts)                       # puntos reales del perfil
        polygon_pts.append((pts[-1][0], baseline_y))  # cierre en la base

    # -----------------------------------------------------------
    # Creación del objeto SVG
    # -----------------------------------------------------------
    s = Svg(width=svg_w, height=svg_h)
    s.add_title(f'Perfil altimétrico - {circuit_name}')

    # -----------------------------------------------------------
    # Dibujamos cuadrículas horizontales (altitud)
    # -----------------------------------------------------------
    v_divs = 5  # número de divisiones
    for i in range(v_divs + 1):
        frac = i / v_divs
        val = min_plot + (max_plot - min_plot) * frac
        y = y_from_alt(val)
        # Línea horizontal de la cuadrícula
        s.add_line(margin_left, y, svg_w - margin_right, y, cls='grid')
        # Etiqueta a la izquierda con el valor en metros
        s.add_text(margin_left - 10, y + 4, f"{round(val,1)} m", anchor='end')

    # -----------------------------------------------------------
    # Dibujamos cuadrículas verticales (distancia)
    # -----------------------------------------------------------
    h_divs = 8
    for i in range(h_divs + 1):
        frac = i / h_divs
        dval = float(total_distance) * frac
        x = margin_left + frac * inner_w
        s.add_line(x, margin_top, x, baseline_y, cls='grid')
        # Mostramos etiquetas en metros o kilómetros
        if dval >= 1000:
            txt = f"{round(dval/1000,2)} km"
        else:
            txt = f"{round(dval,1)} m"
        s.add_text(x, baseline_y + 18, txt, anchor='middle')

    # -----------------------------------------------------------
    # Dibujamos los ejes principales X (distancia) e Y (altitud)
    # -----------------------------------------------------------
    s.add_line(margin_left, margin_top, margin_left, baseline_y, cls='axis')   # eje Y
    s.add_line(margin_left, baseline_y, svg_w - margin_right, baseline_y, cls='axis')  # eje X

    # -----------------------------------------------------------
    # Dibujamos el perfil altimétrico (relleno y línea superior)
    # -----------------------------------------------------------
    if polygon_pts:
        # Área bajo el perfil (relleno)
        s.add_polyline(polygon_pts, cls='profile-stroke', fill=True)
        # Línea del perfil (sin relleno)
        s.add_polyline(pts, cls='profile-stroke', fill=False)

    # -----------------------------------------------------------
    # Añadimos una leyenda simple en la esquina superior derecha
    # -----------------------------------------------------------
    s.add_rect(svg_w - margin_right - 160, margin_top, 150, 48)
    s.add_text(svg_w - margin_right - 80, margin_top + 18, "Altimetría", anchor='middle')
    s.add_text(svg_w - margin_right - 80, margin_top + 36, "(m)", anchor='middle')

    # -----------------------------------------------------------
    # Guardamos el archivo SVG final
    # -----------------------------------------------------------
    s.save(SVG_OUT)
    print(f"SVG guardado en '{SVG_OUT}' — distancia total = {float(total_distance):.2f} m.")


# ---------------------------------------------------------------
# Función principal (main)
# ---------------------------------------------------------------
# Coordina la lectura del XML, el cálculo y la generación del SVG
# ---------------------------------------------------------------

def main():
    # Llamamos a parse_tramos() para leer el XML y extraer datos
    distances, altitudes = parse_tramos(XML_IN)

    # Comprobamos si se han leído datos válidos
    if not distances:
        print("No se han encontrado tramos válidos en el XML.")
        return

    # Calculamos la distancia acumulada (eje X)
    cumul, total = build_profile(distances)

    # Intentamos obtener el nombre del circuito para el título
    try:
        root = ET.parse(XML_IN).getroot()
        nombre_el = root.find('c:nombre', NS)
        name = nombre_el.text if nombre_el is not None else 'Circuito'
    except Exception:
        name = 'Circuito'

    # Generamos el SVG con todos los datos
    make_svg(cumul, altitudes, total, circuit_name=name)


# ---------------------------------------------------------------
# Punto de entrada del programa
# ---------------------------------------------------------------
if __name__ == '__main__':
    main()