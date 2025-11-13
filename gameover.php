<?php
session_start();
require_once 'admin/logger.php';
require_once __DIR__ . '/utils/lang.php';
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
<html lang="<?php echo htmlspecialchars(pt_current_lang()); ?>">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars(t('gameover.title')); ?></title>
    <link rel="stylesheet" href="styles.css?<?php echo time(); ?>">
</head>
<body>

    <!-- Recuadro usuario -->
    <div id="user-box">
        👤 <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong><br>
        <a href="destroy_session.php"><?= htmlspecialchars(t('index.logout')); ?></a>
    </div>

    <audio id="button-sound" src="media/boton.mp3" preload="auto"></audio>

    <div class="gameover-container">
        <h1><?= htmlspecialchars(t('gameover.title')); ?></h1>

        <!-- Desglose -->
        <h3><?= htmlspecialchars(t('gameover.results')); ?></h3>
        <p>✅ <?= htmlspecialchars(t('gameover.hits')); ?>: <strong><?= $hits ?></strong></p>
        <p>🎯 <?= htmlspecialchars(t('gameover.difficultyBonus')); ?>: <strong><?= $bonus ?></strong></p>

        <?php if ($bonusGiratina > 0): ?>
            <p>✨ <?= htmlspecialchars(t('gameover.bonusGiratina')); ?>: <strong>+<?= $bonusGiratina ?></strong></p>
        <?php endif; ?>

        <p>⚡ <?= htmlspecialchars(t('gameover.timeBonus')); ?>: <strong><?= $timeBonus ?></strong></p>

        <p>🔥 <?= htmlspecialchars(t('gameover.comboMultiplier') ?? 'Combo'); ?>: <strong>x<?= $comboLevel ?></strong></p>
        
        <p>⏱ <?= htmlspecialchars(t('gameover.totalTime')); ?>: <strong><?= $time ?>s</strong></p>

        <hr>

        <p>🏆 <strong><?= htmlspecialchars(t('gameover.finalScore')); ?>: <?= $finalScore ?> <?= htmlspecialchars(t('gameover.scoreUnit')); ?></strong></p>

        <?php if ($isPermadeath): ?>
            <?php if ($muerto): ?>
                <p class="permadeath-notice">⚠️ <?= htmlspecialchars(t('gameover.permadeath_dead') ?? ''); ?></p>
            <?php else: ?>
                <p class="permadeath-notice">⚠️ <?= htmlspecialchars(t('gameover.permadeath_completed') ?? ''); ?></p>
                <p class="permadeath-notice"><?= htmlspecialchars(t('gameover.bonus')); ?>: +<?= $permadeathBonus ?></p>
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

            <button type="submit" class="btn-link" id="save-btn"><?= pt_label_with_hotkey('gameover.yes','save'); ?></button>
        </form>

        <button type="button" class="btn-link" id="no-btn" onclick="goToIndex()"><?= pt_label_with_hotkey('gameover.no','no'); ?></button>
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

    const PT_HOTKEY_SAVE = <?= t_js('hotkeys.save'); ?>.toLowerCase();
    const PT_HOTKEY_NO = <?= t_js('hotkeys.no'); ?>.toLowerCase();
    document.addEventListener("keydown", (e) => {
        const key = e.key.toLowerCase();
        if (key === PT_HOTKEY_SAVE) document.getElementById("save-btn").click();
        if (key === PT_HOTKEY_NO) goToIndex();
    });
</script>

</body>
</html>