<?php
session_start();
require_once 'admin/logger.php';

// Redirige a index si no hay sesión
if (!isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

// Idioma y traducciones
$lang = $_SESSION['lang'] ?? 'ca';
$langFile = __DIR__ . "/lang/{$lang}.php";
$langArray = file_exists($langFile) ? require $langFile : [];
$t = $langArray['ranking'] ?? [];
$tIndex = $langArray['index'] ?? [];

$name = $_SESSION['name'] ?? '';

// Log de acceso
logJuego("VIEW_RANKING", "ranking.php", "Usuario '$name' accedió al ranking");

// Parámetros de resaltado (última partida)
$lastPlayer = isset($_GET['last']) ? htmlspecialchars($_GET['last']) : '';
$lastScore  = isset($_GET['score']) ? intval($_GET['score']) : null;
$lastTime   = isset($_GET['time']) ? floatval($_GET['time']) : null;
$lastCombo  = isset($_GET['combo']) ? intval($_GET['combo']) : null;
$lastPerma  = isset($_GET['permadeath']) ? intval($_GET['permadeath']) : null;

// Leer ranking
$rankingFile = __DIR__ . '/ranking.txt';
$ranking = [];

if (file_exists($rankingFile)) {
    $lines = file($rankingFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Formato: nombre:score:time:combo:permadeath
        $parts = explode(':', trim($line));
        $playerName = $parts[0] ?? '';
        $scoreVal   = isset($parts[1]) ? intval($parts[1]) : 0;
        // guardamos el tiempo como float si es numérico, o '' si está vacío para mostrar '-'
        $timeRaw    = $parts[2] ?? '';
        $timeVal    = (is_numeric($timeRaw) ? floatval($timeRaw) : '');
        $comboVal   = isset($parts[3]) ? intval($parts[3]) : 1;
        $permaVal   = isset($parts[4]) ? intval($parts[4]) : 0;

        $ranking[] = [
            'name'       => htmlspecialchars($playerName),
            'score'      => $scoreVal,
            'time'       => $timeVal,
            'combo'      => $comboVal,
            'permadeath' => $permaVal
        ];
    }

    // Orden: score desc, y si empate, menor tiempo (solo si ambos tienen tiempo válido)
    usort($ranking, function($a, $b) {
        if ($b['score'] === $a['score']) {
            $aHasTime = ($a['time'] !== '' && $a['time'] !== null);
            $bHasTime = ($b['time'] !== '' && $b['time'] !== null);
            if ($aHasTime && $bHasTime) {
                return $a['time'] <=> $b['time'];
            }
            return 0;
        }
        return $b['score'] <=> $a['score'];
    });
}

// Paginación
$resultados_por_pagina = 25;
$pagina_actual = (isset($_GET['pag']) && is_numeric($_GET['pag']) && $_GET['pag'] > 0) ? intval($_GET['pag']) : 1;
$total_resultados = count($ranking);
$total_paginas = max(1, ceil($total_resultados / $resultados_por_pagina));
if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;
$inicio = ($pagina_actual - 1) * $resultados_por_pagina;
$ranking_paginado = array_slice($ranking, $inicio, $resultados_por_pagina);

// Render del paginador
function render_pagination_ranking($pagina_actual, $total_paginas)
{
    if ($total_paginas <= 1) return '';
    $html = '<div class="pagination_ranking">';
    $link_base = strtok($_SERVER['REQUEST_URI'], '?');
    $params = $_GET;

    // Prev
    if ($pagina_actual > 1) {
        $params['pag'] = $pagina_actual - 1;
        $html .= '<a href="' . htmlspecialchars($link_base . '?' . http_build_query($params)) . '">&lt;</a>';
    }

    // Números
    for ($i = 1; $i <= $total_paginas; $i++) {
        if ($i == $pagina_actual) {
            $html .= '<span>' . $i . '</span>';
        } else {
            $params['pag'] = $i;
            $html .= '<a href="' . htmlspecialchars($link_base . '?' . http_build_query($params)) . '">' . $i . '</a>';
        }
    }

    // Next
    if ($pagina_actual < $total_paginas) {
        $params['pag'] = $pagina_actual + 1;
        $html .= '<a href="' . htmlspecialchars($link_base . '?' . http_build_query($params)) . '">&gt;</a>';
    }

    $html .= '</div>';
    return $html;
}

// Función para decidir si resaltar una fila
function should_highlight($p, $lastPlayer, $lastScore, $lastTime, $lastCombo, $lastPerma) {
    if (!$lastPlayer) return false;
    if ($p['name'] !== $lastPlayer) return false;
    if ($lastScore !== null && $p['score'] !== $lastScore) return false;
    // tolerancia de tiempo si viene
    if ($lastTime !== null) {
        $pTime = $p['time'];
        if ($pTime === '' || $pTime === null) return false;
        if (abs(floatval($pTime) - floatval($lastTime)) > 0.01) return false; // tolerancia 0.01s
    }
    if ($lastCombo !== null && intval($p['combo']) !== intval($lastCombo)) return false;
    if ($lastPerma !== null && intval($p['permadeath']) !== intval($lastPerma)) return false;
    return true;
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['title'] ?? 'Ranking de Jugadores' ?></title>
    <link rel="stylesheet" href="styles.css?<?= time(); ?>">
</head>
<body>
    <img src="images/fantasmaGengar.png" alt="Gengar Fantasma" class="gengar-float">
    <img src="images/gengar8.png" class="gengar-bottom" alt="Gengar estático abajo">

    <div id="user-box">
        👤 <strong><?= htmlspecialchars($name) ?></strong><br>
        <a href="destroy_session.php"><?= $tIndex['logout'] ?? 'Cerrar sesión' ?></a>
    </div>

    <audio id="bg-music" src="media/ranking.mp3" loop preload="auto"></audio>
    <audio id="button-sound" src="media/boton.mp3" preload="auto"></audio>

    <div id="ranking-container">
        <h1><?= $t['title'] ?? 'Ranking de Jugadores' ?></h1>

        <table>
            <thead>
                <tr>
                    <th><?= $t['name'] ?? 'Jugador' ?></th>
                    <th><?= $t['score'] ?? 'Puntos' ?></th>
                    <th><?= $t['time'] ?? 'Tiempo (s)' ?></th>
                    <th><?= $t['combo'] ?? 'Combo' ?></th>
                    <th><?= $t['permadeath'] ?? 'Permadeath' ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $highlighted = false;
                foreach ($ranking_paginado as $i => $p):
                    $globalIndex = $inicio + $i;
                    $highlight = (!$highlighted && should_highlight($p, $lastPlayer, $lastScore, $lastTime, $lastCombo, $lastPerma));
                    if ($highlight) $highlighted = true;
                ?>
                <tr class="<?= $globalIndex % 2 === 0 ? 'even' : 'odd' ?> <?= $highlight ? 'highlight' : '' ?>">
                    <td><?= $p['name'] ?><?= $p['permadeath'] ? ' 💀' : '' ?></td>
                    <td><?= $p['score'] ?></td>
                    <td>
                        <?php
                        if ($p['time'] !== '' && $p['time'] !== null) {
                            echo number_format(floatval($p['time']), 2);
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    <td>x<?= $p['combo'] ?></td>
                    <td><?= $p['permadeath'] ? ($t['permadeath_flag'] ?? 'Sí') : ($t['permadeath_flag_no'] ?? 'No') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($total_paginas > 1): ?>
            <?= render_pagination_ranking($pagina_actual, $total_paginas); ?>
        <?php endif; ?>

        <!-- Botón volver -->
        <button type="button" id="back-btn">
            <?php
                $backLabel = htmlspecialchars($t['back'] ?? 'ESCAPE');
                $escPos = stripos($backLabel, 'ESC');
                if ($escPos !== false) {
                    echo substr($backLabel, 0, $escPos)
                        . '<span class="underline-letter">' . substr($backLabel, $escPos, 3) . '</span>'
                        . substr($backLabel, $escPos + 3);
                } else {
                    echo $backLabel;
                }
            ?>
        </button>
    </div>

    <script src="utils/music3.js"></script>
    <script>
        const buttonSound = document.getElementById("button-sound");
        const backBtn = document.getElementById("back-btn");

        function playSoundAndBack() {
            buttonSound.currentTime = 0;
            buttonSound.play();
            setTimeout(() => window.location.href = "index.php", 800);
        }

        backBtn.addEventListener("click", (e) => {
            e.preventDefault();
            playSoundAndBack();
        });

        document.addEventListener("keydown", (e) => {
            if (e.repeat) return;
            if (e.key === "Escape") {
                e.preventDefault();
                playSoundAndBack();
                backBtn.classList.add('button-pressed');
                setTimeout(() => backBtn.classList.remove('button-pressed'), 200);
            }
        });

        // Música con primer click si no autoplay
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
