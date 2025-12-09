<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// IMPORTANTE: Incluir las clases ANTES de session_start()
require_once('Cronometro.class.php');
require_once('PruebaUsabilidad.class.php');

session_start();

// Variables de control
$estado = isset($_SESSION['estado_prueba']) ? $_SESSION['estado_prueba'] : 'datos_usuario';
$mensaje = '';
$error = '';

// Obtener pericias para el formulario
$prueba = new PruebaUsabilidad();
$pericias = $prueba->obtenerPericias();

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    if (isset($_POST['guardar_usuario'])) {
        // Validar y guardar datos del usuario
        $idUsuario = trim($_POST['idUsuario']);
        $profesion = trim($_POST['profesion']);
        $edad = intval($_POST['edad']);
        $genero = $_POST['genero'];
        $idPericia = intval($_POST['idPericia']);
        
        if (empty($idUsuario) || empty($profesion) || $edad <= 0) {
            $error = 'Por favor, completa todos los campos correctamente.';
        } else {
            if ($prueba->guardarUsuario($idUsuario, $profesion, $edad, $genero, $idPericia)) {
                $_SESSION['idUsuario'] = $idUsuario;
                $_SESSION['estado_prueba'] = 'inicio';
                $estado = 'inicio';
                $mensaje = 'Usuario registrado correctamente. Ahora puedes comenzar la prueba.';
            } else {
                $error = 'Error al guardar los datos del usuario.';
            }
        }
        
    } elseif (isset($_POST['iniciar_prueba'])) {
        // Iniciar cronómetro INVISIBLE para el usuario
        $_SESSION['cronometro_test'] = new Cronometro();
        $_SESSION['cronometro_test']->arrancar();
        $_SESSION['dispositivo'] = $_POST['dispositivo'];
        $_SESSION['estado_prueba'] = 'en_curso';
        $estado = 'en_curso';
        
    } elseif (isset($_POST['terminar_prueba'])) {
        // Detener cronómetro INVISIBLE
        if (isset($_SESSION['cronometro_test'])) {
            $_SESSION['cronometro_test']->parar();
            
            // Validar que todas las preguntas estén respondidas
            $todasRespondidas = true;
            for ($i = 1; $i <= 10; $i++) {
                if (!isset($_POST['pregunta' . $i]) || empty($_POST['pregunta' . $i])) {
                    $todasRespondidas = false;
                    break;
                }
            }
            
            if ($todasRespondidas) {
                // Guardar respuestas temporalmente
                $_SESSION['respuestas'] = [];
                for ($i = 1; $i <= 10; $i++) {
                    $_SESSION['respuestas']['pregunta' . $i] = $_POST['pregunta' . $i];
                }
                $_SESSION['estado_prueba'] = 'feedback_usuario';
                $estado = 'feedback_usuario';
            } else {
                $error = 'Debes responder a todas las preguntas antes de terminar la prueba.';
                $estado = 'en_curso';
            }
        }
        
    } elseif (isset($_POST['guardar_feedback'])) {
        // Guardar feedback del usuario
        $_SESSION['comentariosUsuario'] = trim($_POST['comentariosUsuario']);
        $_SESSION['propuestasMejora'] = trim($_POST['propuestasMejora']);
        $_SESSION['valoracion'] = intval($_POST['valoracion']);
        $_SESSION['estado_prueba'] = 'observaciones_facilitador';
        $estado = 'observaciones_facilitador';
        
    } elseif (isset($_POST['guardar_observaciones'])) {
        // Guardar todo en base de datos
        if (isset($_SESSION['cronometro_test']) && isset($_SESSION['idUsuario'])) {
            // USAR EL MÉTODO getTiempo() en lugar de acceder directamente a ->tiempo
            $tiempoTotal = $_SESSION['cronometro_test']->getTiempo();
            $idUsuario = $_SESSION['idUsuario'];
            $dispositivo = $_SESSION['dispositivo'];
            $comentariosUsuario = isset($_SESSION['comentariosUsuario']) ? $_SESSION['comentariosUsuario'] : '';
            $propuestasMejora = isset($_SESSION['propuestasMejora']) ? $_SESSION['propuestasMejora'] : '';
            $valoracion = $_SESSION['valoracion'];
            $comentariosFacilitador = trim($_POST['comentariosFacilitador']);
            
            // Determinar si la tarea se completó (todas las preguntas respondidas)
            $tareaCompletada = 1;
            
            $idPrueba = $prueba->guardarPrueba(
                $idUsuario, 
                $dispositivo, 
                $tiempoTotal, 
                $tareaCompletada, 
                $comentariosUsuario, 
                $propuestasMejora, 
                $valoracion
            );
            
            if ($idPrueba) {
                if (!empty($comentariosFacilitador)) {
                    $prueba->guardarObservacion($idPrueba, $comentariosFacilitador);
                }
                $_SESSION['estado_prueba'] = 'finalizada';
                $estado = 'finalizada';
                $mensaje = 'Prueba guardada correctamente en la base de datos.';
            } else {
                $error = 'Error al guardar los resultados en la base de datos.';
            }
        }
        
    } elseif (isset($_POST['nueva_prueba'])) {
        // Reiniciar todo
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['estado_prueba'] = 'datos_usuario';
        $estado = 'datos_usuario';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Prueba de usabilidad del proyecto MotoGP Desktop">
    <meta name="author" content="Eloy Rubio Suárez">
    <meta name="keywords" content="motos,motor,deporte,usabilidad,test,evaluación,motogp">
    <title>Test de Usabilidad - MotoGP Desktop</title>
    <link rel="stylesheet" type="text/css" href="./estilo/estilo.css">
    <link rel="stylesheet" type="text/css" href="./estilo/layout.css">
    <link rel="icon" href="./multimedia/icon.png" type="image/png">
</head>
<body>
    <header>
        <section>
            <h1>Test de Usabilidad - MotoGP Desktop</h1>
        </section>
    </header>
    
    <main>
        <?php if (!empty($error)): ?>
        <section>
            <article>
                <h3>Error</h3>
                <p><?php echo htmlspecialchars($error); ?></p>
            </article>
        </section>
        <?php endif; ?>
        
        <?php if (!empty($mensaje)): ?>
        <section>
            <article>
                <h3>Información</h3>
                <p><?php echo htmlspecialchars($mensaje); ?></p>
            </article>
        </section>
        <?php endif; ?>
        
        <?php if ($estado === 'datos_usuario'): ?>
        <section>
            <h2>Datos del Participante</h2>
            <article>
                <h3>Información Personal</h3>
                <p>Antes de comenzar la prueba, necesitamos algunos datos básicos para identificarte.</p>
                <p>Todos los campos son obligatorios.</p>
            </article>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="idUsuario">Identificador de Usuario (único):</label>
                <input type="text" id="idUsuario" name="idUsuario" required maxlength="50" placeholder="Ej: usuario001">
                
                <label for="profesion">Profesión:</label>
                <input type="text" id="profesion" name="profesion" required maxlength="100" placeholder="Ej: Estudiante, Ingeniero, etc.">
                
                <label for="edad">Edad:</label>
                <input type="number" id="edad" name="edad" required min="1" max="120" placeholder="Introduce tu edad">
                
                <label for="genero">Género:</label>
                <select id="genero" name="genero" required>
                    <option value="">Selecciona una opción</option>
                    <option value="Masculino">Masculino</option>
                    <option value="Femenino">Femenino</option>
                    <option value="Otro">Otro</option>
                    <option value="Prefiero no decirlo">Prefiero no decirlo</option>
                </select>
                
                <label for="idPericia">Nivel de Pericia Informática:</label>
                <select id="idPericia" name="idPericia" required>
                    <option value="">Selecciona tu nivel</option>
                    <?php foreach ($pericias as $pericia): ?>
                        <option value="<?php echo $pericia['idPericia']; ?>">
                            <?php echo htmlspecialchars($pericia['nivelPericia']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" name="guardar_usuario">Registrar y Continuar</button>
            </form>
        </section>
        
        <?php elseif ($estado === 'inicio'): ?>
        <section>
            <h2>Bienvenido a la Prueba de Usabilidad</h2>
            <article>
                <h3>Instrucciones</h3>
                <p>Hola <strong><?php echo htmlspecialchars($_SESSION['idUsuario']); ?></strong>, gracias por participar en esta prueba de usabilidad.</p>
                <p>Esta prueba consiste en responder 10 preguntas sobre el proyecto MotoGP Desktop.</p>
                <p><strong>Puedes consultar el sitio web de MotoGP Desktop en otra pestaña</strong> mientras realizas la prueba.</p>
                <p>Una vez que pulses el botón "Iniciar Prueba", podrás comenzar a responder las preguntas.</p>
                <p>Responde a todas las preguntas con calma y pulsa "Terminar Prueba" cuando hayas acabado.</p>
                <p><em>Nota: El tiempo de realización será registrado automáticamente para análisis estadístico.</em></p>
            </article>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="dispositivo">Dispositivo que estás usando:</label>
                <select id="dispositivo" name="dispositivo" required>
                    <option value="">Selecciona tu dispositivo</option>
                    <option value="Ordenador">Ordenador</option>
                    <option value="Tableta">Tableta</option>
                    <option value="Teléfono">Teléfono</option>
                </select>
                
                <button type="submit" name="iniciar_prueba">Iniciar Prueba</button>
            </form>
        </section>
        
        <?php elseif ($estado === 'en_curso'): ?>
        <section>
            <h2>Preguntas de Usabilidad</h2>
            <p>Por favor, responde a todas las preguntas. <strong>Puedes consultar el sitio web en otra pestaña.</strong></p>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <article>
                    <h3>Pregunta 1</h3>
                    <p>¿En qué sección del sitio web puedes encontrar información sobre los pilotos de MotoGP?</p>
                    <label><input type="radio" name="pregunta1" value="a" required> Inicio</label>
                    <label><input type="radio" name="pregunta1" value="b"> Piloto</label>
                    <label><input type="radio" name="pregunta1" value="c"> Circuito</label>
                    <label><input type="radio" name="pregunta1" value="d"> Clasificaciones</label>
                </article>
                
                <article>
                    <h3>Pregunta 2</h3>
                    <p>¿Dónde se encuentra el menú de navegación principal del sitio?</p>
                    <label><input type="radio" name="pregunta2" value="a" required> En el pie de página</label>
                    <label><input type="radio" name="pregunta2" value="b"> En la cabecera</label>
                    <label><input type="radio" name="pregunta2" value="c"> En una barra lateral</label>
                    <label><input type="radio" name="pregunta2" value="d"> No hay menú</label>
                </article>
                
                <article>
                    <h3>Pregunta 3</h3>
                    <p>¿Qué tipo de información puedes consultar en la sección "Meteorología"?</p>
                    <label><input type="radio" name="pregunta3" value="a" required> Clasificaciones de pilotos</label>
                    <label><input type="radio" name="pregunta3" value="b"> Datos meteorológicos</label>
                    <label><input type="radio" name="pregunta3" value="c"> Calendario de carreras</label>
                    <label><input type="radio" name="pregunta3" value="d"> Noticias</label>
                </article>
                
                <article>
                    <h3>Pregunta 4</h3>
                    <p>¿Cuántas opciones de juego/aplicaciones aproximadamente hay en la sección "Juegos"?</p>
                    <label><input type="radio" name="pregunta4" value="a" required> 1-2</label>
                    <label><input type="radio" name="pregunta4" value="b"> 3-5</label>
                    <label><input type="radio" name="pregunta4" value="c"> 6-8</label>
                    <label><input type="radio" name="pregunta4" value="d"> Más de 8</label>
                </article>
                
                <article>
                    <h3>Pregunta 5</h3>
                    <p>¿Cuál es el nombre del piloto?</p>
                    <label><input type="radio" name="pregunta5" value="a" required> Enea Bastianini</label>
                    <label><input type="radio" name="pregunta5" value="b"> Marc Márquez</label>
                    <label><input type="radio" name="pregunta5" value="c"> Valentino Rossi</label>
                    <label><input type="radio" name="pregunta5" value="d"> No lo sé</label>
                </article>
                
                <article>
                    <h3>Pregunta 6</h3>
                    <p>¿Cuál es el país del circuito?</p>
                    <label><input type="radio" name="pregunta6" value="a" required> España</label>
                    <label><input type="radio" name="pregunta6" value="b"> Alemania</label>
                    <label><input type="radio" name="pregunta6" value="c"> Austria</label>
                    <label><input type="radio" name="pregunta6" value="d"> Australia</label>
                </article>
                
                <article>
                    <h3>Pregunta 7</h3>
                    <p>¿Hay alguna sección de ayuda o soporte en el sitio web?</p>
                    <label><input type="radio" name="pregunta7" value="a" required> No existe</label>
                    <label><input type="radio" name="pregunta7" value="b"> Sí, en "Ayuda"</label>
                    <label><input type="radio" name="pregunta7" value="c"> Solo en el pie de página</label>
                    <label><input type="radio" name="pregunta7" value="d"> No lo he encontrado</label>
                </article>
                
                <article>
                    <h3>Pregunta 8</h3>
                    <p>¿Cuál es la población del lugar del circuito?</p>
                    <label><input type="radio" name="pregunta8" value="a" required> 8000</label>
                    <label><input type="radio" name="pregunta8" value="b"> 100000</label>
                    <label><input type="radio" name="pregunta8" value="c"> Más de 100000</label>
                    <label><input type="radio" name="pregunta8" value="d"> Pequeño, menos de 8000</label>
                </article>
                
                <article>
                    <h3>Pregunta 9</h3>
                    <p>¿Cuál es el dorsal del piloto?</p>
                    <label><input type="radio" name="pregunta9" value="a" required> 24</label>
                    <label><input type="radio" name="pregunta9" value="b"> 32</label>
                    <label><input type="radio" name="pregunta9" value="c"> 33</label>
                    <label><input type="radio" name="pregunta9" value="d">23</label>
                </article>
                
                <article>
                    <h3>Pregunta 10</h3>
                    <p>¿Acabó en el top 5 en la temporada 2024?</p>
                    <label><input type="radio" name="pregunta10" value="a" required> Sí</label>
                    <label><input type="radio" name="pregunta10" value="b"> No</label>
                    <label><input type="radio" name="pregunta10" value="c"> No lo sé</label>
                </article>
                
                <button type="submit" name="terminar_prueba">Terminar Prueba</button>
            </form>
        </section>
        
        <?php elseif ($estado === 'feedback_usuario'): ?>
        <section>
            <h2>Feedback del Usuario</h2>
            <article>
                <h3>Tu Opinión es Importante</h3>
                <p>Ahora que has completado las preguntas, nos gustaría conocer tu opinión general sobre el sitio web.</p>
            </article>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="comentariosUsuario">Comentarios generales (opcional):</label>
                <textarea id="comentariosUsuario" name="comentariosUsuario" rows="4" placeholder="¿Qué te ha parecido el sitio web en general?"></textarea>
                
                <label for="propuestasMejora">Propuestas de mejora (opcional):</label>
                <textarea id="propuestasMejora" name="propuestasMejora" rows="4" placeholder="¿Qué mejorarías del sitio web?"></textarea>
                
                <label for="valoracion">Valoración general (0-10):</label>
                <input type="number" id="valoracion" name="valoracion" required min="0" max="10" value="5">
                
                <button type="submit" name="guardar_feedback">Continuar</button>
            </form>
        </section>
        
        <?php elseif ($estado === 'observaciones_facilitador'): ?>
        <section>
            <h2>Observaciones del Facilitador</h2>
            <article>
                <h3>Comentarios del Observador</h3>
                <p>Como facilitador de esta prueba, puedes añadir comentarios sobre el comportamiento del usuario, dificultades encontradas, o cualquier otra observación relevante.</p>
            </article>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="comentariosFacilitador">Observaciones del facilitador:</label>
                <textarea id="comentariosFacilitador" name="comentariosFacilitador" rows="6" required placeholder="Escribe aquí tus observaciones sobre la prueba..."></textarea>
                
                <button type="submit" name="guardar_observaciones">Guardar Resultados</button>
            </form>
        </section>
        
        <?php elseif ($estado === 'finalizada'): ?>
        <section>
            <h2>Prueba Completada</h2>
            <article>
                <h3>Gracias por Participar</h3>
                <p>La prueba de usabilidad ha sido completada y los resultados han sido guardados correctamente en la base de datos.</p>
                <p>Usuario: <strong><?php echo htmlspecialchars($_SESSION['idUsuario']); ?></strong></p>
                <p>Los datos recopilados serán analizados para mejorar la experiencia de usuario del proyecto MotoGP Desktop.</p>
                <p>Tus comentarios y sugerencias son muy valiosos para nosotros.</p>
            </article>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <button type="submit" name="nueva_prueba">Realizar Nueva Prueba</button>
            </form>
        </section>
        <?php endif; ?>
    </main>
</body>
</html>