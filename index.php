<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poketype</title>
    <link rel="stylesheet" href="styles.css?<?php echo time(); ?>">
</head>
<body>
    <!-- So per als botons -->
    <audio id="button-sound" src="boton.mp3" preload="auto"></audio>

    <div id="index-container">
        <?php
        function mostrarError($error) {
            if (!empty($error)) {
                echo '<div class="error-alert">' . $error . '</div>';
                echo "<script>document.getElementById('name').focus();</script>";
            }
        }

        $error = "";
        $name = "";
        $dificultat = "";

        if ($_POST) {
            $name = trim($_POST['name']);
            if (empty($name)) {
                $error = "⚠️ El camp nom no pot estar buit";
            } else {
                $dificultat = $_POST['difficulty'];
                header("Location: play.php?name=" . urlencode($name) . "&difficulty=" . urlencode($dificultat));
                exit();
            }
        }
        ?>

        <h1>Poketype</h1>
        <p>Benvingut a Poketype! Un joc per aprendre els tipus de Pokémon i millorar la teva velocitat d’escriptura.</p>
        <img src="https://media.tenor.com/7nOwCz3oGYYAAAAi/gengar.gif" alt="Mew GIF" width="300">

        <form action="index.php" method="post">
            <label for="name">Nom:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>"><br>
            <?php mostrarError($error); ?>
            <br>

            <label for="dificultat">Dificultat:</label>
            <select name="difficulty" id="dificultat">
                <option value="facil">Fàcil</option>
                <option value="normal">Normal</option>
                <option value="dificil">Difícil</option>
            </select><br><br>

            <!-- Botó Jugar amb tecla especial -->
            <button type="submit" id="play-button" disabled>Jugar</button>

            <noscript>
                <div class="error-alert">
                    ⚠️ Aquest joc necessita JavaScript per funcionar. Si us plau, habilita JavaScript al teu navegador. ⚠️
                </div>
            </noscript>
        </form>
    </div>

    <!-- Scripts -->
    <script src="music.js"></script>
    <script>
        // Activar el botó Jugar
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
                    }, 800); // temps per escoltar el so
                }
            });
        });

        // 🔥 Nova funció: prement una lletra fa el mateix que el botó corresponent
        document.addEventListener('keydown', (e) => {
            if (e.repeat) return; // evita repetir si la tecla es manté premuda

            // Evitar activar si s'està escrivint en un camp de text
            const active = document.activeElement;
            if (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA' || active.tagName === 'SELECT') return;

            // --- Assignar tecles segons el text del botó ---
            buttons.forEach(btn => {
                const text = btn.textContent.trim().toLowerCase();
                const key = e.key.toLowerCase();

                // Si el text del botó conté la lletra premsada (ex: "Jugar" → tecla J)
                if (text.startsWith(key)) {
                    btn.click();
                }
            });
        });
    </script>
</body>
</html>