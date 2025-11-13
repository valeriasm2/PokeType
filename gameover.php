<?php
session_start();

// 🚫 Si no hay sesión, volver al index
require_once 'admin/logger.php';
if (!isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

// 🔤 Idioma actual (por defecto catalán)
$lang = $_SESSION['lang'] ?? 'ca';
$langFile = __DIR__ . "/lang/{$lang}.php";
$langArray = file_exists($langFile) ? require $langFile : [];
$t = $langArray['gameover'] ?? [];

// 🌟 Validación de que la info venga de play.php
if (
    !isset($_POST['score'], $_POST['name'], $_POST['time'], $_POST['hits'], $_POST['bonus'], $_POST['timeBonus'])
) {
    header("Location: index.php");
    exit();
}

// 📝 Valores del POST
$name          = htmlspecialchars($_POST['name']);
$score         = intval($_POST['score']);
$time          = floatval($_POST['time']);
$hits          = intval($_POST['hits']);
$bonus         = intval($_POST['bonus']);
$timeBonus     = intval($_POST['timeBonus']);
$bonusGiratina = isset($_POST['bonusGiratina']) ? intval($_POST['bonusGiratina']) : 0;
$comboLevel    = isset($_POST['comboLevel']) ? intval($_POST['comboLevel']) : 1;

// Permadeath (opcional)
$isPermadeath = (isset($_POST['permadeath']) && intval($_POST['permadeath']) === 1);
$muerto       = (isset($_POST['muerto']) && intval($_POST['muerto']) === 1);

$_SESSION['name'] = $name;

// Bonus permadeath
$permadeathBonus = ($isPermadeath && !$muerto) ? 300 : 0;
$finalScore = $score + $permadeathBonus;

logJuego("GAMEOVER_RECEIVED", "gameover.php", "Datos recibidos: jugador '$name', puntuación base $score, muerto=" . ($muerto? '1':'0') . ", bonus_permadeath $permadeathBonus, puntuación final $finalScore, tiempo $time s, combo x$comboLevel, permadeath=" . ($isPermadeath ? '1' : '0'));

// ✅ Guardar récord si se pulsa "Sí"
if (isset($_POST['save'])) {
    $rankingFile = __DIR__ . '/ranking.txt';
    $permaFlag = $isPermadeath ? 1 : 0;
    $line = $name . ":" . $finalScore . ":" . $time . ":" . $comboLevel . ":" . $permaFlag . PHP_EOL;
    file_put_contents($rankingFile, $line, FILE_APPEND | LOCK_EX);

    logJuego("RANKING_SAVED", "gameover.php", "Guardado ranking: $line");

    header("Location: ranking.php?last=" . urlencode($name) . "&score=" . $finalScore . "&time=" . $time . "&combo=" . $comboLevel . "&permadeath=" . $permaFlag);
    exit();
}

// Variables de botones Sí/No
$yesText = $t['yes'] ?? 'Sí';
$noText  = $t['no'] ?? 'No';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title><?= $t['title'] ?? 'Game Over' ?></title>
    <link rel="stylesheet" href="styles.css?<?= time(); ?>">
</head>
<body>
<!--<img src="images/fantasmaGengar.png" alt="Gengar Fantasma" class="gengar-float">
<img src="/images/gengar8.png" class="gengar-bottom" alt="Gengar estático abajo"> -->

    <div id="user-box">
        👤 <strong><?= htmlspecialchars($_SESSION['name']) ?></strong><br>
        <a href="destroy_session.php"><?= $langArray['index']['logout'] ?? 'Cerrar sesión' ?></a>
    </div>

    <div class="gameover-container">
        <h1><?= $t['title'] ?? 'Game Over!' ?></h1>
        <h3><?= $t['results'] ?? 'Resultado de la partida:' ?></h3>
        <p>✅ <?= $t['hits'] ?? 'Aciertos' ?>: <strong><?= $hits ?></strong></p>
        <p>🎯 <?= $t['difficultyBonus'] ?? 'Bonus por dificultad' ?>: <strong><?= $bonus ?></strong></p>
        <?php if ($bonusGiratina > 0): ?>
            <p>✨ <?= $t['bonusGiratina'] ?? 'Bonus Giratina' ?>: <strong>+<?= $bonusGiratina ?> <?= $t['scoreUnit'] ?? '' ?></strong></p>
        <?php endif; ?>
        <p>⚡ <?= $t['timeBonus'] ?? 'Bonus por tiempo' ?>: <strong><?= $timeBonus ?></strong></p>
        <p>🔥 <?= $t['comboMultiplier'] ?? 'Multiplicador de combo' ?>: <strong>x<?= $comboLevel ?></strong></p>
        <p>⏱ <?= $t['totalTime'] ?? 'Tiempo total' ?>: <strong><?= $time ?>s</strong></p>

        <hr>
        <p>🏆 <strong><?= $t['finalScore'] ?? 'Puntuación final' ?>: <?= $finalScore ?> <?= $t['scoreUnit'] ?? '' ?></strong></p>

        <?php if ($isPermadeath): ?>
            <?php if ($muerto): ?>
                <p class="permadeath-notice"><?= $t['permadeath_dead'] ?? '⚠️ Modo Permadeath activado: la partida terminó porque te quedaste sin vidas. No se aplica el bonus.' ?></p>
            <?php else: ?>
                <p class="permadeath-notice"><?= $t['permadeath_alive'] ?? '⚠️ Modo Permadeath activado: esta partida se completó en permadeath.' ?></p>
                <p class="permadeath-notice"><?= $t['permadeath_bonus'] ?? 'Bonus permadeath aplicado' ?>: +<?= $permadeathBonus ?> <?= $t['scoreUnit'] ?? '' ?></p>
            <?php endif; ?>
        <?php endif; ?>

        <?php
        $yesText = $t['yes'] ?? 'Sí';
        $noText  = $t['no'] ?? 'No';
        ?>

<div class="gameover-buttons">
    <!-- Botón Sí dentro del form -->
    <form id="save-form" method="post" action="gameover.php">
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
            <span class="underline-letter">S</span>í
        </button>
    </form>

    <!-- Botón No fuera del form pero dentro del mismo flex -->
    <button type="button" class="btn-link" id="no-btn">
        <span class="underline-letter">N</span>o
    </button>
</div>


    </div>

    <script>
        document.addEventListener("keydown", (e) => {
            if (e.repeat) return;
            const key = e.key.toLowerCase();

            const yesKey = <?= json_encode(mb_substr(($t['yes'] ?? 'Sí'), 0, 1)) ?>.toLowerCase();
            const noKey  = <?= json_encode(mb_substr(($t['no'] ?? 'No'), 0, 1)) ?>.toLowerCase();

            if (key === yesKey || key === "enter") {
                e.preventDefault();
                document.getElementById("save-btn").click();
            }
            if (key === noKey || key === "escape") {
                e.preventDefault();
                document.getElementById("no-btn").click();
            }
        });
    </script>

</body>
</html>
