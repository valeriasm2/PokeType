<?php
session_start();

// Si no hay sesión, vuelve al index
if (!isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_POST['score']) || !isset($_POST['name'])) {
    header("HTTP/1.1 403 Forbidden");
    include(__DIR__ . "/errors/error403.php");
    exit;
}

$score = $name = null;

if (isset($_POST['save']) && isset($_POST['score']) && isset($_POST['name'])) {
    $score = intval($_POST['score']);
    $name = htmlspecialchars($_POST['name']);

    $rankingFile = __DIR__ . '/ranking.txt';
    $line = $name . ":" . $score . PHP_EOL;
    file_put_contents($rankingFile, $line, FILE_APPEND | LOCK_EX);

    header("Location: ranking.php?last=" . urlencode($name) . "&score=" . $score);
    exit;
}

// Si solo se recibe nombre + score (pantalla inicial Game Over)
$score = intval($_POST['score']);
$name = htmlspecialchars($_POST['name']);
$_SESSION['name'] = $name; // asegura que la sesión guarde el nombre
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Game Over</title>
    <link rel="stylesheet" href="styles.css?<?php echo time(); ?>">
</head>
<body>

    <!-- ✅ RECUADRO SUPERIOR DERECHA -->
    <div id="user-box">
        👤 <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong><br>
        <a href="destroy_session.php">Tancar sessió</a>
    </div>

    <audio id="button-sound" src="media/boton.mp3" preload="auto"></audio>

    <div class="gameover-container">
        <h1>Game Over!</h1>

        <p>Vols registrar el teu rècord de <?= $score ?> punts?</p>

        <!-- Botón Sí (guardar) -->
        <form method="post" action="gameover.php" style="display:inline;">
            <input type="hidden" name="name" value="<?= $name ?>">
            <input type="hidden" name="score" value="<?= $score ?>">
            <input type="hidden" name="save" value="1">
            <button type="submit" class="btn-link" id="save-btn">Sí</button>
        </form>

        <!-- Botón No -->
        <button type="button" class="btn-link" id="no-btn" onclick="goToIndex()">No</button>
    </div>
    <script src="utils/musicGameover.js"></script>
    <script>
        const buttonSound = document.getElementById("button-sound");

        // ▶️ Función sonido + acción
        function playSound(callback) {
            buttonSound.currentTime = 0;
            buttonSound.play();
            setTimeout(callback, 800);
        }

        // ✅ Guardar récord
        document.getElementById("save-btn").addEventListener("click", (e) => {
            e.preventDefault();
            playSound(() => e.target.closest("form").submit());
        });

        // ✅ Volver al inicio
        function goToIndex() {
            playSound(() => window.location.href = "index.php");
        }

        // ✅ Teclas S (Sí) y N (No)
        document.addEventListener("keydown", (e) => {
            const key = e.key.toLowerCase();
            if (key === "s") document.getElementById("save-btn").click();
            if (key === "n") goToIndex();
        });
    </script>

</body>
</html>
