<?php
session_start();

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

$_SESSION['name'] = $name;

// ✅ Guardar récord si se pulsa "Sí"
if (isset($_POST['save'])) {

    $rankingFile = __DIR__ . '/ranking.txt';

    // Guardar en formato: nombre:puntuación:tiempo
    $line = $name . ":" . $score . ":" . $time . PHP_EOL;
    file_put_contents($rankingFile, $line, FILE_APPEND | LOCK_EX);

    header("Location: ranking.php?last=" . urlencode($name) . "&score=" . $score . "&time=" . $time);
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
        
        <p>⏱ Temps total: <strong><?= $time ?>s</strong></p>

        <hr>

        <p>🏆 <strong>Puntuació final: <?= $score ?> punts</strong></p>

        <!-- Form guardar récord -->
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
