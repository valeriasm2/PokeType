<?php
session_name("admin_session");
session_start();

// Incluir sistema de logs
require_once 'logger.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
$mensaje = "";
$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //me cargo la frase resaltada anterior para no duplicar y generar una nueva sin confusión
    unset($_SESSION['ultima_frase']);
    unset($_SESSION['ultim_nivell']);
    $nivell = $_POST['nivell'] ?? '';
    $frase = trim($_POST['frase'] ?? '');
    
    if ($nivell === '' || $frase === '') {
        $mensaje = "Error: has de seleccionar un nivell i escriure una frase.";
        $error = true;
    } else {
        $archivo = '../frases.txt';

        if (!file_exists($archivo)) {
            $frases = [
                'facil' => [],
                'normal' => [],
                'dificil' => []
            ];
        } else {
            $contenido = file_get_contents($archivo);
            $frases = json_decode($contenido, true);

            if ($frases === null) {
                $mensaje = "Error: fitxer de frases mal format.";
                $error = true;
            }
        }
        if (!$error) {
            if (!isset($frases[$nivell])) {
                $frases[$nivell] = [];
            }
            //========MANEJO DE IMAGENES SUBIDAS=========
            $nombreImagen = null; // Por defecto sin imagen
    
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                
                $nombreImagen = $_FILES['imagen']['name'];
                $rutaDestino = '../images/' . $nombreImagen;
                
                if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                    $mensaje = "Error: no s'ha pogut guardar la imatge.";
                    $error = true;
                    $nombreImagen = null;
                }
               
            }
            // Crear objeto frase con texto e imagen 
            $nuevaFrase = [
                'texto' => $frase,
                'imagen' => $nombreImagen 
            ];
            
            $frases[$nivell][] = $nuevaFrase;

            $json_nuevo = json_encode($frases, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if (file_put_contents($archivo, $json_nuevo) === false) {
                $mensaje = "Error: no s'ha pogut guardar el fitxer.";
                $error = true;
            } else {
                $mensaje = "Frase afegida correctament.";
                // Log creación exitosa de frase
                $imagenInfo = $nombreImagen ? " con imagen: $nombreImagen" : " sin imagen";
                logAdmin("CREATE_SENTENCE", "create_sentence.php", "Nueva frase creada en nivel '$nivell': '$frase'$imagenInfo");
                //me guardo la última frase y nivel para resaltarla al volver al listado!!!
                $_SESSION['ultima_frase'] = $frase;
                $_SESSION['ultim_nivell'] = $nivell;
                
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
    <head>
        <meta charset="UTF-8">
        <title>Afegir Frase</title>
        <link rel="stylesheet" href="../styles.css?<?php echo time(); ?>">
    </head>
    <body class="admin-page-index">
        <div class="admin-container-index">
            <p> Benvingut, <strong><?php echo htmlspecialchars($_SESSION['admin_user']); ?></strong> | 
            <a href="logout.php" class="admin-link-btn logout" id="logout-link">
                <span class="underline-letter">L</span>ogout
            </a>
            |
            <?php
            // Enlace al panel con el nivel correcto si existe, te redirecciona al listado del último nivel agregado
            $panel_url = 'index.php';
            if (isset($_SESSION['ultim_nivell'])) {
                $panel_url .= '?action=llistar&nivell=' . urlencode($_SESSION['ultim_nivell']);
            }
            ?>
            <a href="<?= $panel_url ?>" class="admin-link-btn" id="panel-link">
                <span class="underline-letter">T</span>ornar al panell
            </a>
            </p>

            <h1>Afegir una nova frase</h1>
            <form action="create_sentence.php" method="POST" enctype="multipart/form-data" id="create-form">
                <label for="nivell">Nivell de dificultat:</label>
                <select name="nivell" id="nivell" required>
                    <option value="">Selecciona un nivell</option>
                    <option value="facil">Fàcil</option>
                    <option value="normal">Normal</option>
                    <option value="dificil">Difícil</option>
                </select>

                <label for="frase">Frase:</label>
                <textarea name="frase" id="frase" rows="4" required></textarea>
                
                <label for="imagen">Imatge (opcional):</label>
                <input type="file" name="imagen" id="imagen" accept="image/*">
                

                <button type="submit" id="add-btn">
                    <span class="underline-letter">A</span>fegir frase
                </button>

                <?php if ($mensaje): ?>
                    <div class="admin-message <?php echo $error ? 'error' : 'success'; ?>">
                        <?php echo $error ? '❌' : '✅'; ?> <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </body>
    <script>
        
        //esto es pa que no joda los atajos cuando el usuario está escribiendo
        
        document.addEventListener("keydown", function(event){
            if (document.activeElement.tagName === 'INPUT' || 
                document.activeElement.tagName === 'TEXTAREA' ||
                document.activeElement.tagName === 'SELECT' ||
                document.activeElement.isContentEditable ) {
                return; 
            }
            const teclita = event.key.toLowerCase();
            if (teclita === "l") {
                window.location.href = "logout.php";
            }
            if (teclita === "t") {
                window.location.href = "index.php";
                document.getElementById("panel-link").click();
            }
            if (teclita === "a") {
                document.getElementById("add-btn").click();
            }
        })

    </script>
</html>
