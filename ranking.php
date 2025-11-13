<?php
session_start();

// Incluir sistema de logs
require_once 'admin/logger.php';
require_once __DIR__ . '/utils/lang.php';

// Si no hay sesión, vuelve al inicio
if (!isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

$name = $_SESSION['name'];

// Log acceso a ranking
logJuego("VIEW_RANKING", "ranking.php", "Usuario '$name' accedió al ranking");

$rankingFile = __DIR__ . '/ranking.txt';
$ranking = [];
$lastPlayer = $_GET['last'] ?? '';
$lastScore  = isset($_GET['score']) ? intval($_GET['score']) : null;

if(file_exists($rankingFile)) {
    $lines = file($rankingFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($lines as $line) {
        // Formato esperado: nombre:puntaje:tiempo:combo:permadeath
        $parts = explode(":", $line);
        $playerName = $parts[0] ?? '';
        $scoreVal = isset($parts[1]) ? intval($parts[1]) : 0;
        $timeVal = isset($parts[2]) ? $parts[2] : '';
        $comboVal = isset($parts[3]) ? intval($parts[3]) : 1;
        $permaVal = isset($parts[4]) ? intval($parts[4]) : 0;
        $ranking[] = [
            'name' => htmlspecialchars($playerName),
            'score' => $scoreVal,
            'time' => $timeVal,
            'combo' => $comboVal,
            'permadeath' => $permaVal
        ];
    }
    usort($ranking, fn($a, $b) => $b['score'] <=> $a['score']);
}

// --- Paginación ---
$por_pagina = 25;
$total_registros = count($ranking);
$total_paginas = ($por_pagina > 0) ? (int)ceil($total_registros / $por_pagina) : 1;
$pagina_actual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
if ($pagina_actual > $total_paginas && $total_paginas > 0) {
    $pagina_actual = $total_paginas;
}
$inicio = ($pagina_actual - 1) * $por_pagina;
$ranking_paginado = array_slice($ranking, $inicio, $por_pagina);
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(pt_current_lang()); ?>">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars(t('ranking.title')); ?></title>
    <link rel="stylesheet" href="styles.css?<?php echo time(); ?>">
</head>
<body>

    <!-- Recuadro session -->
    <div id="user-box">
        👤 <strong><?php echo htmlspecialchars($name); ?></strong><br>
        <a href="destroy_session.php"><?= htmlspecialchars(t('index.logout')); ?></a>
    </div>

    <!-- Audio para música de fondo y efectos -->
    <audio id="bg-music" src="media/ranking.mp3" loop preload="auto"></audio>
    <audio id="button-sound" src="media/boton.mp3" preload="auto"></audio>

    <div id="ranking-container">
        <h1><?= htmlspecialchars(t('ranking.title')); ?></h1>

        <div>
            <table>
                <thead>
                    <tr>
                        <th><?= htmlspecialchars(t('ranking.name')); ?></th>
                        <th><?= htmlspecialchars(t('ranking.score')); ?></th>
                        <th><?= htmlspecialchars(t('ranking.time')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $highlighted = false;
                        foreach($ranking_paginado as $i => $p):
                            $highlight = (!$highlighted && $p['name'] === $lastPlayer && $p['score'] === $lastScore);
                            if($highlight) $highlighted = true;
                    ?>
                        <tr class="<?= $i % 2 === 0 ? 'even' : 'odd' ?> <?= $highlight ? 'highlight' : '' ?>">
                            <?php $displayName = $p['name'] . ($p['permadeath'] ? ' 💀' : ''); ?>
                            <td><?= $displayName ?></td>
                            <td><?= $p['score'] ?></td>
                            <td><?= $p['permadeath'] ? htmlspecialchars($p['time']) : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($total_paginas > 1): ?>
                <div class="pagination">
                    <?php
                        $queryBase = [];
                        if ($lastPlayer !== '') { $queryBase['last'] = $lastPlayer; }
                        if ($lastScore !== null) { $queryBase['score'] = $lastScore; }
                        $queryBase['pagina'] = 1; // placeholder
                        $buildUrl = function($pagina) use ($queryBase) {
                            $queryBase['pagina'] = $pagina;
                            return 'ranking.php?' . http_build_query($queryBase);
                        };
                    ?>
                    <?php if ($pagina_actual > 1): $prev = $pagina_actual - 1; ?>
                        <a href="<?= htmlspecialchars($buildUrl($prev)); ?>"><?= htmlspecialchars(t('admin.pagination_prev')); ?></a>
                    <?php endif; ?>
                    <span><?= htmlspecialchars(t('admin.pagination_page_of', ['current' => $pagina_actual, 'total' => $total_paginas])); ?></span>
                    <?php if ($pagina_actual < $total_paginas): $next = $pagina_actual + 1; ?>
                        <a href="<?= htmlspecialchars($buildUrl($next)); ?>"><?= htmlspecialchars(t('admin.pagination_next')); ?></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Botón Tornar -->
        <a href="index.php" id="back-btn"><?php echo str_replace('ESC', '<span class="underline-letter">ESC</span>', htmlspecialchars(t('ranking.back'))); ?></a>
    </div>

    <script src="utils/music3.js"></script>

    <script>
        // Fix para Chrome: activar música con primer clic si autoplay falla
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                const bgMusic = document.getElementById('bg-music');
                if (bgMusic && bgMusic.paused) {
                    // Si la música no está sonando, activarla con primer clic
                    const activateMusic = () => {
                        bgMusic.play().catch(() => {});
                        document.removeEventListener('click', activateMusic);
                    };
                    document.addEventListener('click', activateMusic);
                }
            }, 500); // Esperar medio segundo para que music3.js termine
        });

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
