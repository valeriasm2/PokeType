<?php
session_start();

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
    <!-- Popup combo (mostrat quan canvia o quan hi ha el timer) -->
    <div id="combo-popup" role="status" aria-live="polite">
    <div>
        <span class="label">Combo</span>
        <span class="mult" id="combo-mult">x1</span>
        <div id="combo-timer" style="display:block;">
        <div id="combo-timer-bar"></div>
        </div>
    </div>
    </div>


<script src="utils/music.js"></script>
<script>
/* ------------------- CONFIG (mantinc parts teves) ------------------- */
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
            resetInactivityTimer(); // arrenco timer d'inactivitat quan comença a jugar
        }
    }, 1000);
}

/* ------------------ COMBO SYSTEM ------------------ */
/*
 He implementat:
 - comboLevel: 1..4 (x1..x4)
 - correctSinceLevel: compta encerts parcials cap al següent nivell (0..4)
 - singleWrongPending: boolean per detectar 1 error (no penalitza)
 - penalització: a partir de 2 errors seguits es redueix comboLevel per cada error seguit (mínim 1)
 - comboTimer: si 3s sense teclat -> perdre combo (esborra el progrés)
 - es mostra popup amb el multiplicador cada vegada que canvia
*/

const comboPopup = document.getElementById("combo-popup");
const comboMultEl = document.getElementById("combo-mult");
const comboTimerBar = document.getElementById("combo-timer-bar");

let comboLevel = 1;            // 1 = x1, 2 = x2, ...
let correctSinceLevel = 0;     // encerts cap al següent nivell (0..4)
let singleWrongPending = false;
let consecutiveWrong = 0;

// Inactivity/timeout
let inactivityTimeout = null;
const INACTIVITY_MS = 3000;
let comboTimerInterval = null;
let comboTimerRemaining = INACTIVITY_MS;

// Mostrar popup (breu) i actualitzar text
function showComboPopup() {
    comboMultEl.textContent = 'x' + comboLevel;
    comboPopup.style.display = 'flex';
    // Reiniciar animació: ho deixem visible 1.4s (o mentre hi hagi barra)
    clearTimeout(comboPopup._hideTimeout);
    comboPopup._hideTimeout = setTimeout(() => {
        // Només ocultem si el timer no està corrent
        if (!comboTimerInterval) comboPopup.style.display = 'none';
    }, 1400);
}

// Actualitzar barra inversa segons time remaining (0..INACTIVITY_MS)
function updateComboTimerBar() {
    const frac = Math.max(0, comboTimerRemaining / INACTIVITY_MS);
    comboTimerBar.style.width = (frac * 100) + '%';
}

// Perdre el combo complet
function clearCombo() {
    comboLevel = 1;
    correctSinceLevel = 0;
    singleWrongPending = false;
    consecutiveWrong = 0;
    comboTimerRemaining = INACTIVITY_MS;
    stopComboTimer();
    showComboPopup();
}

// Inici/atur timer d'inactivitat (3s)
function startComboTimer() {
    stopComboTimer();
    comboTimerRemaining = INACTIVITY_MS;
    updateComboTimerBar();
    comboTimerInterval = setInterval(() => {
        comboTimerRemaining -= 100;
        if (comboTimerRemaining <= 0) {
            clearInterval(comboTimerInterval);
            comboTimerInterval = null;
            // perdre combo per inactivitat
            clearCombo();
            comboPopup.style.display = 'flex'; // mostrar que s'ha perdut
            setTimeout(() => { if (!comboTimerInterval) comboPopup.style.display = 'none'; }, 1200);
        } else {
            updateComboTimerBar();
        }
    }, 100);
    // fer visible la popup mentre corre el timer
    comboPopup.style.display = 'flex';
}

function stopComboTimer() {
    if (comboTimerInterval) {
        clearInterval(comboTimerInterval);
        comboTimerInterval = null;
    }
    updateComboTimerBar();
}

// Reiniciar timer d'inactivitat (cridar a cada tecla)
function resetInactivityTimer() {
    startComboTimer();
}

/* ------------------ JUEGO ------------------ */
function jugar(e) {
    // evitar captures quan el countdown està inactiu o teclas no standard
    if (!frasesData[fraseIndex] || charIndex >= frasesData[fraseIndex].texto.length || e.key.length > 1) {
        // també reiniciem el timer d'inactivitat si és una tecla no-evaluable (p.ex. ESC)
        resetInactivityTimer();
        return;
    }

    let frase = frasesData[fraseIndex].texto;

    // Normalitzar i comparar
    const acertado = normalizar(e.key) === normalizar(frase[charIndex]);
    estado[charIndex] = acertado;

    if (acertado) {
        correctSound.play();

        // COMBO: encert
        correctSinceLevel++;
        consecutiveWrong = 0;
        singleWrongPending = false;

        // Si arribes a 5 encerts addicionals i no s'ha arribat al nivell màxim, puja combo
        if (correctSinceLevel >= 5 && comboLevel < 4) {
            comboLevel++;
            correctSinceLevel = 0;
            showComboPopup();
        }
    } else {
        wrongSound.play();

        // COMBO: error
        consecutiveWrong++;
        if (consecutiveWrong === 1) {
            // 1 error seguit: no passa res (marquem single wrong pending)
            singleWrongPending = true;
        } else {
            // >=2 errors seguits: cada error resta 1 al nivell (mínim 1)
            if (comboLevel > 1) {
                comboLevel = Math.max(1, comboLevel - 1);
                showComboPopup();
            }
            // després d'aplicar penalització, reiniciem el comptador cap a nou augment
            correctSinceLevel = 0;
            singleWrongPending = false;
        }
    }

    // Reinici timer d'inactivitat a cada tecla (reserva del combo)
    resetInactivityTimer();

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

                // Multiplicador final segons comboLevel
                const finalMultiplier = comboLevel; // x1..x4

                // Calculem puntuació final amb multiplicador (aplicat només a la puntuació)
                const baseScore = puntosTotales + bonus;
                const finalScore = Math.floor(baseScore * finalMultiplier);

                form.innerHTML = `
                    <input type="hidden" name="score" value="${finalScore}">
                    <input type="hidden" name="time" value="${document.getElementById("timer").textContent}">
                    <input type="hidden" name="hits" value="${totalHits}">
                    <input type="hidden" name="timeBonus" value="${totalTimeBonus}">
                    <input type="hidden" name="bonus" value="${bonus}">
                    <input type="hidden" name="name" value="<?php echo $name; ?>">
                    <input type="hidden" name="comboLevel" value="${comboLevel}">
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

// També volem que les tecles ràpides S/N/altres segueixin funcionant en gameover; per això restem l'event listener quan toca
</script>


</body>
</html>