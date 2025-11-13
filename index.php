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
    $texts   = $langArray['index'] ?? [];
    $hotkeys = $langArray['hotkeys'] ?? [];
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
    $name       = trim($_POST['name']);
    $difficulty = $_POST['difficulty'] ?? '';
    $permadeath = isset($_POST['permadeath']) && $_POST['permadeath'] === '1';

    if (empty($name)) {
        $error = $texts['error_empty'] ?? "⚠️ El campo nombre no puede estar vacío";
    } else {
        $_SESSION['name']       = $name;              
        $_SESSION['difficulty'] = $difficulty;  
        
        if ($permadeath) {
            $_SESSION['permadeath'] = true;
            logJuego("GAME_START_PERMADEATH", "index.php", "Usuario '$name' inició partida en modo permadeath (5 vidas)");
            header("Location: play.php?permadeath=1");
        } else {
            unset($_SESSION['permadeath']);
            logJuego("GAME_START", "index.php", "Usuario '$name' escogió juego en dificultad '$difficulty'");
            header("Location: play.php");
        }
        exit();
    }
}

// ✅ Recuperar sesión si existe
if (isset($_SESSION['name']))       $name = $_SESSION['name'];
if (isset($_SESSION['difficulty'])) $difficulty = $_SESSION['difficulty'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
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

    <!-- ✅ Selector de idioma -->
    <form action="index.php" method="post" id="lang-selector-form">
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

            <label for="difficulty"><?= $texts['difficulty'] ?? 'Dificultad:' ?></label>
            <select name="difficulty" id="difficulty">
                <option value="facil"  <?= ($difficulty === "facil") ? "selected" : "" ?>>
                    <?= $texts['difficulty_facil'] ?? 'Fácil' ?>
                </option>
                <option value="normal" <?= ($difficulty === "normal") ? "selected" : "" ?>>
                    <?= $texts['difficulty_normal'] ?? 'Normal' ?>
                </option>
                <option value="dificil" <?= ($difficulty === "dificil") ? "selected" : "" ?>>
                    <?= $texts['difficulty_dificil'] ?? 'Difícil' ?>
                </option>
            </select>

            <!-- Mode Permadeath -->
            <input type="checkbox" id="permadeath" name="permadeath" value="1" />
            <label for="permadeath">
                <?= $texts['permadeath_label'] ?? 'Modo permadeath' ?>
            </label>

            <button type="button" id="perma-info" class="info-btn" title="<?= $texts['permadeath_info_title'] ?? '¿Qué es el modo permadeath?' ?>">?</button>

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
                if (btn.id === 'perma-info') {
                    e.preventDefault();
                    alert("<?= $texts['permadeath_info'] ?? 'Modo Permadeath:\nSi lo activas solo tienes 5 vidas y la partida termina cuando las gastas. Puedes recibir un bonus por jugar en este modo.' ?>");
                    return;
                }

                const permaCheckbox = document.getElementById('permadeath');
                const permaChecked = permaCheckbox && permaCheckbox.checked;

                if (btn.type === 'submit') {
                    if (permaChecked) {
                        const ok = confirm("<?= $texts['permadeath_confirm'] ?? 'Has seleccionado Modo Permadeath: solo 5 vidas. ¿Quieres continuar?' ?>");
                        if (!ok) {
                            e.preventDefault();
                            return;
                        }
                    }
                    buttonSound.currentTime = 0;
                    buttonSound.play();
                    e.preventDefault();
                    setTimeout(() => btn.closest('form').submit(), 1000);
                    return;
                }
                buttonSound.currentTime = 0;
                buttonSound.play();
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.repeat) return;
            const active = document.activeElement;
            if (["INPUT", "TEXTAREA", "SELECT"].includes(active.tagName)) return;

            buttons.forEach(btn => {
                const underlineSpan = btn.querySelector(".underline-letter");
                if (underlineSpan) {
                    const key = underlineSpan.textContent.trim().toLowerCase();
                    if (e.key.toLowerCase() === key) {
                        e.preventDefault();
                        btn.click();
                        return;
                    }
                }
            });

            buttons.forEach(btn => {
                const text = btn.textContent.trim().toLowerCase();
                if (text.startsWith(e.key.toLowerCase())) {
                    e.preventDefault();
                    btn.click();
                }
            });
        });
    </script>
</body>
</html>
