<?php
session_start(); // Gestionar sesión

// ✅ Selección de idioma
if (isset($_POST['lang'])) {
    $_SESSION['lang'] = $_POST['lang'];
}

$lang = $_SESSION['lang'] ?? 'ca';
$langFile = __DIR__ . "/lang/{$lang}.php";

if (file_exists($langFile)) {
    $langArray = require $langFile;

    // ✅ Cargar textos del bloque INDEX
    $texts = $langArray['index'];
    $hotkeys = $langArray['hotkeys'];
} else {
    $texts = [];
    $hotkeys = [];
}

// ✅ Incluir sistema de logs
require_once 'admin/logger.php';

$error = "";
$name = "";
$difficulty = "";

// ✅ Formulario enviado
if ($_POST && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    $difficulty = $_POST['difficulty'] ?? '';

    if (empty($name)) {
        $error = $texts['error_empty'] ?? "⚠️ El camp nom no pot estar buit";
    } else {
        $_SESSION['name'] = $name;
        $_SESSION['difficulty'] = $difficulty;

        // Log inicio
        logJuego("GAME_START", "index.php", "Usuario '$name' inició juego en dificultad '$difficulty'");

        header("Location: play.php");
        exit();
    }
}

// ✅ Recuperar sesión si existe
if (isset($_SESSION['name'])) $name = $_SESSION['name'];
if (isset($_SESSION['difficulty'])) $difficulty = $_SESSION['difficulty'];

?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Poketype</title>
        <link rel="stylesheet" href="styles.css?<?= time(); ?>">
    </head>

    <body>

        <!-- ✅ Recuadro usuario -->
        <?php if (isset($_SESSION['name'])): ?>
            <div id="user-box">
                👤 <strong><?= htmlspecialchars($_SESSION['name']); ?></strong><br>
                <a href="destroy_session.php"><?= $texts['logout'] ?? 'Cerrar sesión' ?></a>
            </div>
        <?php endif; ?>

        <!-- ✅ Audio botones -->
        <audio id="button-sound" src="boton.mp3" preload="auto"></audio>

        <!-- ✅ Selector de idioma (arriba izquierda) -->
        <form action="index.php" method="post" id="lang-selector">
            <label for="lang">🌐</label>
            <select name="lang" id="lang" onchange="this.form.submit()">
                <option value="ca" <?= ($lang === 'ca') ? 'selected' : '' ?>>Català</option>
                <option value="es" <?= ($lang === 'es') ? 'selected' : '' ?>>Español</option>
                <option value="en" <?= ($lang === 'en') ? 'selected' : '' ?>>English</option>
            </select>
        </form>

        <div id="index-container">
            <h1><?= $texts['welcome'] ?? 'Poketype' ?></h1>
            <p><?= $texts['description'] ?? '' ?></p>
            <img src="https://media.tenor.com/7nOwCz3oGYYAAAAi/gengar.gif" alt="Pokémon GIF" width="300">

            <!-- ✅ Formulario inicio del juego -->
            <form action="index.php" method="post">
                <label for="name"><?= $texts['name_label'] ?? 'Nombre:' ?></label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>"><br>

                <?php if ($error): ?>
                    <div class="error-alert"><?= $error ?></div>
                <?php endif; ?>

                <br>

                <label for="dificultat"><?= $texts['difficulty'] ?? 'Dificultad:' ?></label>
                <select name="difficulty" id="dificultat">
                    <option value="facil" <?= ($difficulty === "facil") ? "selected" : "" ?>>
                        <?= $texts['difficulty_facil'] ?? 'Fácil' ?>
                    </option>

                    <option value="normal" <?= ($difficulty === "normal") ? "selected" : "" ?>>
                        <?= $texts['difficulty_normal'] ?? 'Normal' ?>
                    </option>

                    <option value="dificil" <?= ($difficulty === "dificil") ? "selected" : "" ?>>
                        <?= $texts['difficulty_dificil'] ?? 'Difícil' ?>
                    </option>
                </select>

                <br><br>

                <?php $playText = $texts['play'] ?? 'Jugar'; ?>
                <button type="submit" id="play-button" disabled>
                    <span class="underline-letter"><?= substr($playText, 0, 1) ?></span><?= substr($playText, 1) ?>
                </button>

            </form>

            <noscript>
                <div class="error-alert">
                    <?= $texts['js_required'] ?? '⚠️ Este juego necesita JavaScript para funcionar.' ?>
                </div>
            </noscript>
        </div>

        <script src="utils/music.js"></script>
        <script>
            const playButton = document.getElementById('play-button');
            playButton.disabled = false;

            const buttons = document.querySelectorAll('button');
            const buttonSound = document.getElementById('button-sound');

            buttons.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    buttonSound.currentTime = 0;
                    buttonSound.play();

                    if (btn.type === 'submit') {
                        e.preventDefault();
                        setTimeout(() => btn.closest('form').submit(), 1000);
                    }
                });
            });

            document.addEventListener('keydown', (e) => {
                if (e.repeat) return;
                const active = document.activeElement;
                if (["INPUT", "TEXTAREA", "SELECT"].includes(active.tagName)) return;

                buttons.forEach(btn => {
                    const text = btn.textContent.trim().toLowerCase();
                    if (text.startsWith(e.key.toLowerCase())) btn.click();
                });
            });
        </script>
    </body>
</html>