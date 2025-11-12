<?php

session_start();

require_once "admin/logger.php";

// Si no hay sesión, volver al index
if (!isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

// Variables básicas
$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Jugador';
$difficulty = isset($_SESSION['difficulty']) ? $_SESSION['difficulty'] : 'facil';

logJuego("GAME_START", "play.php", "Jugador '$name' inició partida en dificultad '$difficulty'");

// Permadeath: si viene por GET lo fijamos en sesión; también admitimos la bandera en sesión
$isPermadeath = false;
if (isset($_GET['permadeath']) && $_GET['permadeath']) {
    $_SESSION['permadeath'] = true;
}
if (isset($_SESSION['permadeath']) && $_SESSION['permadeath']) {
    $isPermadeath = true;
}

// Bonus especial Giratina desde Easter Egg
$bonusGiratina = isset($_GET['bonusGiratina']) ? (int)$_GET['bonusGiratina'] : 0;

// Bonus por dificultad
$bonus = ($difficulty === "facil") ? 2 :
        (($difficulty === "normal") ? 3 : 5);

// Cargar frases desde JSON
$file = "frases.txt";
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

// Cantidad de frases según dificultad
$frasesPorNivel = ($difficulty === "facil") ? 3 :
                 (($difficulty === "normal") ? 4 : 5);

// Seleccionar frases aleatorias
shuffle($frasesSeleccionadas);
$frasesSeleccionadas = array_slice($frasesSeleccionadas, 0, $frasesPorNivel);
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

<!-- Info usuario -->
<div id="user-box">
    👤 <strong><?php echo htmlspecialchars($name); ?></strong><br>
    <a href="destroy_session.php">Tancar sessió</a>
</div>
<a href="secret.php" id="easter-egg" title="Easter Egg">👀</a>

<!-- Temporizador -->
<div id="timer-box">⏱ <span id="timer">0.00</span>s</div>
<!-- Vidas (permadeath) - contenedor cajón -->
<div id="lives-container">
    <div id="lives-box">❤❤❤❤❤</div>
</div>

<!-- Sonidos -->
<audio id="correct-sound" src="media/bien.mp3" preload="auto"></audio>
<audio id="wrong-sound" src="media/mal.mp3" preload="auto"></audio>
<audio id="button-sound" src="media/boton.mp3" preload="auto"></audio>

<div id="container">
    <h1>Poketype</h1>
    <p>Dificultat seleccionada: <strong><?php echo ucfirst(htmlspecialchars($difficulty)); ?></strong></p>

    <div id="countdown">3</div>

    <!-- Barra de progreso -->
    <div id="progress-text"></div>
    <div id="progress-container">
        <div id="progress-bar"></div>
    </div>

    <!-- Zona de juego -->
    <div id="game-area" style="display:none;">
        <p id="frase"></p>
        <div id="image-box" class="pokemon-image-container"></div>
    </div>

    <a href="index.php" id="back-btn"><span class="underline-letter">ESC</span>APE</a>
</div>

<!-- Popup combo -->
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
/* ------------------- CONFIG ------------------- */
const frasesData = <?php echo json_encode($frasesSeleccionadas); ?>;
let bonusGiratina = <?php echo $bonusGiratina; ?>;
let bonusDificultad = <?php echo $bonus; ?>;

let fraseIndex = 0;
let charIndex = 0;
let estado = [];

let puntosTotales = 0;
let totalHits = 0;
let totalTimeBonus = 0;
let comboLevel = 1;

const correctSound = document.getElementById("correct-sound");
const wrongSound = document.getElementById("wrong-sound");
const buttonSound = document.getElementById("button-sound");

const fraseEl = document.getElementById("frase");
const imageBox = document.getElementById("image-box");
const progressText = document.getElementById("progress-text");
const progressBar = document.getElementById("progress-bar");
const progressContainer = document.getElementById("progress-container");

/* ------------------ PERMADEATH (vidas) ------------------ */
const permadeathEnabled = <?php echo $isPermadeath ? 'true' : 'false'; ?>;
let vidas = permadeathEnabled ? 5 : 0; // 5 fallos permitidos en permadeath
const livesBox = document.getElementById('lives-box');
function modificarVidas(){
    // Mostrar siempre el cajón para facilitar pruebas visuales.
    livesBox.style.display = 'block';
    // Si el modo permadeath NO está activo, mostramos un estado atenuado (placeholder)
    if(!permadeathEnabled){
        livesBox.classList.add('disabled');
        // mostrar corazones vacíos como placeholder
        let heartsOff = '';
        for(let i=0;i<5;i++) heartsOff += '♡';
        livesBox.innerText = heartsOff + '  (permadeath off)';
        return;
    }
    // Si está activo, mostrar corazones según vidas restantes
    livesBox.classList.remove('disabled');
    let hearts = '';
    for(let i=0;i<vidas;i++) hearts += '❤';
    for(let i=vidas;i<5;i++) hearts += '♡';
    livesBox.innerText = hearts + (vidas<=2 ? '  ⚠️ Queden ' + vidas + ' vides' : '');
}
function perderVidasEff(){
    // pequeño efecto visual
    livesBox.classList.add('life-lost');
    setTimeout(()=> livesBox.classList.remove('life-lost'), 400);
}
modificarVidas();

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
function stopTimerFrase() { return parseFloat(((Date.now() - startTimeFrase)/1000).toFixed(2)); }

/* Normalizar caracteres */
function normalizar(char) {
    return char.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
}

/* Mostrar frase e imagen actual */
function mostrarFrase() {
    let frase = frasesData[fraseIndex].texto;
    let html = "";
    for (let i=0;i<frase.length;i++) {
        if(i<charIndex){
            html += estado[i]? `<span class='correct'>${frase[i]}</span>` : `<span class='wrong'>${frase[i]}</span>`;
        } else if(i===charIndex){
            html += `<span class='highlight'>${frase[i]}</span>`;
        } else {
            html += frase[i];
        }
    }
    fraseEl.innerHTML = html;

    const imagen = frasesData[fraseIndex].imagen;
    if(imagen){
        imageBox.innerHTML = `<img src="images/${imagen}" class="pokemon-icon" alt="Pokemon">`;
    } else {
        imageBox.innerHTML = "";
    }
}

/* ------------------ COUNTDOWN ------------------ */
const countdownEl = document.getElementById("countdown");
const gameArea = document.getElementById("game-area");
function iniciarCuentaAtras() {
    charIndex = 0;
    estado = [];
    gameArea.style.display = "none";
    countdownEl.innerText = 3;
    countdownEl.style.display = "block";

    progressContainer.style.display = "block";
    progressText.innerText = `Frase ${fraseIndex+1} de ${frasesData.length}`;
    progressBar.style.width = ((fraseIndex)/frasesData.length)*100+"%";

    let intervalo = setInterval(()=>{
        countdownEl.innerText--;
        if(countdownEl.innerText=="0"){
            clearInterval(intervalo);
            countdownEl.style.display = "none";
            gameArea.style.display = "block";
            if(startTimeGlobal===null) startGlobalTimer();
            startTimerFrase();
            mostrarFrase();
            document.addEventListener("keydown", jugar);
            resetInactivityTimer();
        }
    },1000);
}

/* ------------------ COMBO SYSTEM ------------------ */
const comboPopup = document.getElementById("combo-popup");
const comboMultEl = document.getElementById("combo-mult");
const comboTimerBar = document.getElementById("combo-timer-bar");

let correctSinceLevel=0, singleWrongPending=false, consecutiveWrong=0;
let comboTimerInterval=null, comboTimerRemaining=3000;

function showComboPopup(){ comboMultEl.textContent='x'+comboLevel; comboPopup.style.display='flex'; clearTimeout(comboPopup._hideTimeout); comboPopup._hideTimeout = setTimeout(()=>{ if(!comboTimerInterval) comboPopup.style.display='none';},1400);}
function updateComboTimerBar(){ comboTimerBar.style.width = Math.max(0,comboTimerRemaining/3000*100)+'%';}
function clearCombo(){ comboLevel=1; correctSinceLevel=0; singleWrongPending=false; consecutiveWrong=0; comboTimerRemaining=3000; stopComboTimer(); showComboPopup();}
function startComboTimer(){ stopComboTimer(); comboTimerRemaining=3000; updateComboTimerBar(); comboTimerInterval=setInterval(()=>{ comboTimerRemaining-=100; if(comboTimerRemaining<=0){ clearInterval(comboTimerInterval); comboTimerInterval=null; clearCombo(); comboPopup.style.display='flex'; setTimeout(()=>{if(!comboTimerInterval) comboPopup.style.display='none';},1200);}else{updateComboTimerBar();}},100); comboPopup.style.display='flex';}
function stopComboTimer(){ if(comboTimerInterval){ clearInterval(comboTimerInterval); comboTimerInterval=null;} updateComboTimerBar();}
function resetInactivityTimer(){ startComboTimer();}

/* ------------------ JUEGO ------------------ */
function jugar(e){
    if(!frasesData[fraseIndex] || charIndex>=frasesData[fraseIndex].texto.length || e.key.length>1){ resetInactivityTimer(); return; }
    let frase = frasesData[fraseIndex].texto;
    const acertado = normalizar(e.key)===normalizar(frase[charIndex]);
    estado[charIndex]=acertado;

    if(acertado){
        correctSound.play();
        correctSinceLevel++;
        consecutiveWrong=0;
        singleWrongPending=false;
        if(correctSinceLevel>=5 && comboLevel<4){ comboLevel++; correctSinceLevel=0; showComboPopup(); }
    } else {
        wrongSound.play();
        consecutiveWrong++;
        if(consecutiveWrong===1){ singleWrongPending=true; } else { if(comboLevel>1){ comboLevel=Math.max(1,comboLevel-1); showComboPopup(); } correctSinceLevel=0; singleWrongPending=false; }

        // Permadeath: restar vidas en cada fallo
        if(permadeathEnabled){
            vidas = Math.max(0, vidas - 1);
            perderVidasEff();
            modificarVidas();
            if(vidas <= 0){
                // Fin inmediato de la partida por permadeath
                document.removeEventListener("keydown", jugar);
                fraseEl.innerHTML += "<br><br><strong>💀 Permadeath: no queden vides. Fi de la partida.</strong>";
                setTimeout(()=>{
                    enviarScore(puntosTotales, true);
                }, 2000);
                return;
            }
        }
    }

    resetInactivityTimer();
    charIndex++;
    mostrarFrase();

    if(charIndex===frase.length){
        document.removeEventListener("keydown", jugar);
        fraseEl.innerHTML+="<br><br><strong>✅ Frase completada!</strong>";
        let aciertos = estado.filter(x=>x).length;
        let tiempoFrase = stopTimerFrase();
        let tiempoScore = Math.max(0,Math.floor(30/tiempoFrase));
        totalHits+=aciertos;
        totalTimeBonus+=tiempoScore;
        puntosTotales+=aciertos+tiempoScore;

        fraseIndex++;
        progressBar.style.width=(fraseIndex/frasesData.length)*100+"%";

        if(fraseIndex===frasesData.length){
            setTimeout(()=>{
                enviarScore(puntosTotales);
            },1500);
        } else {
            setTimeout(()=>iniciarCuentaAtras(),1500);
        }
    }
}

/* Enviar score a gameover.php incluyendo bonus Giratina */
function enviarScore(finalScoreBase, muerto = false){
    const finalScore = Math.floor((finalScoreBase + bonusDificultad + bonusGiratina) * comboLevel);
    const form = document.createElement("form");
    form.method="POST";
    form.action="gameover.php";
    form.innerHTML=`
        <input type="hidden" name="score" value="${finalScore}">
        <input type="hidden" name="time" value="${document.getElementById("timer").textContent}">
        <input type="hidden" name="hits" value="${totalHits}">
        <input type="hidden" name="timeBonus" value="${totalTimeBonus}">
        <input type="hidden" name="bonus" value="${bonusDificultad}">
        <input type="hidden" name="bonusGiratina" value="${bonusGiratina}">
        <input type="hidden" name="name" value="<?php echo $name; ?>">
        <input type="hidden" name="comboLevel" value="${comboLevel}">
        ${permadeathEnabled ? '<input type="hidden" name="permadeath" value="1">' : ''}
        ${muerto ? '<input type="hidden" name="muerto" value="1">' : ''}
    `;
    document.body.appendChild(form);
    form.submit();
}

/* ESC → Volver con sonido */
document.addEventListener("keydown",(e)=>{
    if(e.key==="Escape"){ buttonSound.play(); setTimeout(()=>document.getElementById("back-btn").click(),200);}
});

iniciarCuentaAtras();
</script>

</body>
</html>
