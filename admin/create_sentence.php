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

            <?php if ($mensaje): ?>
                <div style="color: <?php echo $error ? 'red' : 'green'; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="frase">Frase:</label>
                <textarea id="frase" name="frase" rows="4" cols="50"n style="<?php echo ($error && $frase === '') ? 'border: 2px solid red;' : ''; ?>">
                    <?php echo isset($_POST['frase']) ? htmlspecialchars($_POST['frase']) : ''; ?></textarea>

                <label for="nivell">Nivell de dificultat:</label>
                <select id="nivell" name="nivell">
                    <option value="" disabled <?php echo !isset($_POST['nivell']) ? 'selected' : ''; ?>>-- Selecciona un nivell --</option>
                    <option value="facil" <?php echo (isset($_POST['nivell']) && $_POST['nivell'] === 'facil') ? 'selected' : ''; ?>>Fàcil</option>
                    <option value="normal" <?php echo (isset($_POST['nivell']) && $_POST['nivell'] === 'normal') ? 'selected' : ''; ?>>Normal</option>
                    <option value="dificil" <?php echo (isset($_POST['nivell']) && $_POST['nivell'] === 'dificil') ? 'selected' : ''; ?>>Difícil</option>
                </select>

                <button type="submit" id="add-btn">
                    <span class="underline-letter">A</span>fegir frase
                </button>
            </form>
        </div>

        <script>
            document.addEventListener("keydown", (e) => {
                // ✅ Verificar si estamos escribiendo en un campo de texto
                const elementoActivo = document.activeElement;
                const esElementoEscritura = elementoActivo && (
                    elementoActivo.tagName === 'INPUT' || 
                    elementoActivo.tagName === 'TEXTAREA' || 
                    elementoActivo.tagName === 'SELECT' ||
                    elementoActivo.isContentEditable ||
                    elementoActivo.type === 'text' ||
                    elementoActivo.type === 'password' ||
                    elementoActivo.type === 'email' ||
                    elementoActivo.type === 'search' ||
                    elementoActivo.contentEditable === 'true'
                );
                
                // Si estamos escribiendo, no activar atajos
                if (esElementoEscritura) {
                    return; // Salir sin procesar atajos
                }

                const key = e.key.toLowerCase();

                if (key === "l") {
                    e.preventDefault();
                    window.location.href = "logout.php";
                } else if (key === "t") {
                    e.preventDefault();
                    window.location.href = "index.php";
                } else if (key === "a") {
                    e.preventDefault();
                    const addBtn = document.getElementById("add-btn");
                    if (addBtn) addBtn.click();
                }
            });
        </script>
    </body>
</html>
