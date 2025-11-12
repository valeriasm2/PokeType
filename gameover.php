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
    <title><?= $t['title'] ?? 'Game Over' ?></title>
    <link rel="stylesheet" href="styles.css?<?= time(); ?>">
</head>

<body>
    <img src="images/fantasmaGengar.png" alt="Gengar Fantasma" class="gengar-float">
    <img src="/images/gengar8.png" class="gengar-bottom" alt="Gengar estático abajo">

    <div id="user-box">
        👤 <strong><?= htmlspecialchars($_SESSION['name']) ?></strong><br>
        <a href="destroy_session.php"><?= $langArray['index']['logout'] ?? 'Cerrar sessió' ?></a>
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

        <div class="gameover-buttons">
            <form method="post" action="gameover.php" style="display:inline;">
                <input type="hidden" name="name" value="<?= $name ?>">
                <input type="hidden" name="score" value="<?= $score ?>">
                <input type="hidden" name="time" value="<?= $time ?>">
                <input type="hidden" name="hits" value="<?= $hits ?>">
                <input type="hidden" name="bonus" value="<?= $bonus ?>">
                <input type="hidden" name="timeBonus" value="<?= $timeBonus ?>">
                <input type="hidden" name="bonusGiratina" value="<?= $bonusGiratina ?>">
                <input type="hidden" name="save" value="1">
                <button type="submit" id="save-btn">
                    <?= $t['yes'] ?? 'Sí' ?>
                </button>
                <button type="button" id="no-btn">
                    <?= $t['no'] ?? 'No' ?>
                </button>
            </form>

        </div>
        <div class="gameover-save-text" style="margin-top:1em;text-align:center">
            <?= $t['save'] ?? 'Guardar puntuació?' ?>
        </div>
    </div>

    <script src="utils/musicGameover.js"></script>
    <script>
        const buttonSound = document.getElementById("button-sound");

        function playSound(callback) {
            if (buttonSound) {
                buttonSound.currentTime = 0;
                buttonSound.play().catch(() => {
                    // Si falla el audio, ejecutar callback inmediatamente
                    if (callback) callback();
                });
                if (callback) {
                    setTimeout(callback, 800);
                }
            } else if (callback) {
                callback();
            }
        }

        function goToIndex() {
            window.location.href = "index.php";
        }

        document.getElementById("save-btn").addEventListener("click", (e) => {
            e.preventDefault();
            playSound(() => {
                const form = e.target.closest("form");
                if (form) form.submit();
            });
        });

        document.getElementById("no-btn").addEventListener("click", (e) => {
            e.preventDefault();
            playSound(() => {
                goToIndex();
            });
        });

        document.addEventListener("keydown", (e) => {
            if (e.repeat) return;
            const active = document.activeElement;
            if (["INPUT", "TEXTAREA", "SELECT"].includes(active.tagName)) return;

            const key = e.key.toLowerCase();
            // Detect both first character and Enter/Escape as fallback
            if (key === (<?= json_encode(mb_substr(($t['yes'] ?? 'S'), 0, 1)) ?>).toLowerCase() || key === "enter") {
                e.preventDefault();
                document.getElementById("save-btn").click();
            }
            if (key === (<?= json_encode(mb_substr(($t['no'] ?? 'N'), 0, 1)) ?>).toLowerCase() || key === "escape") {
                e.preventDefault();
                document.getElementById("no-btn").click();
            }
        });
    </script>
</body>

</html>