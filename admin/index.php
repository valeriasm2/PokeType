<?php
session_name("admin_session");
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

/* ---------------- Idioma ---------------- */
if (isset($_POST['lang'])) {
    $lang = $_POST['lang'];
    $_SESSION['lang'] = $lang;
} else {
    $lang = $_SESSION['lang'] ?? 'ca';
}
$lang_file = "../lang/$lang.php";
if (!file_exists($lang_file)) $lang_file = "../lang/ca.php";
$lang_data = include($lang_file);

/* -------- Archivo de frases por idioma -------- */
$archivo = match ($lang) {
    'es' => '../frases_es.txt',
    'en' => '../frases_en.txt',
    default => '../frases_ca.txt'
};

/* ---------------- Estado UI ---------------- */
$mostrar_llistat = isset($_GET['action']) && $_GET['action'] === 'llistar';
$nivell_seleccionat = $_GET['nivell'] ?? '';
$nuevaFrase = $_SESSION['ultima_frase'] ?? null;
$nivellUltim = $_SESSION['ultim_nivell'] ?? null;

$mensaje_nivel = '';
if ($mostrar_llistat && empty($nivell_seleccionat)) {
    $mensaje_nivel = $lang_data['admin_index']['select_level'] ?? 'Selecciona un nivel';
}

/* ---------------- Listado y paginación ---------------- */
$listado_html = '';
if ($mostrar_llistat) {
    if (!file_exists($archivo)) {
        $error_msg = $lang_data['messages']['error_archivo_no_encontrado'] ?? 'Archivo no encontrado';
    } else {
        $contenido = file_get_contents($archivo);
        $frases = json_decode($contenido, true);

        if (!is_array($frases)) {
            $error_msg = $lang_data['messages']['error_json'] ?? 'Error de formato JSON';
        } else {
            // Si no se eligió nivel o no existe, por defecto ‘facil’
            if (empty($nivell_seleccionat) || !isset($frases[$nivell_seleccionat])) {
                $nivell_seleccionat = 'facil';
            }

            // Mensajes por query
            $mensaje = '';
            if (isset($_GET['msg'])) {
                switch ($_GET['msg']) {
                    case 'frase_eliminada':
                        $mensaje = $lang_data['messages']['frase_eliminada'] ?? "Frase eliminada correctamente";
                        break;
                    case 'error_datos':
                        $mensaje = $lang_data['messages']['error_datos'] ?? "Error: datos incompletos";
                        break;
                    case 'error_frase_no_encontrada':
                        $mensaje = $lang_data['messages']['error_frase_no_encontrada'] ?? "Error: frase no encontrada";
                        break;
                }
            }

            // Paginación
            $por_pagina = 25;
            $total_frases = isset($frases[$nivell_seleccionat]) ? count($frases[$nivell_seleccionat]) : 0;
            $total_paginas = max(1, ceil($total_frases / $por_pagina));
            $pagina_actual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
            if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;
            $inicio = ($pagina_actual - 1) * $por_pagina;
            $frases_paginadas = array_slice($frases[$nivell_seleccionat], $inicio, $por_pagina);

            // Tabla
            $thSentence = $lang_data['admin_index']['list_sentences'] ?? 'Frase';
            $thPhoto    = $lang_data['admin_index']['photo'] ?? 'Foto';
            $thDelete   = $lang_data['admin_index']['delete'] ?? 'Eliminar';
            $btnDelete  = $lang_data['admin_index']['delete_phrase'] ?? 'X';
            $confirmTxt = $lang_data['admin_index']['confirm_delete'] ?? '¿Eliminar esta frase?';

            $listado_html .= '<table>';
            $listado_html .= '<thead><tr>'
                . '<th>' . htmlspecialchars($thSentence) . '</th>'
                . '<th>' . htmlspecialchars($thPhoto) . '</th>'
                . '<th>' . htmlspecialchars($thDelete) . '</th>'
                . '</tr></thead><tbody>';

            foreach ($frases_paginadas as $i => $fraseObj) {
                $index = $inicio + $i;
                $textoFrase = $fraseObj['texto'] ?? '';
                $es_nueva = ($nuevaFrase !== null && $textoFrase === $nuevaFrase && $nivell_seleccionat === $nivellUltim);

                $listado_html .= '<tr' . ($es_nueva ? ' class="highlight"' : '') . '>';
                $listado_html .= '<td>' . htmlspecialchars($textoFrase) . '</td>';

                $nombreFoto = !empty($fraseObj['imagen']) ? htmlspecialchars($fraseObj['imagen']) : '—';
                $listado_html .= '<td class="foto-cell">' . $nombreFoto . '</td>';

                $listado_html .= '<td>
                    <form method="POST" action="delete_sentence.php" onsubmit="return confirm(\'' . addslashes($confirmTxt) . '\');">
                        <input type="hidden" name="nivell" value="' . htmlspecialchars($nivell_seleccionat) . '">
                        <input type="hidden" name="index" value="' . $index . '">
                        <input type="hidden" name="lang" value="' . htmlspecialchars($lang) . '">
                        <button type="submit" class="delete-btn" aria-label="' . htmlspecialchars($thDelete) . '">' . htmlspecialchars($btnDelete) . '</button>
                    </form>
                </td>';
                $listado_html .= '</tr>';
            }

            $listado_html .= '</tbody></table>';

            // Paginador
            if ($total_paginas > 1) {
                $lblPrev = $lang_data['admin_index']['paginator_prev'] ?? 'Anterior';
                $lblNext = $lang_data['admin_index']['paginator_next'] ?? 'Siguiente';
                $lblPage = $lang_data['admin_index']['paginator_page'] ?? 'Página';

                $listado_html .= '<div class="pagination">';

                if ($pagina_actual > 1) {
                    $prev = $pagina_actual - 1;
                    $listado_html .= '<a href="?action=llistar&nivell=' . htmlspecialchars($nivell_seleccionat) . '&pagina=' . $prev . '">&laquo; ' . htmlspecialchars($lblPrev) . '</a>';
                }

                $listado_html .= '<span>' . htmlspecialchars($lblPage) . ' ' . $pagina_actual . ' / ' . $total_paginas . '</span>';

                if ($pagina_actual < $total_paginas) {
                    $next = $pagina_actual + 1;
                    $listado_html .= '<a href="?action=llistar&nivell=' . htmlspecialchars($nivell_seleccionat) . '&pagina=' . $next . '">' . htmlspecialchars($lblNext) . ' &raquo;</a>';
                }

                $listado_html .= '</div>';
            }
        }
    }
}

