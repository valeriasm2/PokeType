<?php
session_start();
require_once "admin/logger.php";

// Si no hay sesión, volver al index
if (!isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

// Idioma actual
$lang = $_SESSION['lang'] ?? 'es';
$langFile = __DIR__ . "/lang/$lang.php";
if (!file_exists($langFile)) $langFile = __DIR__ . "/lang/es.php";
$langArray = include $langFile;

// Alias rápidos (usa las claves que tengas en tus archivos de idioma)
$tPlay  = $langArray['play']  ?? [];
$tIndex = $langArray['index'] ?? [];

// Datos del jugador
$name       = $_SESSION['name'] ?? 'Jugador';
$difficulty = $_SESSION['difficulty'] ?? "facil";

// Log inicio
logJuego("GAME_START", "play.php", "Jugador '$name' inició partida en dificultad '$difficulty'");

// Permadeath
$isPermadeath = false;
if (isset($_GET['permadeath']) && $_GET['permadeath']) {
    $_SESSION['permadeath'] = true;
}
if (!empty($_SESSION['permadeath'])) {
    $isPermadeath = true;
}

// Bonus especial Giratina
$bonusGiratina = isset($_GET['bonusGiratina']) ? (int)$_GET['bonusGiratina'] : 0;

// Bonus por dificultad
$bonusDificultad = ($difficulty === "facil") ? 2 : (($difficulty === "normal") ? 3 : 5);

// Cargar frases desde JSON
$file = "frases.txt";
$frasesSeleccionadas = [];
if (file_exists($file)) {
    $json = file_get_contents($file);
    $frasesData = json_decode($json, true);
    if (isset($frasesData[$difficulty])) {
        foreach ($frasesData[$difficulty] as $obj) {
            $frasesSeleccionadas[] = [
                "texto"  => $obj["texto"],
                "imagen" => $obj["imagen"] ?? null
            ];
        }
    }
}

// Número de frases por nivel según dificultad
$frasesPorNivel = ($difficulty === "facil") ? 3 : (($difficulty === "normal") ? 4 : 5);
shuffle($frasesSeleccionadas);
$frasesSeleccionadas = array_slice($frasesSeleccionadas, 0, $frasesPorNivel);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poketype - Play</title>
    <link rel="stylesheet" href="styles.css?<?= time(); ?>">
</head>
<body>
    <img src="images/fantasmaGengar.png" alt="Gengar Fantasma" class="gengar-float">
    <img src="images/gengar8.png" class="gengar-bottom" alt="Gengar estático abajo">

    <div id="user-box">
        👤 <strong><?= htmlspecialchars($name) ?></strong><br>
        <a href="destroy_session.php"><?= $tIndex['logout'] ?? 'Cerrar sesión' ?></a>
    </div>

    <a href="secret.php" id="easter-egg" title="<?= $tPlay['easter_egg'] ?? 'Easter Egg' ?>">👀</a>

    <div id="timer-box">⏱ <?= $tPlay['timer'] ?? 'Tiempo' ?> <span id="timer">0.00</span>s</div>

    <!-- Vidas (permadeath) -->
    <div id="lives-container">
        <div id="lives-box"></div>
    </div>

    <!-- Sonidos -->
    <audio id="correct-sound" src="media/bien.mp3" preload="auto"></audio>
    <audio id="wrong-sound" src="media/mal.mp3" preload="auto"></audio>
    <audio id="button-sound" src="media/boton.mp3" preload="auto"></audio>

    <div id="container">
        <h1>Poketype</h1>
        <p><?= $tPlay['difficulty_label'] ?? 'Dificultad' ?>:
            <strong><?= $tIndex["difficulty_{$difficulty}"] ?? ucfirst($difficulty) ?></strong>
        </p>

        <div id="writePhrase3lang" style="margin-bottom:16px;">
            <span style="font-size:1.1em; color:#87004A;">
                <?= htmlspecialchars($tPlay['write_phrase'] ?? 'Escribe la frase') ?>
            </span>
        </div>

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

    <!-- Popup combo -->
    <div id="combo-popup" role="status" aria-live="polite" style="display:none;">
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
    // Datos desde PHP
    const frasesData       = <?= json_encode($frasesSeleccionadas) ?>;
    const tPlay            = <?= json_encode($tPlay) ?>;
    const bonusGiratina    = <?= $bonusGiratina ?>;
    const bonusDificultad  = <?= $bonusDificultad ?>;
    const permadeathEnabled= <?= $isPermadeath ? 'true' : 'false' ?>;
    const playerName       = <?= json_encode($name) ?>;

    // Estado del juego
    let fraseIndex = 0, charIndex = 0, estado = [];
    let puntosTotales = 0, totalHits = 0, totalTimeBonus = 0, comboLevel = 1;

    // Vidas en permadeath
    let vidas = permadeathEnabled ? 5 : 0;
    const livesBox = document.getElementById('lives-box');
    function modificarVidas(){
        livesBox.style.display = 'block';
        if(!permadeathEnabled){
            livesBox.innerText = '♡♡♡♡♡ (permadeath off)';
            livesBox.classList.add('disabled');
            return;
        }
        livesBox.classList.remove('disabled');
        let hearts = '';
        for(let i=0;i<vidas;i++) hearts += '❤';
        for(let i=vidas;i<5;i++) hearts += '♡';
        livesBox.innerText = hearts + (vidas<=2 ? '  ⚠️ ' + vidas + ' vidas' : '');
    }
    function perderVidasEff(){
        livesBox.classList.add('life-lost');
        setTimeout(()=> livesBox.classList.remove('life-lost'), 400);
    }
    modificarVidas();

    // Timer global
    let startTimeGlobal = null;
    function startGlobalTimer() {
        startTimeGlobal = Date.now();
        setInterval(() => {
            let elapsed = ((Date.now() - startTimeGlobal) / 1000).toFixed(2);
            document.getElementById("timer").textContent = elapsed;
        }, 10);
    }

    // Timer por frase
    let startTimeFrase;
    function startTimerFrase() { startTimeFrase = Date.now(); }
    function stopTimerFrase() { return parseFloat(((Date.now() - startTimeFrase)/1000).toFixed(2)); }

    // Normalizar caracteres
    function normalizar(char) {
        return char.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }

    const correctSound   = document.getElementById("correct-sound");
    const wrongSound     = document.getElementById("wrong-sound");
    const buttonSound    = document.getElementById("button-sound");
    const fraseEl        = document.getElementById("frase");
    const imageBox       = document.getElementById("image-box");
    const progressText   = document.getElementById("progress-text");
    const progressBar    = document.getElementById("progress-bar");
    const progressContainer = document.getElementById("progress-container");
    const countdownEl    = document.getElementById("countdown");
    const gameArea       = document.getElementById("game-area");

    // Mostrar frase
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
        imageBox.innerHTML = imagen ? `<img src="images/${imagen}" class="pokemon-icon" alt="Pokemon">` : "";
    }

    // Cuenta atrás
    function iniciarCuentaAtras() {
        charIndex = 0;
        estado = [];
        gameArea.style.display = "none";
        countdownEl.innerText = 3;
        countdownEl.style.display = "block";

        progressContainer.style.display = "block";
        const progressTpl = tPlay.progress || 'Frase %d de %d';
        progressText.innerText = progressTpl.replace('%d', (fraseIndex+1)).replace('%d', frasesData.length);
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

    // Combo system (simple y efectivo)
    const comboPopup    = document.getElementById("combo-popup");
    const comboMultEl   = document.getElementById("combo-mult");
    const comboTimerBar = document.getElementById("combo-timer-bar");

    let correctSinceLevel=0, singleWrongPending=false, consecutiveWrong=0;
    let comboTimerInterval=null, comboTimerRemaining=3000;

    function showComboPopup(){
        comboMultEl.textContent='x'+comboLevel;
        comboPopup.style.display='flex';
        clearTimeout(comboPopup._hideTimeout);
        comboPopup._hideTimeout = setTimeout(()=>{ if(!comboTimerInterval) comboPopup.style.display='none';},1400);
    }
    function updateComboTimerBar(){ comboTimerBar.style.width = Math.max(0,comboTimerRemaining/3000*100)+'%';}
    function clearCombo(){ comboLevel=1; correctSinceLevel=0; singleWrongPending=false; consecutiveWrong=0; comboTimerRemaining=3000; stopComboTimer(); showComboPopup();}
    function startComboTimer(){ stopComboTimer(); comboTimerRemaining=3000; updateComboTimerBar(); comboTimerInterval=setInterval(()=>{ comboTimerRemaining-=100; if(comboTimerRemaining<=0){ clearInterval(comboTimerInterval); comboTimerInterval=null; clearCombo(); comboPopup.style.display='flex'; setTimeout(()=>{if(!comboTimerInterval) comboPopup.style.display='none';},1200);}else{updateComboTimerBar();}},100); comboPopup.style.display='flex';}
    function stopComboTimer(){ if(comboTimerInterval){ clearInterval(comboTimerInterval); comboTimerInterval=null;} updateComboTimerBar();}
    function resetInactivityTimer(){ startComboTimer();}

    // Juego
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
            if(consecutiveWrong===1){ 
                singleWrongPending=true; 
            } else { 
                if(comboLevel>1){ comboLevel=Math.max(1,comboLevel-1); showComboPopup(); } 
                correctSinceLevel=0; 
                singleWrongPending=false; 
            }
            // Permadeath: restar vidas
            if(permadeathEnabled){
                vidas = Math.max(0, vidas - 1);
                perderVidasEff();
                modificarVidas();
                if(vidas <= 0){
                    // Fin inmediato por quedarse sin vidas
                    document.removeEventListener("keydown", jugar);
                    fraseEl.innerHTML += "<br><br><strong>💀 " + (tPlay.permadeath_out ?? "Permadeath: no quedan vidas. Fin de la partida.") + "</strong>";
                    setTimeout(()=>{ enviarScore(puntosTotales, true); }, 1500);
                    return;
                }
            }
        }

        resetInactivityTimer();
        charIndex++;
        mostrarFrase();

        // Frase completada
        if(charIndex===frase.length){
            document.removeEventListener("keydown", jugar);
            fraseEl.innerHTML+="<br><br><strong>" + (tPlay.phraseCompleted ?? "✅ ¡Frase completada!") + "</strong>";
            let aciertos = estado.filter(x=>x).length;
            let tiempoFrase = stopTimerFrase();
            let tiempoScore = Math.max(0,Math.floor(30/tiempoFrase));
            totalHits+=aciertos;
            totalTimeBonus+=tiempoScore;
            puntosTotales+=aciertos+tiempoScore;

            fraseIndex++;
            progressBar.style.width=(fraseIndex/frasesData.length)*100+"%";

            if(fraseIndex===frasesData.length){
                setTimeout(()=>{ enviarScore(puntosTotales, false); },1200);
            } else {
                setTimeout(()=>iniciarCuentaAtras(),1200);
            }
        }
    }

    // Enviar score a gameover.php incluyendo bonus, combo y flags
    function enviarScore(finalScoreBase, muerto = false){
        // Nota: aquí el "score" que se envía es base; en gameover.php tú aplicas permadeath bonus si corresponde.
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
            <input type="hidden" name="name" value="${playerName}">
            <input type="hidden" name="comboLevel" value="${comboLevel}">
            ${permadeathEnabled ? '<input type="hidden" name="permadeath" value="1">' : ''}
            ${muerto ? '<input type="hidden" name="muerto" value="1">' : ''}
        `;
        document.body.appendChild(form);
        form.submit();
    }

    // ESC → Volver con sonido
    document.addEventListener("keydown",(e)=>{
        if(e.key==="Escape"){ buttonSound.play(); setTimeout(()=>document.getElementById("back-btn").click(),200);}
    });

    // Arranque
    iniciarCuentaAtras();
    </script>
</body>
</html>
