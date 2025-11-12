<?php
session_start();
require_once 'admin/logger.php';
// Si no hay sesión → al index
if (!isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

/*
 ✅ Validación:
 En lugar de enviar a error403, si falta algún dato vamos al index
 para evitar errores cuando venga desde play.php.
*/
if (
    !isset($_POST['score']) ||
    !isset($_POST['name'])  ||
    !isset($_POST['time'])  ||
    !isset($_POST['hits'])  ||
    !isset($_POST['bonus']) ||
    !isset($_POST['timeBonus'])
) {
    header("Location: index.php");
    exit();
}

// ✅ Recibir valores desde play.php
$name          = htmlspecialchars($_POST['name']);
$score         = intval($_POST['score']);
$time          = floatval($_POST['time']);
$hits          = intval($_POST['hits']);
$bonus         = intval($_POST['bonus']);
$timeBonus     = intval($_POST['timeBonus']);
$bonusGiratina = isset($_POST['bonusGiratina']) ? intval($_POST['bonusGiratina']) : 0;
$comboLevel    = isset($_POST['comboLevel']) ? intval($_POST['comboLevel']) : 1;

// Permadeath (opcional) — enviado por play.php cuando corresponde
$isPermadeath = (isset($_POST['permadeath']) && intval($_POST['permadeath']) === 1) ? true : false;

// ¿La partida terminó porque el jugador se quedó sin vidas? (enviado por play.php)
$muerto = (isset($_POST['muerto']) && intval($_POST['muerto']) === 1) ? true : false;

$_SESSION['name'] = $name;

// Aplicar bonus por permadeath si corresponde

// Aplicar bonus por permadeath solo si el modo estaba activo Y NO ha sido una muerte por quedarse sin vidas
$permadeathBonus = ($isPermadeath && !$muerto) ? 300 : 0;
$finalScore = $score + $permadeathBonus;

logJuego("GAMEOVER_RECEIVED", "gameover.php", "Datos recibidos: jugador '$name', puntuación base $score, muerto=" . ($muerto? '1':'0') . ", bonus_permadeath $permadeathBonus, puntuación final $finalScore, tiempo $time s, combo x$comboLevel, permadeath=" . ($isPermadeath ? '1' : '0'));


// ✅ Guardar récord si se pulsa "Sí"
if (isset($_POST['save'])) {

    $rankingFile = __DIR__ . '/ranking.txt';
    // Guardar en formato: nombre:puntuación:tiempo:combo:permadeath
    $permaFlag = (isset($_POST['permadeath']) && intval($_POST['permadeath']) === 1) ? 1 : 0;
    // Guardamos la puntuación final (incluido bonus de permadeath)
    $line = $name . ":" . $finalScore . ":" . $time . ":" . $comboLevel . ":" . $permaFlag . PHP_EOL;
    file_put_contents($rankingFile, $line, FILE_APPEND | LOCK_EX);

    logJuego("RANKING_SAVED", "gameover.php", "Guardado ranking: $line");

    // Añadimos perma al redirect para que ranking pueda mostrarlo si quiere
    header("Location: ranking.php?last=" . urlencode($name) . "&score=" . $finalScore . "&time=" . $time . "&combo=" . $comboLevel . "&permadeath=" . $permaFlag);
    exit();
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Game Over</title>
    <link rel="stylesheet" href="styles.css?<?php echo time(); ?>">
</head>
<body>

    <!-- Recuadro usuario -->
    <div id="user-box">
        👤 <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong><br>
        <a href="destroy_session.php">Tancar sessió</a>
    </div>

    <audio id="button-sound" src="media/boton.mp3" preload="auto"></audio>

    <div class="gameover-container">
        <h1>Game Over!</h1>

        <!-- Desglose -->
        <h3>Resultat de la partida:</h3>
        <p>✅ Encerts: <strong><?= $hits ?></strong></p>
        <p>🎯 Bonus per dificultat: <strong><?= $bonus ?></strong></p>

        <?php if ($bonusGiratina > 0): ?>
            <p>✨ Bonus Giratina: <strong>+<?= $bonusGiratina ?></strong> punts!</p>
        <?php endif; ?>

        <p>⚡ Bonus per temps: <strong><?= $timeBonus ?></strong></p>

        <p>🔥 Multiplicador de combo: <strong>x<?= $comboLevel ?></strong></p>
        
        <p>⏱ Temps total: <strong><?= $time ?>s</strong></p>

        <hr>

        <p>🏆 <strong>Puntuació final: <?= $finalScore ?> punts</strong></p>

        <?php if ($isPermadeath): ?>
            <?php if ($muerto): ?>
                <p class="permadeath-notice">⚠️ Mode <em>Permadeath</em> activat: la partida ha acabat perquè et vas quedar sense vides. No s'aplica el bonus.</p>
            <?php else: ?>
                <p class="permadeath-notice">⚠️ Mode <em>Permadeath</em> activat: aquesta partida s'ha completat en permadeath.</p>
                <p class="permadeath-notice">Bonus permadeath aplicat: +<?= $permadeathBonus ?> punts</p>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Form guardar récord -->
        <form method="post" action="gameover.php" style="display:inline;">
            <input type="hidden" name="name" value="<?= $name ?>">
            <input type="hidden" name="score" value="<?= $finalScore ?>">
            <input type="hidden" name="time" value="<?= $time ?>">
            <input type="hidden" name="hits" value="<?= $hits ?>">
            <input type="hidden" name="bonus" value="<?= $bonus ?>">
            <input type="hidden" name="timeBonus" value="<?= $timeBonus ?>">
            <input type="hidden" name="bonusGiratina" value="<?= $bonusGiratina ?>">
            <input type="hidden" name="comboLevel" value="<?= $comboLevel ?>">
            <input type="hidden" name="permadeath" value="<?= $isPermadeath ? 1 : 0 ?>">
            <input type="hidden" name="save" value="1">

            <button type="submit" class="btn-link" id="save-btn">
                <span class="underline-letter">S</span>i
            </button>
        </form>

        <button type="button" class="btn-link" id="no-btn" onclick="goToIndex()">
            <span class="underline-letter">N</span>o
        </button>
    </div>

<script src="utils/musicGameover.js"></script>
<script>
    const buttonSound = document.getElementById("button-sound");

    function playSound(callback) {
        buttonSound.currentTime = 0;
        buttonSound.play();
        setTimeout(callback, 800);
    }

    document.getElementById("save-btn").addEventListener("click", (e) => {
        e.preventDefault();
        playSound(() => e.target.closest("form").submit());
    });

    function goToIndex() {
        playSound(() => window.location.href = "index.php");
    }

    document.addEventListener("keydown", (e) => {
        const key = e.key.toLowerCase();
        if (key === "s") document.getElementById("save-btn").click();
        if (key === "n") goToIndex();
    });
</script>

</body>
</html>