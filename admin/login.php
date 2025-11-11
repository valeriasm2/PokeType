<?php
session_name("admin_session");
session_start();

// Incluir sistema de logs
require_once 'logger.php';

// ===============================
// CARGA AUTOMÁTICA DEL IDIOMA
// ===============================
$lang_dir = __DIR__ . "/../lang/";
$lang_files = glob($lang_dir . "*.php");
$valid_langs = array_map(fn($f) => basename($f, ".php"), $lang_files);

$browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'ca', 0, 2);

if (isset($_POST['lang'])) {
    $_SESSION['lang'] = $_POST['lang'];
    $lang = $_POST['lang'];

    if (!isset($_POST['user'])) {
        header("Location: login.php");
        exit;
    }
} elseif (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} else {
    $lang = in_array($browser_lang, $valid_langs) ? $browser_lang : ($valid_langs[0] ?? 'ca');
    $_SESSION['lang'] = $lang;
}

$lang_file = $lang_dir . "$lang.php";
$lang_data = file_exists($lang_file) ? include $lang_file : include $lang_dir . ($valid_langs[0] ?? "ca") . ".php";

// ===============================
// REDIRECCION SI YA ESTÁ LOGUEADO
// ===============================
if (!empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';
$error_type = '';

// ===============================
// PROCESAR LOGIN
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user'])) {

    $user = trim($_POST['user'] ?? '');
    $pass = trim($_POST['pass'] ?? '');

    if ($user === '' || $pass === '') {
        $error = "Tots els camps són obligatoris.";
        $error_type = "empty";

    } else {
        $cred_file = __DIR__ . '/credentials.txt';

        if (is_readable($cred_file)) {
            $creds = file($cred_file, FILE_IGNORE_NEW_LINES);

            if (!empty($creds)) {
                list($stored_user, $stored_pass) = explode(':', trim($creds[0]));

                if ($user !== $stored_user) {
                    $error = "Usuari no trobat.";
                    $error_type = "user";
                    escribirLog("LOGIN_FAILED", "login.php", "Intento de login con usuario incorrecto: $user");

                } elseif ($pass !== $stored_pass) {
                    $error = "Contrasenya incorrecta.";
                    $error_type = "pass";
                    escribirLog("LOGIN_FAILED", "login.php", "Intento de login con contraseña incorrecta para usuario: $user");

                } else {
                    // Login correcto
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_user'] = $user;

                    session_regenerate_id(true);

                    logAdmin("LOGIN_SUCCESS", "login.php", "Admin logueado correctamente");

                    header("Location: index.php");
                    exit;
                }
            } else {
                $error = "No s'han trobat credencials vàlides.";
                $error_type = "server";
            }
        } else {
            $error = "No s'han pogut llegir les credencials del servidor.";
            $error_type = "server";
        }
    }
}

// Texto del botón según idioma
if ($lang === 'en') {
    $btn_text = 'Login';
    $underline_letter = 'L';
} else {
    $btn_text = 'Entrar';
    $underline_letter = 'E';
}

?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang_data['admin_login']['title'] ?? 'Login Administrador' ?></title>
    <link rel="stylesheet" href="../styles.css?<?= time() ?>">
</head>
<body class="admin-page">

    <!-- Selector de idioma -->
    <form method="post" class="lang-selector-admin" action="login.php">
        <label for="lang">🌐</label>
        <select name="lang" id="lang" onchange="this.form.submit()">
            <option value="ca" <?= $lang === 'ca' ? 'selected' : '' ?>>Català</option>
            <option value="es" <?= $lang === 'es' ? 'selected' : '' ?>>Español</option>
            <option value="en" <?= $lang === 'en' ? 'selected' : '' ?>>English</option>
        </select>
    </form>

    <!-- Contenedor principal -->
    <div class="admin-container">
        <img src="../media/piplu.gif" alt="Piplup" class="admin-gif" />
        <h1><?= $lang_data['admin_login']['title'] ?? 'Login Administrador' ?></h1>

        <?php if ($error): ?>
            <p class="error <?= htmlspecialchars($error_type) ?>">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="post" autocomplete="off">
            <label for="user"><?= $lang_data['admin_login']['user'] ?? 'Usuari:' ?></label>
            <input type="text" name="user" id="user" value="<?= htmlspecialchars($user ?? '') ?>" >

            <label for="pass"><?= $lang_data['admin_login']['pass'] ?? 'Contrasenya:' ?></label>
            <input type="password" name="pass" id="pass" >

            <button type="submit" id="enter-btn">
                <span class="underline-letter"><?= $underline_letter ?></span><?= substr($btn_text,1) ?>
            </button>
        </form>
    </div>

    <script>
        const btn = document.getElementById("enter-btn");
        // Tecla de acceso según idioma
        const submitKey = "<?= strtolower($underline_letter) ?>";

        document.addEventListener("keydown", (e) => {
            const active = document.activeElement;
            if (active && ["INPUT","TEXTAREA","SELECT"].includes(active.tagName)) return;

            if (e.key.toLowerCase() === submitKey) {
                e.preventDefault();
                btn.click();
            }
        });
    </script>

</body>
</html>
