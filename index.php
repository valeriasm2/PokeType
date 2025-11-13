<?php
session_start(); // ✅ Permet recordar el nom i mostrar recuadro de sessió

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

    if (empty($name)) {
        $error = "⚠️ El camp nom no pot estar buit";
    } else {
        $_SESSION['name'] = $name;              // ✅ Guardem nom en sessió
        $_SESSION['difficulty'] = $difficulty;  // ✅ Guardem dificultat en sessió

        header("Location: play.php");
        exit();
    }
}

// ✅ Si hi ha sessió iniciada, recuperar dades per mostrar-les al formulari
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
        <img src="https://media.tenor.com/7nOwCz3oGYYAAAAi/gengar.gif" alt="Mew GIF" width="300">

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
            </select><br><br>

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

        // Reproducir so en fer clic
        buttons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                buttonSound.currentTime = 0;
                buttonSound.play();

                if (btn.type === 'submit') {
                    e.preventDefault();
                    setTimeout(() => {
                        btn.closest('form').submit();
                    }, 1000); // temps per escoltar el so
                }
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
