<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poketype</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Sonido para todos los botones -->
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
        $dificultad = "";

        if ($_POST) {
            $name = trim($_POST['name']);
            if (empty($name)) {
                $error = "⚠️ El camp nom no pot estar buit";
            } else {
                $dificultad = $_POST['difficulty'];
                header("Location: play.php?name=" . urlencode($name) . "&difficulty=" . urlencode($dificultad));
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

            <button type="submit" id="play-button" disabled>Jugar</button>

            <!-- Mensaje JS deshabilitado debajo del botón -->
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
        // Habilitar botón
        const playButton = document.getElementById('play-button');
        playButton.disabled = false;

        // Seleccionar todos los botones
        const buttons = document.querySelectorAll('button');
        const buttonSound = document.getElementById('button-sound');

        buttons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                // Reproducir sonido del botón
                buttonSound.currentTime = 0;
                buttonSound.play();

                // Si es el botón de submit, retrasar el envío para que se escuche
                if (btn.type === 'submit') {
                    e.preventDefault();
                    setTimeout(() => {
                        btn.closest('form').submit();
                    }, 800); // 800ms para que se escuche el sonido completo
                }
            });
        });
    </script>
</body>
</html>
