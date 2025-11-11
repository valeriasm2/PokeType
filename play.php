<?php
session_start();
$bonusGiratina = isset($_GET['bonusGiratina']) ? intval($_GET['bonusGiratina']) : 0;
$_SESSION['bonusGiratina'] = $bonusGiratina;


// Si no hay sesión, volver al index
if (!isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

$name = $_SESSION['name'];
$difficulty = isset($_SESSION['difficulty']) ? $_SESSION['difficulty'] : "facil";

$file = "frases.txt";
$frasesSeleccionadas = [];

// Cargar frases desde JSON (array de objetos con texto + imagen)
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

// Cantidad de frases según dificultad
$frasesPorNivel = ($difficulty === "facil") ? 3 :
                 (($difficulty === "normal") ? 4 : 5);

// Seleccionar frases aleatorias (sin repetir)
shuffle($frasesSeleccionadas);
$frasesSeleccionadas = array_slice($frasesSeleccionadas, 0, $frasesPorNivel);

// Bonus por dificultad
$bonus = ($difficulty === "facil") ? 2 :
        (($difficulty === "normal") ? 3 : 5);
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

    <!-- ✅ Info usuario -->
    <div id="user-box">
        👤 <strong><?php echo htmlspecialchars($name); ?></strong><br>
        <a href="destroy_session.php">Tancar sessió</a>
    </div>
    <a href="secret.php" id="easter-egg" title="Easter Egg">👀</a>

    <!-- ⏱ Temporizador total -->
    <div id="timer-box">⏱ <span id="timer">0.00</span>s</div>

    <!-- Sonidos -->
    <audio id="correct-sound" src="media/bien.mp3" preload="auto"></audio>
    <audio id="wrong-sound" src="media/mal.mp3" preload="auto"></audio>
    <audio id="button-sound" src="media/boton.mp3" preload="auto"></audio>

    <div id="container">
        <h1>Poketype</h1>
        <p>Dificultat seleccionada: <strong><?php echo ucfirst(htmlspecialchars($difficulty)); ?></strong></p>

        <div id="countdown">3</div>

        <!-- ✅ Barra de progreso -->
        <div id="progress-text"></div>
        <div id="progress-container">
            <div id="progress-bar"></div>
        </div>

        <!-- ✅ Zona de juego -->
        <div id="game-area" style="display:none;">
            <p id="frase"></p>
            <!-- 🔹 Imagen del Pokémon (si existe) -->
            <div id="image-box" class="pokemon-image-container"></div>
        </div>

        <a href="index.php" id="back-btn"><span class="underline-letter">ESC</span>APE</a>
    </div>

<script src="utils/music.js"></script>
<script>
/* ------------------- CONFIG ------------------- */
const frasesData = <?php echo json_encode($frasesSeleccionadas); ?>;
let fraseIndex = 0;
let charIndex = 0;
let estado = [];

let puntosTotales = 0;
let totalHits = 0;
let totalTimeBonus = 0;
let bonus = <?php echo $bonus; ?>;

const correctSound = document.getElementById("correct-sound");
const wrongSound = document.getElementById("wrong-sound");
const buttonSound = document.getElementById("button-sound");

const fraseEl = document.getElementById("frase");
const imageBox = document.getElementById("image-box");
const progressText = document.getElementById("progress-text");
const progressBar = document.getElementById("progress-bar");
const progressContainer = document.getElementById("progress-container");

/* ------------------- TIMER GLOBAL ------------------- */
let startTimeGlobal = null;

function startGlobalTimer() {
    startTimeGlobal = Date.now();
    setInterval(() => {
        let elapsed = ((Date.now() - startTimeGlobal) / 1000).toFixed(2);
        document.getElementById("timer").textContent = elapsed;
    }, 10);
}

/* ------------------- TIMER POR FRASE ------------------- */
let startTimeFrase;
function startTimerFrase() { startTimeFrase = Date.now(); }
function stopTimerFrase() {
    return parseFloat(((Date.now() - startTimeFrase) / 1000).toFixed(2));
}

/* Normalizar caracteres */
function normalizar(char) {
    return char.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
}

/* Mostrar frase e imagen actual */
function mostrarFrase() {
    let frase = frasesData[fraseIndex].texto;
    let html = "";

    for (let i = 0; i < frase.length; i++) {
        if (i < charIndex) {
            html += estado[i]
                ? `<span class='correct'>${frase[i]}</span>`
                : `<span class='wrong'>${frase[i]}</span>`;
        } else if (i === charIndex) {
            html += `<span class='highlight'>${frase[i]}</span>`;
        } else {
            html += frase[i];
        }
    }
    fraseEl.innerHTML = html;

    // Mostrar imagen si existe
    const imagen = frasesData[fraseIndex].imagen;
    if (imagen) {
        imageBox.innerHTML = `<img src="images/${imagen}" class="pokemon-icon" alt="Pokemon">`;
    } else {
        imageBox.innerHTML = "";
    }
}

/* ------------------ COUNTDOWN ------------------ */
function iniciarCuentaAtras() {
    charIndex = 0;
    estado = [];

    gameArea.style.display = "none";
    countdownEl.innerText = 3;
    countdownEl.style.display = "block";

    progressContainer.style.display = "block";
    progressText.innerText = `Frase ${fraseIndex + 1} de ${frasesData.length}`;
    progressBar.style.width = ((fraseIndex) / frasesData.length) * 100 + "%";

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

/* ------------------ JUEGO ------------------ */
function jugar(e) {
    let frase = frasesData[fraseIndex].texto;
    if (charIndex >= frase.length || e.key.length > 1) return;

    const acertado = normalizar(e.key) === normalizar(frase[charIndex]);
    estado[charIndex] = acertado;

    acertado ? correctSound.play() : wrongSound.play();

    charIndex++;
    mostrarFrase();

    if (charIndex === frase.length) {
        document.removeEventListener("keydown", jugar);
        fraseEl.innerHTML += "<br><br><strong>✅ Frase completada!</strong>";

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
                    <input type="hidden" name="bonusGiratina" value="<?php echo $_SESSION['bonusGiratina'] ?? 0; ?>">
                    <input type="hidden" name="bonus" value="${bonus}">
                    <input type="hidden" name="name" value="<?php echo $name; ?>">
                `;
                document.body.appendChild(form);
                form.submit();
            }, 1500);
        } else {
            setTimeout(() => iniciarCuentaAtras(), 1500);
        }
    }
}

/* Iniciar primer countdown */
const countdownEl = document.getElementById("countdown");
const gameArea = document.getElementById("game-area");
iniciarCuentaAtras();

/* ESC → Volver con sonido */
document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
        buttonSound.play();
        setTimeout(() => document.getElementById("back-btn").click(), 200);
    }
});
</script>

</body>
</html>
