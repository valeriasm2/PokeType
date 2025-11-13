<?php
session_start(); // permite gestionar la sessió del usuarioo

// Incluir sistema de logs y lenguaje
require_once 'admin/logger.php';
require_once __DIR__ . '/utils/lang.php';

function mostrarError($error) {
    if (!empty($error)) {
        echo '<div class="error-alert">' . $error . '</div>';
        echo "<script>document.getElementById('name').focus();</script>";
    }
}

$error = "";
$name = "";
$difficulty = "";

// Cambio de idioma desde selector independiente
if (isset($_POST['setlang']) && isset($_POST['lang'])) {
    $newLang = $_POST['lang'];
    if (in_array($newLang, ['es','ca','en'], true)) {
        $_SESSION['lang'] = $newLang;
    }
    header('Location: index.php');
    exit();
}

// ✅ Si el formulari s'envia
if ($_POST) {
    $name = trim($_POST['name']);
    $difficulty = $_POST['difficulty'] ?? '';
    $permadeath = isset($_POST['permadeath']) && $_POST['permadeath'] === '1';

    if (empty($name)) {
        $error = t('index.error_empty');
    } else {
        $_SESSION['name'] = $name;              
        $_SESSION['difficulty'] = $difficulty;  
        
        // aqui se gestiona si se esta jugando en permadeath o no
        if ($permadeath) {
            $_SESSION['permadeath'] = true;
            // Log inicio de juego en modo permadeath
            logJuego("GAME_START_PERMADEATH", "index.php", "Usuario '$name' inició partida en modo permadeath (5 vides)");
            header("Location: play.php?permadeath=1");
        } else {
            unset($_SESSION['permadeath']);
            // Log inicio de juego normal
            logJuego("GAME_START", "index.php", "Usuario '$name' escogió juego en dificultad '$difficulty'");
            header("Location: play.php");
        }
        exit();
    }
}

if (isset($_SESSION['name'])) {
    $name = $_SESSION['name'];
}

if (isset($_SESSION['difficulty'])) {
    $difficulty = $_SESSION['difficulty'];
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(pt_current_lang()); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poketype</title>
    <link rel="stylesheet" href="styles.css?<?php echo time(); ?>">
</head>
<body>

    <!-- ✅ Recuadro superior derecho de sesión -->
    <?php if (isset($_SESSION['name'])): ?>
        <div id="user-box">
            👤 <strong><?= htmlspecialchars($_SESSION['name']); ?></strong><br>
            <a href="destroy_session.php"><?= htmlspecialchars(t('index.logout')); ?></a>
        </div>
    <?php endif; ?>
    <!-- ✅ Fin recuadro -->

    <!-- Selector de idioma -->
    <form action="index.php" method="post" style="position:absolute; top:10px; left:10px;">
        <input type="hidden" name="setlang" value="1">
        <label for="lang" style="margin-right:6px;"><?= htmlspecialchars(t('index.language')); ?></label>
        <select name="lang" id="lang" onchange="this.form.submit()">
            <?php $cur = pt_current_lang(); $names = pt_load_messages(pt_current_lang())['lang_names'] ?? ['es'=>'Español','ca'=>'Català','en'=>'English']; ?>
            <option value="es" <?= $cur==='es'?'selected':''; ?>><?= htmlspecialchars($names['es'] ?? 'Español'); ?></option>
            <option value="ca" <?= $cur==='ca'?'selected':''; ?>><?= htmlspecialchars($names['ca'] ?? 'Català'); ?></option>
            <option value="en" <?= $cur==='en'?'selected':''; ?>><?= htmlspecialchars($names['en'] ?? 'English'); ?></option>
        </select>
    </form>

    <!-- So botons -->
    <audio id="button-sound" src="boton.mp3" preload="auto"></audio>

    <div id="index-container">

        <h1>Poketype</h1>
        <p><?= htmlspecialchars(t('index.description')); ?></p>
        <img src="/media/gengarIndex.png" alt="Gengar" width="300">

        <form action="index.php" method="post">
            <label for="name"><?= htmlspecialchars(t('index.name_label')); ?></label>
            <input type="text" id="name" name="name"
                   value="<?php echo htmlspecialchars($name); ?>"><br>

            <?php mostrarError($error); ?>
            <br>

            <label for="dificultat"><?= htmlspecialchars(t('index.difficulty')); ?></label>
            <select name="difficulty" id="dificultat">
                <option value="facil"  <?= ($difficulty === "facil") ? "selected" : "" ?>><?= htmlspecialchars(t('index.difficulty_facil')); ?></option>
                <option value="normal" <?= ($difficulty === "normal") ? "selected" : "" ?>><?= htmlspecialchars(t('index.difficulty_normal')); ?></option>
                <option value="dificil" <?= ($difficulty === "dificil") ? "selected" : "" ?>><?= htmlspecialchars(t('index.difficulty_dificil')); ?></option>
            </select>

            <!-- Mode Permadeath -->
            <label for="permadeath" style="margin-left:10px;">
                <input type="checkbox" id="permadeath" name="permadeath" value="1" /> <?= htmlspecialchars(t('index.permadeath_label')); ?>
            </label>
            <button type="button" id="perma-info" class="info-btn" title="Què és el mode permadeath?">?</button>
            <br><br>

            <!-- Botó Jugar -->
            <button type="submit" id="play-button" disabled>
                <?= pt_label_with_hotkey('index.play', 'play'); ?>
            </button>


            <noscript>
                <div class="error-alert">
                    <?= htmlspecialchars(t('index.noscript')); ?>
                </div>
            </noscript>
        </form>
    </div>

    <!-- Scripts -->
    <script src="utils/music.js"></script>
    <script>
        const PT_PERMA_INFO = <?= t_js('index.permadeath_info'); ?>;
        const PT_PERMA_CONFIRM = <?= t_js('index.permadeath_confirm'); ?>;
        // Activar el botó Jugar quan es carregui la pàgina
        const playButton = document.getElementById('play-button');
        playButton.disabled = false;

        const buttons = document.querySelectorAll('button');
        const buttonSound = document.getElementById('button-sound');

        // Reproducir so en fer clic i gestionar confirm per permadeath
        buttons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                // Si és el botó d'informació sobre permadeath, mostrar explicació
                if (btn.id === 'perma-info') {
                    e.preventDefault();
                    alert(PT_PERMA_INFO);
                    return;
                }

                const permaCheckbox = document.getElementById('permadeath');
                const permaChecked = permaCheckbox && permaCheckbox.checked;

                if (btn.type === 'submit') {
                    // Si està marcat permadeath, demanar confirmació abans de continuar
                    if (permaChecked) {
                        const ok = confirm(PT_PERMA_CONFIRM);
                        if (!ok) {
                            e.preventDefault();
                            return;
                        }
                    }

                    // Reproduir so i enviar formulari després d'un petit retard
                    buttonSound.currentTime = 0;
                    buttonSound.play();
                    e.preventDefault();
                    setTimeout(() => {
                        btn.closest('form').submit();
                    }, 1000);
                    return;
                }

                // Per a botons no-submit, només reproduir so
                buttonSound.currentTime = 0;
                buttonSound.play();
            });
        });

        // Tecles: prem una lletra i simula el clic del botó corresponent
        document.addEventListener('keydown', (e) => {
            if (e.repeat) return;

            const active = document.activeElement;
            if (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA' || active.tagName === 'SELECT') return;

            buttons.forEach(btn => {
                const text = btn.textContent.trim().toLowerCase();
                if (text.startsWith(e.key.toLowerCase())) {
                    btn.click();
                }
            });
        });
    </script>

</body>
</html>
