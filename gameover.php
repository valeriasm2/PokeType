<?php
session_start();

// 🚫 Si no hay sesión, volver al index
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

$_SESSION['name'] = $name;

// 🎲 Guardar en ranking si el usuario pulsa "sí"
if (isset($_POST['save'])) {
    $rankingFile = __DIR__ . '/ranking.txt';
    $line = $name . ":" . $score . ":" . $time . PHP_EOL;
    file_put_contents($rankingFile, $line, FILE_APPEND | LOCK_EX);

    header("Location: ranking.php?last=" . urlencode($name) . "&score=" . $score . "&time=" . $time);
    exit();
}
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
    <img src="images/fantasmaGengar.png" alt="Gengar Fantasma" class="gengar-float">
    <img src="images/gengar8.png" class="gengar-bottom" alt="Gengar estático abajo">

    <div id="user-box">
        👤 <strong><?= htmlspecialchars($_SESSION['name']) ?></strong><br>
        <a href="destroy_session.php"><?= $langArray['index']['logout'] ?? 'Cerrar sessió' ?></a>
    </div>

    <div class="gameover-container">
        <h1><?= $t['title'] ?? 'Game Over!' ?></h1>
        <h3><?= $t['results'] ?? 'Resultat de la partida:' ?></h3>
        <p>✅ <?= $t['hits'] ?? 'Encerts' ?>: <strong><?= $hits ?></strong></p>
        <p>🎯 <?= $t['difficultyBonus'] ?? 'Bonus per dificultat' ?>: <strong><?= $bonus ?></strong></p>
        <?php if ($bonusGiratina > 0): ?>
            <p>✨ <?= $t['bonusGiratina'] ?? 'Bonus Giratina' ?>: <strong>+<?= $bonusGiratina ?> <?= $t['scoreUnit'] ?? '' ?></strong></p>
        <?php endif; ?>
        <p>⚡ <?= $t['timeBonus'] ?? 'Bonus per temps' ?>: <strong><?= $timeBonus ?></strong></p>
        <p>⏱ <?= $t['totalTime'] ?? 'Temps total' ?>: <strong><?= $time ?>s</strong></p>
        <hr>
        <p>🏆 <strong><?= $t['finalScore'] ?? 'Puntuació final' ?>: <?= $score ?> <?= $t['scoreUnit'] ?? '' ?></strong></p>

        <div class="gameover-save-text" style="margin-top:1em;margin-bottom:1em;text-align:center">
            <?= $t['save'] ?? 'Guardar puntuació?' ?>
        </div>

        <?php
        $yesText = $t['yes'] ?? 'Sí';
        $noText  = $t['no'] ?? 'No';
        ?>

        <div class="gameover-buttons">
            <form method="post" action="gameover.php">
                <input type="hidden" name="name" value="<?= $name ?>">
                <input type="hidden" name="score" value="<?= $score ?>">
                <input type="hidden" name="time" value="<?= $time ?>">
                <input type="hidden" name="hits" value="<?= $hits ?>">
                <input type="hidden" name="bonus" value="<?= $bonus ?>">
                <input type="hidden" name="timeBonus" value="<?= $timeBonus ?>">
                <input type="hidden" name="bonusGiratina" value="<?= $bonusGiratina ?>">
                <input type="hidden" name="save" value="1">
                <button type="submit" id="save-btn">
                    <span class="underline-letter"><?= substr($yesText, 0, 1) ?></span><?= substr($yesText, 1) ?>
                </button>
            </form>
            <a href="index.php" id="no-btn" class="btn">
                <span class="underline-letter"><?= substr($noText, 0, 1) ?></span><?= substr($noText, 1) ?>
            </a>
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
