<?php
session_start();

// 🚫 Si no hay sesión, volver al index
if (!isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

// 🔤 Idioma actual
$lang = $_SESSION['lang'] ?? 'es';

// Cargar traducciones
$langFile = __DIR__ . "/lang/$lang.php";
if (!file_exists($langFile)) $langFile = __DIR__ . "/lang/es.php";
$langArray = include $langFile;

// Alias rápidos
$tPlay = $langArray['play'];
$tIndex = $langArray['index'];

// Datos del jugador
$name = $_SESSION['name'];
$difficulty = $_SESSION['difficulty'] ?? "facil";

$file = __DIR__ . "/frases_{$lang}.txt";
$frasesSeleccionadas = [];

if (file_exists($file)) {
    $json = file_get_contents($file);
    $frasesData = json_decode($json, true);
    if (isset($frasesData[$difficulty])) {
        foreach ($frasesData[$difficulty] as $obj) {
            $frasesSeleccionadas[] = [
                "texto" => $obj["texto"],
                "imagen" => $obj["imagen"] ?? null
            ];
        }
    }
}

$frasesPorNivel = ($difficulty === "facil") ? 3 :
                 (($difficulty === "normal") ? 4 : 5);

shuffle($frasesSeleccionadas);
$frasesSeleccionadas = array_slice($frasesSeleccionadas, 0, $frasesPorNivel);

// ✅ Bonus
$bonus = ($difficulty === "facil") ? 2 :
        (($difficulty === "normal") ? 3 : 5);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Poketype - Play</title>
        <link rel="stylesheet" href="styles.css?<?= time(); ?>">
    </head>
    <body>
    <img src="images/fantasmaGengar.png" alt="Gengar Fantasma" class="gengar-float">
    <img src="/images/gengar8.png" class="gengar-bottom" alt="Gengar estático abajo">

        <div id="user-box">
            👤 <strong><?= htmlspecialchars($name) ?></strong><br>
            <a href="destroy_session.php"><?= $tIndex['logout'] ?></a>
        </div>

        <a href="secret.php" id="easter-egg" title="<?= $tPlay['easter_egg'] ?>">👀</a>

        <div id="timer-box"><?= $tPlay['timer'] ?> <span id="timer">0.00</span>s</div>

        <audio id="correct-sound" src="media/bien.mp3" preload="auto"></audio>
        <audio id="wrong-sound" src="media/mal.mp3" preload="auto"></audio>
        <audio id="button-sound" src="media/boton.mp3" preload="auto"></audio>

        <div id="container">
            <h1>Poketype</h1>
            <p><?= $tPlay['difficulty_label'] ?>:
            <strong><?= $tIndex["difficulty_{$difficulty}"] ?? ucfirst($difficulty) ?></strong></p>

            <div id="countdown">3</div>

            <div id="progress-text"></div>
            <div id="progress-container">
                <div id="progress-bar"></div>
            </div>

            <div id="game-area" style="display:none;">
                <p id="frase"></p>
                <div id="image-box" class="pokemon-image-container"></div>
            </div>

            <a href="index.php" id="back-btn"><span class="underline-letter">ESC</span>APE</a>
        </div>

        <script>
        const frasesData = <?= json_encode($frasesSeleccionadas) ?>;
        const tPlay = <?= json_encode($tPlay) ?>;

        let fraseIndex = 0, charIndex = 0, estado = [];
        let puntosTotales = 0, totalHits = 0, totalTimeBonus = 0;
        let bonus = <?= $bonus ?>;

        const correctSound = document.getElementById("correct-sound");
        const wrongSound = document.getElementById("wrong-sound");
        const buttonSound = document.getElementById("button-sound");
        const fraseEl = document.getElementById("frase");
        const imageBox = document.getElementById("image-box");
        const progressText = document.getElementById("progress-text");
        const progressBar = document.getElementById("progress-bar");
        const progressContainer = document.getElementById("progress-container");
        const countdownEl = document.getElementById("countdown");
        const gameArea = document.getElementById("game-area");

        let startTimeGlobal = null;
        let startTimeFrase;

        function startGlobalTimer() {
            startTimeGlobal = Date.now();
            setInterval(() => {
                document.getElementById("timer").textContent =
                    ((Date.now() - startTimeGlobal) / 1000).toFixed(2);
            }, 10);
        }

        function startTimerFrase() { startTimeFrase = Date.now(); }
        function stopTimerFrase() { return ((Date.now() - startTimeFrase) / 1000).toFixed(2); }

        function normalizar(char) {
            return char.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        }

        function mostrarFrase() {
            let frase = frasesData[fraseIndex].texto;
            let html = "";
            for (let i = 0; i < frase.length; i++) {
                if (i < charIndex)
                    html += estado[i] ? `<span class='correct'>${frase[i]}</span>` :
                                        `<span class='wrong'>${frase[i]}</span>`;
                else if (i === charIndex)
                    html += `<span class='highlight'>${frase[i]}</span>`;
                else html += frase[i];
            }
            fraseEl.innerHTML = html;
            const imagen = frasesData[fraseIndex].imagen;
            imageBox.innerHTML = imagen ? `<img src="images/${imagen}" class="pokemon-icon" alt="Pokemon">` : "";
        }

        function iniciarCuentaAtras() {
            charIndex = 0; estado = [];
            gameArea.style.display = "none";
            countdownEl.innerText = 3;
            countdownEl.style.display = "block";
            progressContainer.style.display = "block";
            progressText.innerText = tPlay.progress.replace("%d", fraseIndex + 1).replace("%d", frasesData.length);
            progressBar.style.width = (fraseIndex / frasesData.length) * 100 + "%";

            let intervalo = setInterval(() => {
                countdownEl.innerText--;
                if (countdownEl.innerText === "0") {
                    clearInterval(intervalo);
                    countdownEl.style.display = "none";
                    gameArea.style.display = "block";
                    if (startTimeGlobal === null) startGlobalTimer();
                    startTimerFrase();
                    mostrarFrase();
                    document.addEventListener("keydown", jugar);
                }
            }, 1000);
        }

        function jugar(e) {
            let frase = frasesData[fraseIndex].texto;
            if (charIndex >= frase.length || e.key.length > 1) return;
            const acertado = normalizar(e.key) === normalizar(frase[charIndex]);
            estado[charIndex] = acertado;
            acertado ? correctSound.play() : wrongSound.play();
            charIndex++; mostrarFrase();

            if (charIndex === frase.length) {
                document.removeEventListener("keydown", jugar);
                fraseEl.innerHTML += `<br><br><strong>${tPlay.phraseCompleted}</strong>`;
                let aciertos = estado.filter(x => x).length;
                let tiempoFrase = stopTimerFrase();
                let tiempoScore = Math.max(0, Math.floor(30 / tiempoFrase));
                totalHits += aciertos;
                totalTimeBonus += tiempoScore;
                puntosTotales += aciertos + tiempoScore;
                fraseIndex++;
                progressBar.style.width = (fraseIndex / frasesData.length) * 100 + "%";
                if (fraseIndex === frasesData.length) {
                    setTimeout(() => {
                        const form = document.createElement("form");
                        form.method = "POST";
                        form.action = "gameover.php";
                        form.innerHTML = `
                            <input type="hidden" name="score" value="${puntosTotales + bonus}">
                            <input type="hidden" name="time" value="${document.getElementById("timer").textContent}">
                            <input type="hidden" name="hits" value="${totalHits}">
                            <input type="hidden" name="timeBonus" value="${totalTimeBonus}">
                            <input type="hidden" name="bonus" value="${bonus}">
                            <input type="hidden" name="name" value="<?= $name ?>">
                        `;
                        document.body.appendChild(form);
                        form.submit();
                    }, 1500);
                } else setTimeout(iniciarCuentaAtras, 1500);
            }
        }

        iniciarCuentaAtras();

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                buttonSound.play();
                setTimeout(() => document.getElementById("back-btn").click(), 200);
            }
        });
        </script>
    </body>
</html>
