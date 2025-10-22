import xml.etree.ElementTree as ET

# Definir el namespace del XML
NS = {'c': 'http://www.uniovi.es'}

# Cargar el archivo XML y crear el árbol DOM
tree = ET.parse('circuitoEsquema.xml')
root = tree.getroot()

# Extraer el nombre del circuito usando XPath
nombre_circuito = root.find('c:nombre', NS).text

# Plantilla de encabezado del archivo KML
kml_header = f'''<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
  <Document>
    <name>{nombre_circuito}</name>
    <description>Planimetría del circuito {nombre_circuito}</description>
    <Style id="lineStyle">
      <LineStyle>
        <color>ff0000ff</color>
        <width>4</width>
      </LineStyle>
    </Style>
    <Placemark>
      <name>Recorrido del circuito</name>
      <styleUrl>#lineStyle</styleUrl>
      <LineString>
        <coordinates>
'''

# Plantilla de cierre del KML
kml_footer = '''        </coordinates>
      </LineString>
    </Placemark>
  </Document>
</kml>
'''

# Generar las coordenadas usando XPath
coordinates_list = []
for tramo in root.findall('.//c:tramo', NS):
    coord = tramo.find('c:coordenadas', NS)
    lon = coord.find('c:longitude', NS).text
    lat = coord.find('c:latitude', NS).text
    alt = coord.find('c:altitude', NS).text
    coordinates_list.append(f"{lon},{lat},{alt}")

# Añadir la primera coordenada al final para cerrar el circuito
if coordinates_list:
    coordinates_list.append(coordinates_list[0])

coordinates = "\n".join(coordinates_list) + "\n"

# Escribir el archivo KML
with open('circuito.kml', 'w', encoding='utf-8') as f:
    f.write(kml_header)
    f.write(coordinates)
    f.write(kml_footer)

print("Archivo 'circuito.kml' generado correctamente con circuito cerrado.")
