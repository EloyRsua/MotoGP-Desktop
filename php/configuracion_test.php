<?php
// OBLIGATORIO: Los archivos PHP deben usar POO.
// Nota: La configuración de error está activa (E_ALL), es útil para desarrollo.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Asegúrate de que Configuracion.php existe en la misma carpeta
require_once('Configuracion.php'); 

$configuracion = new Configuracion();
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['reiniciar'])) {
        $mensaje = $configuracion->reiniciarBaseDeDatos();
    } elseif (isset($_POST['eliminar'])) {
        $mensaje = $configuracion->eliminarBaseDeDatos();
    } elseif (isset($_POST['exportar'])) {
        // La función exportarDatosCSV llama a exit()
        $configuracion->exportarDatosCSV();
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Configuración y mantenimiento de la base de datos del test de usabilidad del proyecto MotoGP Desktop.">
    <title>Configuración Test de Usabilidad</title>
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
    <link rel="icon" href="../multimedia/icon.png" type="image/png">
</head>

<body>
    <header>
        <section>
            <h1>Configuración de la Base de Datos</h1>
        </section>
    </header>

    <main>
        <section>
            <h2>Gestión del Test de Usabilidad (DB: UO298184_DB)</h2>
            <p>Utiliza esta interfaz para realizar operaciones de mantenimiento sobre la base de datos.</p>

            <?php if (!empty($mensaje)): ?>
                <article>
                    <h3>Resultado de la Operación</h3>
                    <p><?php echo $mensaje; ?></p>
                </article>
            <?php endif; ?>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

                <section>
                    <h3>Reiniciar Datos</h3>
                    <p>Borra **todos** los datos de las tablas de la base de datos y reinserta la Pericia Informática.</p>
                    <button type="submit" name="reiniciar">
                        Reiniciar Base de Datos
                    </button>
                </section>

                <section>
                    <h3>Exportar Datos</h3>
                    <p>Exporta la información de la tabla Prueba en formato CSV.</p>
                    <button type="submit" name="exportar">
                        Exportar Datos a CSV
                    </button>
                </section>

                <section>
                    <h3>Eliminar Base de Datos</h3>
                    <p>Elimina la base de datos completa, sus tablas y todos los datos asociados.</p>
                    <button type="submit" name="eliminar">
                        Eliminar Base de Datos
                    </button>
                </section>

            </form>
        </section>
    </main>
</body>
</html>