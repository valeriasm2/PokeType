<?php
session_start(); // permite gestionar la sessió del usuarioo

// Incluir sistema de logs
require_once 'admin/logger.php';

function mostrarError($error) {
    if (!empty($error)) {
        echo '<div class="error-alert">' . $error . '</div>';
        echo "<script>document.getElementById('name').focus();</script>";
    }
}

$error = "";
$name = "";
$difficulty = "";

// ✅ Si el formulari s'envia
if ($_POST) {
    $name = trim($_POST['name']);
    $difficulty = $_POST['difficulty'] ?? '';
    $permadeath = isset($_POST['permadeath']) && $_POST['permadeath'] === '1';

    if (empty($name)) {
        $error = "⚠️ El camp nom no pot estar buit";
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
<html lang="ca">
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
            <a href="destroy_session.php">Tancar sessió</a>
        </div>
    <?php endif; ?>
    <!-- ✅ Fin recuadro -->

    <!-- So botons -->
    <audio id="button-sound" src="boton.mp3" preload="auto"></audio>

    <div id="index-container">

        <h1>Poketype</h1>
        <p>Benvingut a Poketype! Un joc per aprendre els tipus de Pokémon i millorar la teva velocitat d’escriptura.</p>
        <img src="/media/gengarIndex.png" alt="Gengar" width="300">

        <form action="index.php" method="post">
            <label for="name">Nom:</label>
            <input type="text" id="name" name="name"
                   value="<?php echo htmlspecialchars($name); ?>"><br>

            <?php mostrarError($error); ?>
            <br>

            <label for="dificultat">Dificultat:</label>
            <select name="difficulty" id="dificultat">
                <option value="facil"  <?= ($difficulty === "facil") ? "selected" : "" ?>>Fàcil</option>
                <option value="normal" <?= ($difficulty === "normal") ? "selected" : "" ?>>Normal</option>
                <option value="dificil" <?= ($difficulty === "dificil") ? "selected" : "" ?>>Difícil</option>
            </select>

            <!-- Mode Permadeath -->
            <label for="permadeath" style="margin-left:10px;">
                <input type="checkbox" id="permadeath" name="permadeath" value="1" /> Mode permadeath
            </label>
            <button type="button" id="perma-info" class="info-btn" title="Què és el mode permadeath?">?</button>
            <br><br>

            <!-- Botó Jugar -->
            <button type="submit" id="play-button" disabled>
                <span class="underline-letter">J</span>ugar
            </button>


            <noscript>
                <div class="error-alert">
                    ⚠️ Aquest joc necessita JavaScript per funcionar. Si us plau, habilita JavaScript al teu navegador. ⚠️
                </div>
            </noscript>
        </form>
    </div>

    <!-- Scripts -->
    <script src="utils/music.js"></script>
    <script>
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
                    alert("Mode Permadeath:\nSi l'activas només tens 5 vides i la partida s'acaba quan te les gastes. Pots rebre un bonus per jugar en aquest mode.");
                    return;
                }

                const permaCheckbox = document.getElementById('permadeath');
                const permaChecked = permaCheckbox && permaCheckbox.checked;

                if (btn.type === 'submit') {
                    // Si està marcat permadeath, demanar confirmació abans de continuar
                    if (permaChecked) {
                        const ok = confirm("Has seleccionat Mode Permadeath: només 5 vides. Vols continuar?");
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
