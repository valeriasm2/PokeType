<?php
session_start();

// Redirigir si no hay sesión
if (!isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

// Idioma del jugador
$lang = $_SESSION['lang'] ?? 'ca';
$langFile = __DIR__ . "/lang/{$lang}.php";
$langArray = file_exists($langFile) ? require $langFile : [];

// Textos de play y de index (para dificultad)
$tPlay = $langArray['play'] ?? [];
$tIndex = $langArray['index'] ?? [];

$name = $_SESSION['name'];
$difficulty = $_SESSION['difficulty'] ?? 'facil';

// Obtener el texto traducido de la dificultad
$difficultyLabel = $tIndex['difficulty_'.$difficulty] ?? ucfirst($difficulty);


$file = "frases.txt";
$frasesSeleccionadas = [];

// Cargar frases desde JSON (texto + imagen)
if (file_exists($file)) {
    $json = file_get_contents($file);
    $frasesData = json_decode($json, true);

    if (isset($frasesData[$difficulty])) {
        foreach ($frasesData[$difficulty] as $obj) {
            $frasesSeleccionadas[] = [
                'texto' => $obj['texto'],
                'imagen' => $obj['imagen'] ?? null
            ];
        }
    }
}

// Cantidad de frases según dificultad
$frasesPorNivel = ($difficulty === 'facil') ? 3 : (($difficulty === 'normal') ? 4 : 5);
shuffle($frasesSeleccionadas);
$frasesSeleccionadas = array_slice($frasesSeleccionadas, 0, $frasesPorNivel);

// Bonus por dificultad
$bonus = ($difficulty === 'facil') ? 2 : (($difficulty === 'normal') ? 3 : 5);
// Bonus Easter Egg
$bonusGiratina = isset($_GET['bonusGiratina']) ? intval($_GET['bonusGiratina']) : 0;
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

    <!-- Info usuario -->
    <div id="user-box">
        👤 <strong><?= htmlspecialchars($name) ?></strong><br>
        <a href="destroy_session.php"><?= $langArray['index']['logout'] ?? 'Cerrar sesión' ?></a>
    </div>

    <!-- Temporizador total -->
    <div id="timer-box">⏱ <span id="timer">0.00</span>s</div>

    <!-- Sonidos -->
    <audio id="correct-sound" src="media/bien.mp3" preload="auto"></audio>
    <audio id="wrong-sound" src="media/mal.mp3" preload="auto"></audio>
    <audio id="button-sound" src="media/boton.mp3" preload="auto"></audio>

    <div id="container">
        <h1>Poketype</h1>
        <p><?= $tPlay['difficulty'] ?? 'Difficulty' ?>: <strong><?= $difficultyLabel ?></strong></p>

        <div id="countdown">3</div>

        <div id="progress-text"></div>
        <div id="progress-container">
            <div id="progress-bar"></div>
        </div>

        <div id="game-area" style="display:none;">
            <p id="frase"></p>
            <div id="image-box" class="pokemon-image-container"></div>
        </div>

        <!-- Botón ESCAPE -->
        <a href="index.php" id="back-btn">
            <span class="underline-letter">ESC</span><?= substr($tPlay['escape'] ?? 'ESCAPE', 3) ?>
        </a>
    </div>

    <script src="utils/music.js"></script>
    <script>
        const frasesData = <?= json_encode($frasesSeleccionadas) ?>;
        let fraseIndex = 0,
            charIndex = 0,
            estado = [];
        let puntosTotales = 0,
            totalHits = 0,
            totalTimeBonus = 0;
        let bonus = <?= $bonus ?>,
            bonusGiratina = <?= $bonusGiratina ?>;

        const correctSound = document.getElementById("correct-sound");
        const wrongSound = document.getElementById("wrong-sound");
        const buttonSound = document.getElementById("button-sound");

        const fraseEl = document.getElementById("frase");
        const imageBox = document.getElementById("image-box");
        const progressText = document.getElementById("progress-text");
        const progressBar = document.getElementById("progress-bar");
        const progressContainer = document.getElementById("progress-container");

        let startTimeGlobal = null;

        function startGlobalTimer() {
            startTimeGlobal = Date.now();
            setInterval(() => {
                document.getElementById("timer").textContent = ((Date.now() - startTimeGlobal) / 1000).toFixed(2);
            }, 10);
        }

        let startTimeFrase;

        function startTimerFrase() {
            startTimeFrase = Date.now();
        }

        function stopTimerFrase() {
            return parseFloat(((Date.now() - startTimeFrase) / 1000).toFixed(2));
        }

        function normalizar(char) {
            return char.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        }

        function mostrarFrase() {
            const frase = frasesData[fraseIndex].texto;
            let html = "";
            for (let i = 0; i < frase.length; i++) {
                if (i < charIndex) html += estado[i] ? `<span class='correct'>${frase[i]}</span>` : `<span class='wrong'>${frase[i]}</span>`;
                else if (i === charIndex) html += `<span class='highlight'>${frase[i]}</span>`;
                else html += frase[i];
            }
            fraseEl.innerHTML = html;

            const imagen = frasesData[fraseIndex].imagen;
            imageBox.innerHTML = imagen ? `<img src="images/${imagen}" class="pokemon-icon" alt="Pokemon">` : "";
        }

        function iniciarCuentaAtras() {
            charIndex = 0;
            estado = [];
            gameArea.style.display = "none";
            countdownEl.innerText = 3;
            countdownEl.style.display = "block";

            progressContainer.style.display = "block";
            progressText.innerText = `Frase ${fraseIndex+1} de ${frasesData.length}`;
            progressBar.style.width = (fraseIndex / frasesData.length) * 100 + "%";

            let intervalo = setInterval(() => {
                countdownEl.innerText--;
                if (countdownEl.innerText == "0") {
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
            const frase = frasesData[fraseIndex].texto;
            if (charIndex >= frase.length || e.key.length > 1) return;
            const acertado = normalizar(e.key) === normalizar(frase[charIndex]);
            estado[charIndex] = acertado;
            acertado ? correctSound.play() : wrongSound.play();
            charIndex++;
            mostrarFrase();

            if (charIndex === frase.length) {
                document.removeEventListener("keydown", jugar);
                fraseEl.innerHTML += `<br><br><strong><?= $t['phraseCompleted'] ?? '✅ Frase completada!' ?></strong>`;
                const aciertos = estado.filter(x => x).length;
                const tiempoFrase = stopTimerFrase();
                const tiempoScore = Math.max(0, Math.floor(30 / tiempoFrase));

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
                    <input type="hidden" name="score" value="${puntosTotales+bonus+bonusGiratina}">
                    <input type="hidden" name="time" value="${document.getElementById("timer").textContent}">
                    <input type="hidden" name="hits" value="${totalHits}">
                    <input type="hidden" name="timeBonus" value="${totalTimeBonus}">
                    <input type="hidden" name="bonus" value="${bonus}">
                    <input type="hidden" name="bonusGiratina" value="${bonusGiratina}">
                    <input type="hidden" name="name" value="<?= $name ?>">
                `;
                        document.body.appendChild(form);
                        form.submit();
                    }, 1500);
                } else setTimeout(iniciarCuentaAtras, 1500);
            }
        }

        const countdownEl = document.getElementById("countdown");
        const gameArea = document.getElementById("game-area");
        iniciarCuentaAtras();

        /* ESC → volver al index */
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                buttonSound.play();
                setTimeout(() => document.getElementById("back-btn").click(), 200);
            }
        });
    </script>

</body>

</html>