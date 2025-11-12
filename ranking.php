<?php
session_start();

require_once 'admin/logger.php';

// Adaptada: Redirige a index si no hay sesión
if (!isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

// Idioma y traducciones
$lang = $_SESSION['lang'] ?? 'ca';
$langFile = __DIR__ . "/lang/{$lang}.php";
$langArray = file_exists($langFile) ? require $langFile : [];
$t = $langArray['ranking'] ?? [];

$name = $_SESSION['name'] ?? '';

// Log de acceso al ranking
logJuego("VIEW_RANKING", "ranking.php", "Usuario '$name' accedió al ranking");

// Leer y procesar el ranking (formato nombre:score:time)
$rankingFile = __DIR__ . '/ranking.txt';
$ranking = [];
$lastPlayer = $_GET['last'] ?? '';
$lastScore = isset($_GET['score']) ? intval($_GET['score']) : null;
$lastTime = isset($_GET['time']) ? floatval($_GET['time']) : null;

if (file_exists($rankingFile)) {
    $lines = file($rankingFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $parts = explode(':', $line);
        $playerName = $parts[0] ?? '';
        $score = isset($parts[1]) ? intval($parts[1]) : 0;
        $time = isset($parts[2]) ? floatval($parts[2]) : null;
        $ranking[] = [
            'name' => htmlspecialchars($playerName),
            'score' => $score,
            'time' => $time
        ];
    }
    // Ordenar primero por score descendente, luego menor tiempo (si empate)
    usort($ranking, function($a, $b) {
        if ($b['score'] === $a['score']) {
            // Menor tiempo gana si ambos tienen tiempo válido
            if ($a['time'] !== null && $b['time'] !== null) {
                return $a['time'] <=> $b['time'];
            }
            return 0;
        }
        return $b['score'] <=> $a['score'];
    });
}

// Paginación adaptada
$resultados_por_pagina = 25;
$pagina_actual = (isset($_GET['pag']) && is_numeric($_GET['pag']) && $_GET['pag'] > 0) ? intval($_GET['pag']) : 1;
$total_resultados = count($ranking);
$total_paginas = max(1, ceil($total_resultados / $resultados_por_pagina));
if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;
$inicio = ($pagina_actual - 1) * $resultados_por_pagina;
$ranking_paginado = array_slice($ranking, $inicio, $resultados_por_pagina);

// Renderizar paginador del ranking
function render_pagination_ranking($pagina_actual, $total_paginas)
{
    if ($total_paginas <= 1) return '';
    $html = '<div class="pagination_ranking">' . PHP_EOL;
    $link_base = strtok($_SERVER['REQUEST_URI'], '?');
    $params = $_GET;
    // Anterior
    if ($pagina_actual > 1) {
        $params['pag'] = $pagina_actual - 1;
        $html .= '<a href="' . htmlspecialchars($link_base . '?' . http_build_query($params)) . '">&lt;</a>';
    }
    // Números de página
    for ($i = 1; $i <= $total_paginas; $i++) {
        if ($i == $pagina_actual) {
            $html .= '<span>' . $i . '</span>';
        } else {
            $params['pag'] = $i;
            $html .= '<a href="' . htmlspecialchars($link_base . '?' . http_build_query($params)) . '">' . $i . '</a>';
        }
    }
    // Siguiente
    if ($pagina_actual < $total_paginas) {
        $params['pag'] = $pagina_actual + 1;
        $html .= '<a href="' . htmlspecialchars($link_base . '?' . http_build_query($params)) . '">&gt;</a>';
    }
    $html .= '</div>' . PHP_EOL;
    return $html;
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['title'] ?? 'Rànking de Jugadors' ?></title>
    <link rel="stylesheet" href="styles.css?<?= time(); ?>">
</head>
<body>
    <img src="images/fantasmaGengar.png" alt="Gengar Fantasma" class="gengar-float">
    <img src="/images/gengar8.png" class="gengar-bottom" alt="Gengar estático abajo">

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
                    <th><?= $t['time'] ?? 'Temps (s)' ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $highlighted = false;
                foreach ($ranking_paginado as $i => $p):
                    $globalIndex = $inicio + $i;
                    // Adaptada: resalta la fila del jugador si coincide nombre+score+time
                    $highlight = (
                        !$highlighted
                        && $p['name'] === $lastPlayer
                        && $p['score'] === $lastScore
                        && (
                            ($lastTime === null && ($p['time'] === null || $p['time'] === 0))
                            || ($lastTime !== null && $p['time'] !== null && abs($p['time'] - $lastTime) < 0.0001)
                        )
                    );
                    if ($highlight) $highlighted = true;
                ?>
                <tr class="<?= $globalIndex % 2 === 0 ? 'even' : 'odd' ?> <?= $highlight ? 'highlight' : '' ?>">
                    <td><?= $p['name'] ?></td>
                    <td><?= $p['score'] ?></td>
                    <td>
                        <?php
                        if ($p['time'] !== null)
                            // Mostramos 2 decimales si hay tiempo válido, si no '-'
                            echo ($p['time'] > 0 ? number_format($p['time'], 2) : '-');
                        else
                            echo '-';
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($total_paginas > 1): ?>
            <?= render_pagination_ranking($pagina_actual, $total_paginas); ?>
        <?php endif; ?>

    <!-- Botón volver Escape -->
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
            const active = document.activeElement;
            if (["INPUT", "TEXTAREA", "SELECT"].includes(active.tagName)) return;

            // Buscar la letra subrayada en el botón de volver
            const underlineSpan = backBtn.querySelector(".underline-letter");
            if (underlineSpan) {
                const key = underlineSpan.textContent.trim().toLowerCase();
                // Manejar "esc" (3 letras) o letra individual
                if (key === "esc" && e.key === "Escape") {
                    e.preventDefault();
                    playSoundAndBack();
                    backBtn.classList.add('button-pressed');
                    setTimeout(() => backBtn.classList.remove('button-pressed'), 200);
                } else if (key.length === 1 && e.key.toLowerCase() === key) {
                    e.preventDefault();
                    playSoundAndBack();
                    backBtn.classList.add('button-pressed');
                    setTimeout(() => backBtn.classList.remove('button-pressed'), 200);
                }
            }
            
            // Fallback: Escape siempre funciona
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