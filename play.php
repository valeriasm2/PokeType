<?php
$name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : "Jugador";
$difficulty = isset($_GET['difficulty']) ? htmlspecialchars($_GET['difficulty']) : "facil";

// --- NUEVO: Captura del bonus del easter egg ---
$bonus = isset($_GET['bonus']) ? intval($_GET['bonus']) : 0;
// ---------------------------------------------------

$file = "frases.txt";
$frase = "No hi ha frases disponibles per aquest nivell.";

if (file_exists($file)) {
    $json = file_get_contents($file);
    $frases = json_decode($json, true);

    if (isset($frases[$difficulty]) && count($frases[$difficulty]) > 0) {
        $frase = $frases[$difficulty][array_rand($frases[$difficulty])];
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Poketype - Joc</title>
<link rel="stylesheet" href="styles.css<?php echo time(); ?>">
</head>
<body>

<!-- Música de fondo y sonidos de acierto/error -->
<audio id="correct-sound" src="bien.mp3" preload="auto"></audio>
<audio id="wrong-sound" src="mal.mp3" preload="auto"></audio>
<script src="music.js"></script>

<div id="container">
    <h1>Poketype</h1>
    <p>Benvingut, <strong><?php echo $name; ?></strong>!</p>
    <p>Dificultat seleccionada: <strong><?php echo ucfirst($difficulty); ?></strong></p>

    <div id="countdown">3</div>
    <div id="game-area" style="display:none;">
        <p id="frase"></p>
    </div>

    <a href="index.php" id="back-btn">⬅️ Tornar</a>
</div>

<!-- Easter Egg: pasamos nombre y dificultad -->
<a href="secret.php?name=<?php echo urlencode($name); ?>&difficulty=<?php echo urlencode($difficulty); ?>" id="easter-egg" title="Easter Egg">👀</a>

<script src="music.js"></script>
<script>
// --- NUEVO: Pasamos el bonus a JS ---
let bonus = <?php echo $bonus; ?>; // puntos extra del easter egg
// ---------------------------------------------------

let countdown = 3;
const countdownEl = document.getElementById('countdown');
const gameArea = document.getElementById('game-area');
const frase = "<?php echo addslashes($frase); ?>";
const fraseEl = document.getElementById('frase');
let index = 0;
let estado = [];

const correctSound = document.getElementById('correct-sound');
const wrongSound = document.getElementById('wrong-sound');

function normalizar(char) {
    return char.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
}

const mostrarFrase = () => {
    let html = '';
    for (let i = 0; i < frase.length; i++) {
        if (i < index) {
            html += estado[i] 
                ? `<span class="correct">${frase[i]}</span>` 
                : `<span class="wrong">${frase[i]}</span>`;
        } else if (i === index) {
            html += `<span class="highlight">${frase[i]}</span>`;
        } else {
            html += frase[i];
        }
    }
    fraseEl.innerHTML = html;
};

// Cuenta atrás
const interval = setInterval(() => {
    countdown--;
    countdownEl.textContent = countdown;
    if (countdown === 0) {
        clearInterval(interval);
        countdownEl.style.display = 'none';
        gameArea.style.display = 'block';
        mostrarFrase();
        document.addEventListener('keydown', jugar);
    }
}, 1000);

// Función principal del juego
function jugar(e) {
    if (index >= frase.length) return;
    if (e.key.length > 1) return;

    const acertado = normalizar(e.key) === normalizar(frase[index]);
    estado[index] = acertado;
    
    if(acertado){
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
        document.removeEventListener('keydown', jugar);

        let aciertos = estado.filter(x => x).length;
        let puntos = aciertos + bonus;

        setTimeout(() => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'gameover.php';

            const inputScore = document.createElement('input');
            inputScore.type = 'hidden';
            inputScore.name = 'score';
            inputScore.value = puntos;
            form.appendChild(inputScore);

            const inputName = document.createElement('input');
            inputName.type = 'hidden';
            inputName.name = 'name';
            inputName.value = "<?php echo $name; ?>";
            form.appendChild(inputName);

            document.body.appendChild(form);
            form.submit();
        }, 2000); 
    }
}
</script>

</body>
</html>
