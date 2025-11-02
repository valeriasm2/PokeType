<?php
if (!isset($_POST['score']) || !isset($_POST['name'])) {
    header("HTTP/1.1 403 Forbidden");
    include(__DIR__ . "/errors/error403.php");
    exit;
}
$score = $name = null;
$lastPlayer = $lastScore = null;

if (isset($_POST['save']) && isset($_POST['score']) && isset($_POST['name'])) {
    $score = intval($_POST['score']);
    $name = htmlspecialchars($_POST['name']);

    $rankingFile = __DIR__ . '/ranking.txt';
    $line = $name . ":" . $score . PHP_EOL;
    file_put_contents($rankingFile, $line, FILE_APPEND | LOCK_EX);

    header("Location: ranking.php?last=" . urlencode($name) . "&score=" . $score);
    exit;
}

if (isset($_POST['score']) && isset($_POST['name'])) {
    $score = intval($_POST['score']);
    $name = htmlspecialchars($_POST['name']);
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
        <audio id="button-sound" src="media/boton.mp3" preload="auto"></audio>

        <div class="gameover-container">
            <h1>Game Over!</h1>
            <?php if ($score !== null && $name !== null): ?>
                <p>Vols registrar el teu rècord de <?= $score ?> punts?</p>

                <form method="post" action="gameover.php" style="display:inline;">
                    <input type="hidden" name="name" value="<?= $name ?>">
                    <input type="hidden" name="score" value="<?= $score ?>">
                    <input type="hidden" name="save" value="1">
                    <button type="submit" class="btn-link">Sí</button>
                </form>

                <a href="index.php" class="btn-link"><button type="button" class="btn-link">No</button></a>

            <?php else: ?>
                <p>Error: no hi ha dades del jugador.</p>
                <a href="index.php" class="btn-link"><button type="button" class="btn-link">Tornar</button></a>
            <?php endif; ?>
        </div>
        <script src="utils/musicGameover.js"></script>

    </body>
</html>
