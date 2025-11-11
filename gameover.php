<?php
session_start();

// Redirigir al index si no hay sesión
if (!isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

// Selección de idioma
$lang = $_SESSION['lang'] ?? 'ca';
$langFile = __DIR__ . "/lang/{$lang}.php";
$langArray = file_exists($langFile) ? require $langFile : [];
$t = $langArray['gameover'] ?? []; // Textos para gameover

// Validar que todos los datos vengan de play.php
if (
    !isset($_POST['score'], $_POST['name'], $_POST['time'], $_POST['hits'], $_POST['bonus'], $_POST['timeBonus'])
) {
    header("Location: index.php");
    exit();
}

// Valores recibidos
$name          = htmlspecialchars($_POST['name']);
$score         = intval($_POST['score']);
$time          = floatval($_POST['time']);
$hits          = intval($_POST['hits']);
$bonus         = intval($_POST['bonus']);
$timeBonus     = intval($_POST['timeBonus']);
$bonusGiratina = isset($_POST['bonusGiratina']) ? intval($_POST['bonusGiratina']) : 0;

$_SESSION['name'] = $name;

// Guardar récord
if (isset($_POST['save'])) {
    $rankingFile = __DIR__ . '/ranking.txt';
    $line = $name . ":" . $score . ":" . $time . PHP_EOL;
    file_put_contents($rankingFile, $line, FILE_APPEND | LOCK_EX);

    header("Location: ranking.php?last=" . urlencode($name) . "&score=" . $score . "&time=" . $time);
    exit();
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <title><?= $t['title'] ?? 'Game Over' ?></title>
    <link rel="stylesheet" href="styles.css?<?= time(); ?>">
</head>

<body>
    <img src="images/fantasmaGengar.png" alt="Gengar Fantasma" class="gengar-float">
    <img src="/images/gengar8.png" class="gengar-bottom" alt="Gengar estático abajo">

    <div id="user-box">
        👤 <strong><?= htmlspecialchars($_SESSION['name']) ?></strong><br>
        <a href="destroy_session.php"><?= $langArray['index']['logout'] ?? 'Cerrar sesión' ?></a>
    </div>

    <audio id="button-sound" src="media/boton.mp3" preload="auto"></audio>

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

        <form method="post" action="gameover.php" style="display:inline;">
            <input type="hidden" name="name" value="<?= $name ?>">
            <input type="hidden" name="score" value="<?= $score ?>">
            <input type="hidden" name="time" value="<?= $time ?>">
            <input type="hidden" name="hits" value="<?= $hits ?>">
            <input type="hidden" name="bonus" value="<?= $bonus ?>">
            <input type="hidden" name="timeBonus" value="<?= $timeBonus ?>">
            <input type="hidden" name="bonusGiratina" value="<?= $bonusGiratina ?>">
            <input type="hidden" name="save" value="1">

            <button type="submit" class="btn-link" id="save-btn">
                <span class="underline-letter"><?= substr($t['yes'] ?? 'S', 0, 1) ?></span><?= substr($t['yes'] ?? 'Si', 1) ?>
            </button>
        </form>

        <button type="button" class="btn-link" id="no-btn" onclick="goToIndex()">
            <span class="underline-letter"><?= substr($t['no'] ?? 'N', 0, 1) ?></span><?= substr($t['no'] ?? 'No', 1) ?>
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
            if (key === (<?= json_encode(substr($t['yes'] ?? 'S', 0, 1)) ?>).toLowerCase()) document.getElementById("save-btn").click();
            if (key === (<?= json_encode(substr($t['no'] ?? 'N', 0, 1)) ?>).toLowerCase()) goToIndex();
        });
    </script>
</body>

</html>