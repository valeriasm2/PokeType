<?php
session_name("admin_session");
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Guardar idioma seleccionado
if (isset($_POST['lang'])) {
    $lang = $_POST['lang'];
    $_SESSION['lang'] = $lang;
} else {
    $lang = $_SESSION['lang'] ?? 'ca';
}

// Cargar archivo de idioma
$lang_file = "../lang/$lang.php";
if (!file_exists($lang_file)) $lang_file = "../lang/ca.php";
$lang_data = include($lang_file);

// Determinar archivo de frases según idioma
$archivo = match ($lang) {
    'es' => '../frases_es.txt',
    'en' => '../frases_en.txt',
    default => '../frases_ca.txt'
};

$mensaje_nivel = '';
if ($mostrar_llistat && !isset($_GET['nivell'])) {
    $mensaje_nivel = $lang_data['admin_index']['select_level'] ?? 'Selecciona un nivel';
}

// Mostrar listado
$mostrar_llistat = isset($_GET['action']) && $_GET['action'] === 'llistar';
$nivell_seleccionat = $_GET['nivell'] ?? 'facil';
$nuevaFrase = $_SESSION['ultima_frase'] ?? null;
$nivellUltim = $_SESSION['ultim_nivell'] ?? null;

$listado_html = '';
if ($mostrar_llistat) {
    if (!file_exists($archivo)) {
        $error_msg = $lang_data['messages']['error_archivo_no_encontrado'];
    } else {
        $contenido = file_get_contents($archivo);
        $frases = json_decode($contenido, true);

        if ($frases === null) {
            $error_msg = $lang_data['messages']['error_json'];
        } else {
            if (!isset($frases[$nivell_seleccionat])) $nivell_seleccionat = 'facil';

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
                        // ... otros errores que quieras
                }
            }
            /* tabla frases */
            $listado_html .= '<table>';
            $listado_html .= '<thead><tr><th>' . $lang_data['admin_index']['list_sentences'] . '</th><th>' . $lang_data['admin_index']['delete'] . '</th></tr></thead><tbody>';

            /* paginador */
            $por_pagina = 25;
            $total_frases = count($frases[$nivell_seleccionat]);
            $total_paginas = ceil($total_frases / $por_pagina);
            $pagina_actual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
            $inicio = ($pagina_actual - 1) * $por_pagina;
            $frases_paginadas = array_slice($frases[$nivell_seleccionat], $inicio, $por_pagina);

            foreach ($frases_paginadas as $i => $fraseObj) {
                $index = $inicio + $i;
                $textoFrase = $fraseObj['texto'];
                $es_nueva = ($nuevaFrase !== null && $textoFrase === $nuevaFrase && $nivell_seleccionat === $nivellUltim);

                $listado_html .= '<tr' . ($es_nueva ? ' class="highlight"' : '') . '>';
                $listado_html .= '<td>' . htmlspecialchars($textoFrase) . '</td>';
                $listado_html .= '<td>
                    <form method="POST" action="delete_sentence.php" onsubmit="return confirm(\'' . addslashes($lang_data['admin_index']['delete'] ?? 'Delete') . '?\');">
                        <input type="hidden" name="nivell" value="' . htmlspecialchars($nivell_seleccionat) . '">
                        <input type="hidden" name="index" value="' . $index . '">
                        <input type="hidden" name="lang" value="' . htmlspecialchars($lang) . '">
                        <button type="submit" class="delete-btn">' . ($lang_data['admin_index']['delete'] ?? 'X') . '</button>
                    </form>
                </td>';
                $listado_html .= '</tr>';
            }

            $listado_html .= '</tbody></table>';

            // Paginación
            if ($total_paginas > 1) {
                $listado_html .= '<div class="pagination">';
                if ($pagina_actual > 1) $listado_html .= '<a href="?action=llistar&nivell=' . htmlspecialchars($nivell_seleccionat) . '&pagina=' . ($pagina_actual - 1) . '">&laquo; ' . $lang_data['admin_index']['paginator'] . '</a>';
                $listado_html .= "<span>Pàgina $pagina_actual de $total_paginas</span>";
                if ($pagina_actual < $total_paginas) $listado_html .= '<a href="?action=llistar&nivell=' . htmlspecialchars($nivell_seleccionat) . '&pagina=' . ($pagina_actual + 1) . '">' . $lang_data['admin_index']['paginator'] . ' &raquo;</a>';
                $listado_html .= '</div>';
            }
        }
    }
}

