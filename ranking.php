<?php
session_start();

// Si no hay sesión, vuelve al inicio
if (!isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

$name = $_SESSION['name'];

$rankingFile = __DIR__ . '/ranking.txt';
$ranking = [];
$lastPlayer = $_GET['last'] ?? '';
$lastScore  = isset($_GET['score']) ? intval($_GET['score']) : null;

if(file_exists($rankingFile)) {
    $lines = file($rankingFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($lines as $line) {
        [$playerName, $score] = explode(":", $line);
        $ranking[] = ['name' => htmlspecialchars($playerName), 'score' => intval($score)];
    }
    usort($ranking, fn($a, $b) => $b['score'] <=> $a['score']);
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Rànking de rècords</title>
    <link rel="stylesheet" href="styles.css?<?php echo time(); ?>">
</head>
<body>

    <!-- Recuadro session -->
    <div id="user-box">
        👤 <strong><?php echo htmlspecialchars($name); ?></strong><br>
        <a href="destroy_session.php">Tancar sessió</a>
    </div>

    <audio id="button-sound" src="media/boton.mp3" preload="auto"></audio>

    <div id="ranking-container">
        <h1>Rànking de Jugadors</h1>

        <div>
            <table>
                <thead>
                    <tr>
                        <th>Jugador</th>
                        <th>Punts</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $highlighted = false;
                        foreach($ranking as $i => $p):
                            $highlight = (!$highlighted && $p['name'] === $lastPlayer && $p['score'] === $lastScore);
                            if($highlight) $highlighted = true;
                    ?>
                        <tr class="<?= $i % 2 === 0 ? 'even' : 'odd' ?> <?= $highlight ? 'highlight' : '' ?>">
                            <td><?= $p['name'] ?></td>
                            <td><?= $p['score'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Botón Tornar -->
        <a href="index.php" id="back-btn">ESCAPE</a>
    </div>

    <script src="utils/music3.js"></script>

    <script>
        const buttonSound = document.getElementById("button-sound");
        const backBtn = document.getElementById("back-btn");

        // Función sonido + volver
        function playSoundAndBack() {
            buttonSound.currentTime = 0;
            buttonSound.play();
            setTimeout(() => window.location.href = "index.php", 800);
        }

        backBtn.addEventListener("click", (e) => {
            e.preventDefault();
            playSoundAndBack();
        });

        // ✅ ESC vuelve al inicio
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                playSoundAndBack();
            }
        });
    </script>

</body>
</html>
