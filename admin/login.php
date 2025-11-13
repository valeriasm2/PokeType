<?php
session_name("admin_session");
session_start();

// Incluir sistema de logs
require_once 'logger.php';
require_once __DIR__ . '/../utils/lang.php';

// Selector de idioma antes de login
if (isset($_POST['setlang']) && isset($_POST['lang'])) {
    $newLang = $_POST['lang'];
    if (in_array($newLang, pt_supported_langs(), true)) {
        $_SESSION['lang'] = $newLang;
    }
    header('Location: login.php');
    exit;
}

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';
$error_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['user'] ?? '');
    $pass = trim($_POST['pass'] ?? '');

    if ($user === '' || $pass === '') {
        $error = t('admin.login.errors.empty');
        $error_type = "empty";
    } else {
        $creds = file(__DIR__ . '/credentials.txt', FILE_IGNORE_NEW_LINES);
        if ($creds) {
            list($stored_user, $stored_pass) = explode(':', trim($creds[0]));

            if ($user !== $stored_user) {
                $error = t('admin.login.errors.user');
                $error_type = "user";
                // Log intento de login con usuario incorrecto
                escribirLog("LOGIN_FAILED", "login.php", "Intento login con usuario incorrecto: $user");
            } elseif ($pass !== $stored_pass) {
                $error = t('admin.login.errors.pass');
                $error_type = "pass";
                // Log intento de login con contraseña incorrecta
                escribirLog("LOGIN_FAILED", "login.php", "Intento login con contraseña incorrecta para usuario: $user");
            } else {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user'] = $user;
                session_regenerate_id(true);
                // Log login exitoso
                logAdmin("LOGIN_SUCCESS", "login.php", "Admin logueado correctamente");
                header("Location: index.php");
                exit;
            }
        } else {
            $error = t('admin.login.errors.server');
            $error_type = "server";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(pt_current_lang()); ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Administrador</title>
        <link rel="stylesheet" href="../styles.css?<?php echo time(); ?>">
    </head>
    <body class="admin-page">
        <div class="admin-container">
            <!-- Selector de idioma para Admin Login -->
            <form action="login.php" method="post" style="position:fixed; top:10px; left:10px; white-space:nowrap; z-index:9999;">
                <input type="hidden" name="setlang" value="1">
                <label for="lang" style="margin-right:6px;"><?= htmlspecialchars(t('admin.language_label')); ?></label>
                <?php $cur = pt_current_lang(); $names = pt_load_messages($cur)['lang_names'] ?? ['es'=>'Español','ca'=>'Català','en'=>'English']; ?>
                <select name="lang" id="lang" onchange="this.form.submit()">
                    <option value="es" <?= $cur==='es'?'selected':''; ?>><?= htmlspecialchars($names['es'] ?? 'Español'); ?></option>
                    <option value="ca" <?= $cur==='ca'?'selected':''; ?>><?= htmlspecialchars($names['ca'] ?? 'Català'); ?></option>
                    <option value="en" <?= $cur==='en'?'selected':''; ?>><?= htmlspecialchars($names['en'] ?? 'English'); ?></option>
                </select>
            </form>
            <img src="../media/piplu.gif" alt="Piplup" class="admin-gif" />
            <h1><?= htmlspecialchars(t('admin.login.title')); ?></h1>
            <?php 
            if ($error) {
                echo "<p class='error $error_type'>$error</p>";
            }
            ?>
            <form method="post">
                <label><?= htmlspecialchars(t('admin.login.user_label')); ?></label>
                <input type="text" name="user" value="<?php echo htmlspecialchars($user ?? ''); ?>">
                <label><?= htmlspecialchars(t('admin.login.pass_label')); ?></label>
                <input type="password" name="pass">
                <button type="submit" id="enter-btn">
                    <?= htmlspecialchars(t('admin.login.enter')); ?>
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

                if (e.key.toLowerCase() === "e") {
                    e.preventDefault();
                    document.getElementById("enter-btn").click();
                }
            });
        </script>

    </body>
    </html>