function renderButton($text, $id, $link = '#')
{
    $first = substr($text, 0, 1);
    $rest = substr($text, 1);
    if ($link === '#') {
        return '<button type="button" class="admin-btn" id="' . $id . '"><span class="underline-letter">' . $first . '</span>' . $rest . '</button>';
    } else {
        return '<a href="' . $link . '" class="admin-btn" id="' . $id . '"><span class="underline-letter">' . $first . '</span>' . $rest . '</a>';
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <title><?= $lang_data['admin_index']['title'] ?></title>
    <link rel="stylesheet" href="../styles.css?<?= time() ?>">
</head>

<body class="admin-page-index">
    <!-- Selector de idioma -->
    <form method="post" class="lang-selector-admin" action="login.php">
        <label for="lang">🌐</label>
        <select name="lang" id="lang" onchange="this.form.submit()">
            <option value="ca" <?= $lang === 'ca' ? 'selected' : '' ?>>Català</option>
            <option value="es" <?= $lang === 'es' ? 'selected' : '' ?>>Español</option>
            <option value="en" <?= $lang === 'en' ? 'selected' : '' ?>>English</option>
        </select>
    </form>

    <!--PANEL ADMIN-->
    <div class="admin-container-index">
        <div class="admin-topbar">
            <p class="admin-welcome">
                <?= $lang_data['admin_index']['title']; ?>, <strong><?= htmlspecialchars($_SESSION['admin_user']); ?></strong>
            </p>
        </div>

        <h1><?= $lang_data['admin_index']['title'] ?></h1>

        <div class="admin-buttons">
            <?= renderButton($lang_data['admin_index']['list_sentences'], 'toggleLlistar') ?>
            <?= renderButton($lang_data['admin_index']['create'], 'createBtn', 'create_sentence.php') ?>
            <?= renderButton($lang_data['admin_index']['logout'], 'logoutBtn', 'logout.php') ?>
        </div>

        <div id="llistarContainer" class="<?= $mostrar_llistat ? '' : 'hidden' ?>">
            <form method="GET" action="">
                <input type="hidden" name="action" value="llistar">
                <label for="nivell"><?= $lang_data['admin_index']['difficulty'] ?></label>
                <select name="nivell" id="nivell" onchange="this.form.submit();">
                    <!-- Placeholder -->
                    <option value="" disabled selected>
                        <?= htmlspecialchars($lang_data['admin_index']['select_level'] ?? 'Selecciona un nivell') ?>
                    </option>

                    <?php foreach ($lang_data['admin_index']['levels'] as $key => $label): ?>
                        <option value="<?= $key ?>" <?= (isset($_GET['nivell']) && $_GET['nivell'] === $key) ? 'selected' : '' ?>>
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

        // Teclas rápidas
        const keysMap = {
            toggleLlistar: toggleBtn.textContent[0].toLowerCase(),
            createBtn: createBtn.textContent[0].toLowerCase(),
            logoutBtn: logoutBtn.textContent[0].toLowerCase()
        };

        // Cambiar texto Listar ↔ Ocultar
        function underlineFirstLetter(str) {
            if (!str || !str.length) return str;
            return '<span class="underline-letter">' + str[0] + '</span>' + str.slice(1);
        }

        function actualizarTextos(visible) {
            if (visible) {
                toggleBtn.innerHTML = underlineFirstLetter('<?= addslashes($lang_data['admin_index']['hide_sentences']) ?>');
            } else {
                toggleBtn.innerHTML = underlineFirstLetter('<?= addslashes($lang_data['admin_index']['list_sentences']) ?>');
            }
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