<?php
session_name("admin_session");
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
$mensaje = "";
$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            $frases[$nivell][] = $frase;

            $json_nuevo = json_encode($frases, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if (file_put_contents($archivo, $json_nuevo) === false) {
                $mensaje = "Error: no s'ha pogut guardar el fitxer.";
                $error = true;
            } else {
                $mensaje = "Frase afegida correctament.";
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
            <a href="index.php" class="admin-link-btn" id="panel-link">
                <span class="underline-letter">T</span>ornar al panell
            </a>
            </p>

            <h1>Afegir una nova frase</h1>
            <form action="create_sentence.php" method="POST" id="create-form">
                <label for="nivell">Nivell de dificultat:</label>
                <select name="nivell" id="nivell" required>
                    <option value="">Selecciona un nivell</option>
                    <option value="facil">Fàcil</option>
                    <option value="normal">Normal</option>
                    <option value="dificil">Difícil</option>
                </select>

                <label for="frase">Frase:</label>
                <textarea name="frase" id="frase" rows="4" required></textarea>

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
            }
            if (teclita === "a") {
                document.getElementById("add-btn").click();
            }
        })

    </script>
</html>
