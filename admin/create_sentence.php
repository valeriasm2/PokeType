<?php
session_name("admin_session");
session_start();
require_once 'logger.php';

// Verificar login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$defaultLang = $_SESSION['lang'] ?? 'ca';
$lang_file = "../lang/$defaultLang.php";
if (!file_exists($lang_file)) $lang_file = "../lang/ca.php";
$lang_data = include($lang_file);

$mensaje = "";
$error = false;
$idiomas = $lang_data['lang_names'] ?? ['ca' => 'Català', 'es' => 'Español', 'en' => 'English'];
$archivos_frases = ['ca' => '../frases_ca.txt', 'es' => '../frases_es.txt', 'en' => '../frases_en.txt'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lang = $_POST['lang'] ?? $defaultLang;
    $archivo = $archivos_frases[$lang] ?? '../frases_ca.txt';
    unset($_SESSION['ultima_frase'], $_SESSION['ultim_nivell']);
    $nivell = $_POST['nivell'] ?? '';
    $frase  = trim($_POST['frase'] ?? '');

    if ($nivell === '' || $frase === '') {
        $mensaje = $lang_data['messages']['error_datos'] ?? "Error: incomplete data";
        $error = true;
    } else {
        $frases = file_exists($archivo) ? json_decode(file_get_contents($archivo), true) : ['facil' => [], 'normal' => [], 'dificil' => []];
        if ($frases === null) {
            $mensaje = $lang_data['messages']['error_json'] ?? "Error: malformed file";
            $error = true;
        }

        if (!$error) {
            if (!isset($frases[$nivell])) $frases[$nivell] = [];
            $nombreImagen = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $nombreImagen = basename($_FILES['imagen']['name']);
                $rutaDestino = '../images/' . $nombreImagen;
                if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                    $mensaje = $lang_data['messages']['error_guardado'] ?? "Error: could not save the image.";
                    $error = true;
                    $nombreImagen = null;
                }
            }

            if (!$error) {
                $nuevaFrase = ['texto' => $frase, 'imagen' => $nombreImagen];
                $frases[$nivell][] = $nuevaFrase;
                if (file_put_contents($archivo, json_encode($frases, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
                    $mensaje = $lang_data['messages']['error_guardado'] ?? "Error: could not save the file.";
                    $error = true;
                } else {
                    $mensaje = str_replace('{lang}', $idiomas[$lang], $lang_data['admin_create']['success_lang'] ?? "Sentence added successfully in language: " . $idiomas[$lang]);
                    $_SESSION['ultima_frase'] = $frase;
                    $_SESSION['ultim_nivell'] = $nivell;
                    logAdmin("CREATE_SENTENCE", "create_sentence.php", "New sentence in '$nivell', language '$lang': '$frase'");
                }
            }
        }
    }
}

function underlineFirstLetter($text)
{
    if (empty($text)) return '';
    $first = substr($text, 0, 1);
    $rest  = substr($text, 1);
    return "<span class='underline-letter'>{$first}</span>{$rest}";
}
?>

<!DOCTYPE html>
<html lang="<?= $defaultLang ?>">

<head>
    <meta charset="UTF-8">
    <title><?= $lang_data['admin_create']['title'] ?? "Create New Sentence" ?></title>
    <link rel="stylesheet" href="../styles.css?<?= time() ?>">
</head>

<body class="admin-page-index">
    <div class="admin-container-index">

        <p>
            <?= sprintf($lang_data['index']['welcome'] ?? "Welcome, %s", htmlspecialchars($_SESSION['admin_user'])) ?> |

            <a href="logout.php" class="admin-link-btn logout">
                <?= underlineFirstLetter($lang_data['index']['logout'] ?? "Logout") ?>
            </a> |

            <?php
            $panel_url = 'index.php';
            if (isset($_SESSION['ultim_nivell'])) {
                $panel_url .= '?action=llistar&nivell=' . urlencode($_SESSION['ultim_nivell']);
            }
            ?>
            <a href="<?= $panel_url ?>" class="admin-link-btn">
                <?= underlineFirstLetter($lang_data['admin_create']['back'] ?? "Back to panel") ?>
            </a>
        </p>

        <h1><?= $lang_data['admin_create']['title'] ?? "Create New Sentence" ?></h1>

        <form action="create_sentence.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="lang" value="<?= $defaultLang ?>">

            <p class="info-idioma">
                <?= $lang_data['admin_create']['info_lang'] ?? "The sentence will be saved in the list for the selected language:" ?>
                <strong><?= $idiomas[$defaultLang] ?></strong>
            </p>

            <label for="nivell"><?= $lang_data['admin_create']['difficulty'] ?? "Difficulty:" ?></label>
            <select name="nivell" id="nivell">
                <option value=""><?= $lang_data['admin_create']['select_level'] ?? "Select a level" ?></option>
                <option value="facil"><?= $lang_data['admin_index']['levels']['facil'] ?? "Easy" ?></option>
                <option value="normal"><?= $lang_data['admin_index']['levels']['normal'] ?? "Normal" ?></option>
                <option value="dificil"><?= $lang_data['admin_index']['levels']['dificil'] ?? "Hard" ?></option>
            </select>

            <label for="frase"><?= $lang_data['admin_create']['text'] ?? "Sentence text:" ?></label>
            <textarea name="frase" id="frase" rows="4"></textarea>

            <label><?= $lang_data['admin_create']['image'] ?? "Image name:" ?></label>
            <div class="custom-file">
                <span id="custom-file-text" class="custom-file-text"><?= $lang_data['admin_create']['select_file'] ?? "No file selected" ?></span>
                <input type="file" name="imagen" id="imagen" accept="image/*">
            </div>

            <br><br>
            <button type="submit"><?= $lang_data['admin_create']['save'] ?? "Save" ?></button>

            <?php if ($mensaje): ?>
                <div class="<?= $error ? 'error' : 'success' ?>">
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>
        </form>

    </div>

    <script>
        const inputFile = document.getElementById('imagen');
        const fileText = document.getElementById('custom-file-text');
        inputFile.addEventListener('change', function() {
            fileText.textContent = this.files.length ?
                this.files[0].name :
                "<?= addslashes($lang_data['admin_create']['select_file'] ?? "No file selected") ?>";
        });
    </script>
</body>

</html>