<?php
session_name("admin_session");
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Mostrar listado
$mostrar_llistat = isset($_GET['action']) && $_GET['action'] === 'llistar';

// Guardar idioma seleccionado
if (isset($_POST['lang'])) {
    $lang = $_POST['lang'];
    $_SESSION['lang'] = $lang;
} else {
    $lang = $_SESSION['lang'] ?? 'ca';
}

// Cargar archivo de idioma
switch ($lang) {
    case 'es': $lang_file = '../lang/es.php'; break;
    case 'en': $lang_file = '../lang/en.php'; break;
    case 'ca':
    default: $lang_file = '../lang/ca.php'; break;
}
$lang_data = include($lang_file);

// Determinar archivo de frases según idioma
switch ($lang) {
    case 'es': $archivo = '../frases_es.txt'; break;
    case 'en': $archivo = '../frases_en.txt'; break;
    case 'ca':
    default: $archivo = '../frases_ca.txt'; break;
}

// Frase nueva para resaltar
$nuevaFrase = $_SESSION['ultima_frase'] ?? null;
$nivellUltim = $_SESSION['ultim_nivell'] ?? null;

// Listado de frases
if ($mostrar_llistat) {
    if (!file_exists($archivo)) {
        $error_msg = $lang_data['messages']['error_archivo_no_encontrado'];
    } else {
        $contenido = file_get_contents($archivo);
        $frases = json_decode($contenido, true);

        if ($frases === null) {
            $error_msg = $lang_data['messages']['error_json'];
        } else {
            $nivell_seleccionat = $_GET['nivell'] ?? 'facil';
            if (!isset($frases[$nivell_seleccionat])) {
                $nivell_seleccionat = 'facil';
            }

            $listado_html = '<table>';
            $listado_html .= '<thead><tr><th>'.$lang_data['admin_index']['list_sentences'].'</th><th>'.$lang_data['admin_index']['delete'].'</th></tr></thead><tbody>';

            // Paginación
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

                $listado_html .= '<tr'.($es_nueva ? ' class="highlight"' : '').'>';
                $listado_html .= '<td>'.htmlspecialchars($textoFrase).'</td>';
                $listado_html .= '<td>
                    <form method="POST" action="delete_sentence.php" onsubmit="return confirm(\''.$lang_data['admin_index']['delete'].'?\');">
                        <input type="hidden" name="nivell" value="'.htmlspecialchars($nivell_seleccionat).'">
                        <input type="hidden" name="index" value="'.$index.'">
                        <button type="submit" aria-label="'.$lang_data['admin_index']['delete'].'">X</button>
                    </form>
                </td>';
                $listado_html .= '</tr>';
            }

            $listado_html .= '</tbody></table>';

            // Paginación HTML
            if ($total_paginas > 1) {
                $listado_html .= '<div class="pagination">';
                if ($pagina_actual > 1) {
                    $prev = $pagina_actual - 1;
                    $listado_html .= '<a href="?action=llistar&nivell='.htmlspecialchars($nivell_seleccionat).'&pagina='.$prev.'">&laquo; '.$lang_data['admin_index']['paginator'].'</a>';
                }
                $listado_html .= "<span>Pàgina $pagina_actual de $total_paginas</span>";
                if ($pagina_actual < $total_paginas) {
                    $next = $pagina_actual + 1;
                    $listado_html .= '<a href="?action=llistar&nivell='.htmlspecialchars($nivell_seleccionat).'&pagina='.$next.'">'.$lang_data['admin_index']['paginator'].' &raquo;</a>';
                }
                $listado_html .= '</div>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $lang_data['admin_index']['title']; ?></title>
        <link rel="stylesheet" href="../styles.css?<?php echo time(); ?>">
    </head>
    <body class="admin-page-index">
        <div class="admin-container-index">
            <div class="admin-topbar">
                <p class="admin-welcome">
                    <?php echo $lang_data['admin_index']['title']; ?>, <strong><?php echo htmlspecialchars($_SESSION['admin_user']); ?></strong>
                </p>
                <form action="index.php" method="post" class="lang-selector-form">
                    <label for="lang">🌐</label>
                    <select name="lang" id="lang" onchange="this.form.submit()">
                        <option value="ca" <?php if($lang==='ca') echo 'selected'; ?>>Català</option>
                        <option value="es" <?php if($lang==='es') echo 'selected'; ?>>Español</option>
                        <option value="en" <?php if($lang==='en') echo 'selected'; ?>>English</option>
                    </select>
                </form>
            </div>

            <h1><?php echo $lang_data['admin_index']['title']; ?></h1>

            <button id="toggleLlistar" type="button">
                <span class="underline-letter">L</span><?php echo $lang_data['admin_index']['list_sentences']; ?>
            </button>

            <a href="create_sentence.php" class="admin-btn">
                <span class="underline-letter">A</span><?php echo $lang_data['admin_index']['create']; ?>
            </a>

            <a href="logout.php" class="admin-btn">
                <?php echo $lang_data['admin_index']['logout']; ?>
            </a>

            <div id="llistarContainer" class="<?php echo $mostrar_llistat ? '' : 'hidden'; ?>">
                <form method="GET" action="">
                    <input type="hidden" name="action" value="llistar">
                    <label for="nivell"><?php echo $lang_data['admin_index']['difficulty']; ?></label>
                    <select name="nivell" id="nivell" onchange="this.form.submit();">
                        <option value="facil" <?php if (($nivell_seleccionat ?? '') === 'facil') echo 'selected'; ?>>Fàcil</option>
                        <option value="normal" <?php if (($nivell_seleccionat ?? '') === 'normal') echo 'selected'; ?>>Normal</option>
                        <option value="dificil" <?php if (($nivell_seleccionat ?? '') === 'dificil') echo 'selected'; ?>>Difícil</option>
                    </select>
                </form>

                <?php if (isset($_GET['msg'])): ?>
                    <?php
                    $msg_text = $lang_data['messages'][$_GET['msg']] ?? $lang_data['messages']['error_guardado'];
                    $msg_class = ($_GET['msg']==='frase_eliminada') ? 'success' : 'error';
                    echo "<div class='$msg_class'>$msg_text</div>";
                    ?>
                <?php endif; ?>

                <?php if (isset($error_msg)): ?>
                    <div class="error"><?php echo htmlspecialchars($error_msg); ?></div>
                <?php elseif (isset($listado_html)): ?>
                    <?php echo $listado_html; ?>
                <?php endif; ?>
            </div>
        </div>

        <script>
        document.addEventListener("DOMContentLoaded", () => {
            const toggleBtn = document.getElementById("toggleLlistar");
            const container = document.getElementById("llistarContainer");
            const logoutLink = document.querySelector('a[href="logout.php"]');

            const actualizarTextos = (visible) => {
                if (visible) toggleBtn.innerHTML = '<span class="underline-letter">O</span><?php echo $lang_data['admin_index']['hide_sentences']; ?>';
                else toggleBtn.innerHTML = '<span class="underline-letter">L</span><?php echo $lang_data['admin_index']['list_sentences']; ?>';
            };

            const inicialmenteVisible = container && !container.classList.contains("hidden");
            actualizarTextos(inicialmenteVisible);

            toggleBtn.addEventListener("click", () => {
                container.classList.toggle("hidden");
                actualizarTextos(!container.classList.contains("hidden"));
                if (!window.location.search.includes("action=llistar")) {
                    window.location.href = "?action=llistar&nivell=facil";
                }
            });
        });
        </script>
    </body>
</html>
