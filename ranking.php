<?php
session_start();

// Incluir sistema de logs
require_once 'admin/logger.php';

// Si no hay sesión → index
if (!isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

// Selección de idioma
$lang = $_SESSION['lang'] ?? 'ca';
$langFile = __DIR__ . "/lang/{$lang}.php";
$langArray = file_exists($langFile) ? require $langFile : [];
$t = $langArray['ranking'] ?? []; // Textos para ranking

$name = $_SESSION['name'];

// Log acceso a ranking
logJuego("VIEW_RANKING", "ranking.php", "Usuario '$name' accedió al ranking");

// Cargar ranking
$rankingFile = __DIR__ . '/ranking.txt';
$ranking = [];
$lastPlayer = $_GET['last'] ?? '';
$lastScore  = isset($_GET['score']) ? intval($_GET['score']) : null;

if (file_exists($rankingFile)) {
    $lines = file($rankingFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        [$playerName, $score] = explode(":", $line);
        $ranking[] = ['name' => htmlspecialchars($playerName), 'score' => intval($score)];
    }
    usort($ranking, fn($a, $b) => $b['score'] <=> $a['score']);
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <title><?= $t['title'] ?? 'Rànking de Jugadors' ?></title>
    <link rel="stylesheet" href="styles.css?<?= time(); ?>">
</head>

<body>

    <div id="user-box">
        👤 <strong><?= htmlspecialchars($name) ?></strong><br>
        <a href="destroy_session.php"><?= $langArray['index']['logout'] ?? 'Cerrar sesión' ?></a>
    </div>

    <audio id="bg-music" src="media/ranking.mp3" loop preload="auto"></audio>
    <audio id="button-sound" src="media/boton.mp3" preload="auto"></audio>

    <div id="ranking-container">
        <h1><?= $t['title'] ?? 'Rànking de Jugadors' ?></h1>

        <table>
            <thead>
                <tr>
                    <th><?= $t['name'] ?? 'Jugador' ?></th>
                    <th><?= $t['score'] ?? 'Punts' ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $highlighted = false;
                foreach ($ranking as $i => $p):
                    $highlight = (!$highlighted && $p['name'] === $lastPlayer && $p['score'] === $lastScore);
                    if ($highlight) $highlighted = true;
                ?>
                    <tr class="<?= $i % 2 === 0 ? 'even' : 'odd' ?> <?= $highlight ? 'highlight' : '' ?>">
                        <td><?= $p['name'] ?></td>
                        <td><?= $p['score'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Botón volver -->
        <a href="index.php" id="back-btn">
            <span class="underline-letter">ESC</span><?= substr($t['back'] ?? 'Tornar', 3) ?>
        </a>
    </div>

    <script src="utils/music3.js"></script>
    <script>
        const buttonSound = document.getElementById("button-sound");
        const backBtn = document.getElementById("back-btn");

        // Sonido + volver al index
        function playSoundAndBack() {
            buttonSound.currentTime = 0;
            buttonSound.play();
            setTimeout(() => window.location.href = "index.php", 800);
        }

        backBtn.addEventListener("click", (e) => {
            e.preventDefault();
            playSoundAndBack();
        });

        // ESC → volver al index
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") playSoundAndBack();
        });

        // Activar música con primer clic si autoplay falla (Chrome)
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                const bgMusic = document.getElementById('bg-music');
                if (bgMusic && bgMusic.paused) {
                    const activateMusic = () => {
                        bgMusic.play().catch(() => {});
                        document.removeEventListener('click', activateMusic);
                    };
                    document.addEventListener('click', activateMusic);
                }
            }, 500);
        });
    </script>

</body>

</html>