<?php
session_start();

// Incluir sistema de logs
require_once 'admin/logger.php';

// Si no hay sesión, volver al index
if (!isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

$name = $_SESSION['name'];
$difficulty = isset($_SESSION['difficulty']) ? $_SESSION['difficulty'] : "facil";
$bonus = isset($_GET['bonus']) ? intval($_GET['bonus']) : 0;

$file = "frases.txt";
$frase = "No hi ha frases disponibles per aquest nivell.";
$imagenFrase = null; // Inicializar

if (file_exists($file)) {
    $json = file_get_contents($file);
    $frases = json_decode($json, true);

    if (isset($frases[$difficulty]) && count($frases[$difficulty]) > 0) {
        $fraseObj = $frases[$difficulty][array_rand($frases[$difficulty])];
        $frase = $fraseObj['texto']; // Extraer solo el texto, ahora el formato es un array de objetos
        $imagenFrase = $fraseObj['imagen'];
        
        // Log nueva frase cargada
        logJuego("LOAD_PHRASE", "play.php", "Usuario '$name' cargó frase en dificultad '$difficulty': '$frase'");
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Poketype - Joc</title>
        <link rel="stylesheet" href="styles.css?<?php echo time(); ?>">
    </head>
    <body>

        <!-- 🔹 Recuadro superior derecho (nombre + logout) -->
        <div id="user-box">
            👤 <strong><?php echo htmlspecialchars($name); ?></strong><br>
            <a href="destroy_session.php">Tancar sessió</a>
            
            
        </div>

        <!-- Easter Egg -->
        <a href="secret.php" id="easter-egg" title="Easter Egg">👀</a>

        <!-- Sonidos -->
        <audio id="correct-sound" src="media/bien.mp3" preload="auto"></audio>
        <audio id="wrong-sound" src="media/mal.mp3" preload="auto"></audio>
        <audio id="button-sound" src="media/boton.mp3" preload="auto"></audio>

        <div id="container">
            <h1>Poketype</h1>
            <p>Dificultat seleccionada: <strong><?php echo ucfirst(htmlspecialchars($difficulty)); ?></strong></p>

            <div id="countdown">3</div>
            <div id="game-area" style="display:none;">
                <p id="frase"></p>
            </div>

            <?php if ($imagenFrase): ?>
                <div class="pokemon-image-container">
                    <img src="images/<?php echo htmlspecialchars($imagenFrase); ?>" 
                         class="pokemon-icon" 
                         alt="Pokemon">
                </div>
            <?php endif; ?>

            <a href="index.php" id="back-btn">
                <span class="underline-letter">ESC</span>APE
            </a>

        </div>
        <script src="utils/music.js"></script>
        <script>
        const correctSound = document.getElementById("correct-sound");
        const wrongSound = document.getElementById("wrong-sound");
        const buttonSound = document.getElementById("button-sound");

        let bonus = <?php echo $bonus; ?>;
        const frase = "<?php echo addslashes($frase); ?>";
        const fraseEl = document.getElementById("frase");
        let index = 0;
        let estado = [];
        let countdown = 3;

        function normalizar(char) {
            return char.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        }

        // Mostrar frase con colores
        function mostrarFrase() {
            let html = "";
            for (let i = 0; i < frase.length; i++) {
                if (i < index) {
                    html += estado[i]
                        ? `<span class='correct'>${frase[i]}</span>`
                        : `<span class='wrong'>${frase[i]}</span>`;
                } else if (i === index) {
                    html += `<span class='highlight'>${frase[i]}</span>`;
                } else {
                    html += frase[i];
                }
            }
            fraseEl.innerHTML = html;
        }

        // Cuenta atrás
        const countdownEl = document.getElementById("countdown");
        const gameArea = document.getElementById("game-area");

        const interval = setInterval(() => {
            countdown--;
            countdownEl.textContent = countdown;
            if (countdown === 0) {
                clearInterval(interval);
                countdownEl.style.display = "none";
                gameArea.style.display = "block";
                mostrarFrase();
                document.addEventListener("keydown", jugar);
            }
        }, 1000);

        // Juego
        function jugar(e) {
            if (index >= frase.length) return;
            if (e.key.length > 1) return;

            const acertado = normalizar(e.key) === normalizar(frase[index]);
            estado[index] = acertado;

            if (acertado) {
                correctSound.currentTime = 0;
                correctSound.play();
            } else {
                wrongSound.currentTime = 0;
                wrongSound.play();
            }

            index++;
            mostrarFrase();

            if (index === frase.length) {
                fraseEl.innerHTML += "<br><br><strong>🎉 Has completat la frase! 🎉</strong>";
                document.removeEventListener("keydown", jugar);

                let aciertos = estado.filter(x => x).length;
                let puntos = aciertos + bonus;

                setTimeout(() => {
                    const form = document.createElement("form");
                    form.method = "POST";
                    form.action = "gameover.php";

                    form.innerHTML = `
                        <input type="hidden" name="score" value="${puntos}">
                        <input type="hidden" name="name" value="<?php echo $name; ?>">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }, 2000);
            }
        }

        // Volver con ESC + sonido
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                buttonSound.currentTime = 0;
                buttonSound.play();
                setTimeout(() => document.getElementById("back-btn").click(), 200);
            }
        });
        </script>

    </body>
</html>