/* ---------------- Botones con letra subrayada ---------------- */
function renderButton($text, $id, $link = '#')
{
    $first = mb_substr($text, 0, 1);
    $rest = mb_substr($text, 1);
    if ($link === '#') {
        return '<button type="button" class="admin-btn" id="' . $id . '"><span class="underline-letter">' . htmlspecialchars($first) . '</span>' . htmlspecialchars($rest) . '</button>';
    } else {
        return '<a href="' . htmlspecialchars($link) . '" class="admin-btn" id="' . $id . '"><span class="underline-letter">' . htmlspecialchars($first) . '</span>' . htmlspecialchars($rest) . '</a>';
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($lang_data['admin_index']['title'] ?? 'Panel Admin') ?></title>
    <link rel="stylesheet" href="../styles.css?<?= time() ?>">
</head>
<body class="admin-page-index">
    <img src="/images/pipluuu2.png" class="piplu-bottom" alt="Gengar estático abajo">

    <!-- Selector de idioma -->
    <form method="post" class="lang-selector-admin" action="index.php">
        <label for="lang">🌐</label>
        <select name="lang" id="lang" onchange="this.form.submit()">
            <option value="ca" <?= $lang === 'ca' ? 'selected' : '' ?>>Català</option>
            <option value="es" <?= $lang === 'es' ? 'selected' : '' ?>>Español</option>
            <option value="en" <?= $lang === 'en' ? 'selected' : '' ?>>English</option>
        </select>
    </form>

    <!-- PANEL ADMIN -->
    <div class="admin-container-index">
        <div class="admin-topbar">
            <p class="admin-welcome">
                <?= htmlspecialchars($lang_data['admin_index']['title'] ?? 'Panel Admin'); ?>,
                <strong><?= htmlspecialchars($_SESSION['admin_user'] ?? '') ?></strong>
            </p>
        </div>

        <h1><?= htmlspecialchars($lang_data['admin_index']['title'] ?? 'Panel Admin') ?></h1>

        <div class="admin-buttons">
            <?= renderButton($lang_data['admin_index']['list_sentences'] ?? 'Listar frases', 'toggleLlistar') ?>
            <?= renderButton($lang_data['admin_index']['create'] ?? 'Crear', 'createBtn', 'create_sentence.php') ?>
            <?= renderButton($lang_data['admin_index']['logout'] ?? 'Salir', 'logoutBtn', 'logout.php') ?>
        </div>

        <div id="llistarContainer" class="<?= $mostrar_llistat ? '' : 'hidden' ?>">
            <form method="GET" action="">
                <input type="hidden" name="action" value="llistar">
                <label for="nivell"><?= htmlspecialchars($lang_data['admin_index']['difficulty'] ?? 'Dificultad') ?></label>
                <select name="nivell" id="nivell" onchange="this.form.submit();">
                    <option value="" <?= empty($nivell_seleccionat) ? 'selected' : '' ?> disabled>
                        <?= htmlspecialchars($lang_data['admin_index']['select_level'] ?? 'Selecciona un nivel') ?>
                    </option>
                    <?php foreach (($lang_data['admin_index']['levels'] ?? ['facil'=>'Fácil','normal'=>'Normal','dificil'=>'Difícil']) as $key => $label): ?>
                        <option value="<?= htmlspecialchars($key) ?>" <?= ($nivell_seleccionat === $key) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php if (!empty($mensaje_nivel)): ?>
                <div class="info"><?= htmlspecialchars($mensaje_nivel) ?></div>
            <?php endif; ?>

            <?php if (isset($error_msg)): ?>
                <div class="error"><?= htmlspecialchars($error_msg) ?></div>
            <?php endif; ?>

            <?php if (!empty($mensaje)): ?>
                <div class="<?= ($_GET['msg'] ?? '') === 'frase_eliminada' ? 'success' : 'error' ?>">
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($listado_html)): ?>
                <?= $listado_html ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById("toggleLlistar");
        const container = document.getElementById("llistarContainer");
        const createBtn = document.getElementById("createBtn");
        const logoutBtn = document.getElementById("logoutBtn");

        // Teclas rápidas: primera letra subrayada
        const keysMap = {
            toggleLlistar: toggleBtn.textContent.trim().charAt(0).toLowerCase(),
            createBtn: createBtn.textContent.trim().charAt(0).toLowerCase(),
            logoutBtn: logoutBtn.textContent.trim().charAt(0).toLowerCase()
        };

        function underlineFirstLetter(str) {
            if (!str || !str.length) return str;
            return '<span class="underline-letter">' + str[0] + '</span>' + str.slice(1);
        }

        function actualizarTextos(visible) {
            const txtShow = '<?= addslashes($lang_data['admin_index']['list_sentences'] ?? 'Listar frases') ?>';
            const txtHide = '<?= addslashes($lang_data['admin_index']['hide_sentences'] ?? 'Ocultar frases') ?>';
            toggleBtn.innerHTML = underlineFirstLetter(visible ? txtHide : txtShow);
        }

        actualizarTextos(!container.classList.contains("hidden"));

        toggleBtn.addEventListener("click", () => {
            container.classList.toggle("hidden");
            actualizarTextos(!container.classList.contains("hidden"));
        });

        // Atajos de teclado
        document.addEventListener("keydown", (e) => {
            const active = document.activeElement;
            if (active && ["INPUT", "TEXTAREA", "SELECT"].includes(active.tagName)) return;

            if (e.key.toLowerCase() === keysMap.toggleLlistar) toggleBtn.click();
            else if (e.key.toLowerCase() === keysMap.createBtn) window.location.href = 'create_sentence.php';
            else if (e.key.toLowerCase() === keysMap.logoutBtn) window.location.href = 'logout.php';
        });
    </script>
</body>
</html>
