<?php
session_name("admin_session");
session_start();

// Lenguaje para admin y acceso a traducciones si se usan
require_once __DIR__ . '/../utils/lang.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Selector de idioma (persistido en sesión admin)
if (isset($_POST['setlang']) && isset($_POST['lang'])) {
    $newLang = $_POST['lang'];
    if (in_array($newLang, pt_supported_langs(), true)) {
        $_SESSION['lang'] = $newLang;
    }
    header('Location: index.php');
    exit;
}

$mostrar_llistat = isset($_GET['action']) && $_GET['action'] === 'llistar';

// Obtengo la frase nueva que viene de create_sentence para resaltar
$nuevaFrase = $_SESSION['ultima_frase'] ?? null;
$nivellUltim = $_SESSION['ultim_nivell'] ?? null;

if ($mostrar_llistat) {
    $lang = pt_current_lang();
    $archivo_lang = __DIR__ . '/../frases.' . $lang . '.txt';
    $archivo_fallback = __DIR__ . '/../frases.txt';

    if (is_file($archivo_lang)) {
        $contenido = file_get_contents($archivo_lang);
    } elseif (is_file($archivo_fallback)) {
        $contenido = file_get_contents($archivo_fallback);
    } else {
        $contenido = null;
    }

    $frases = $contenido ? json_decode($contenido, true) : null;

    if ($frases === null) {
        $error_msg = t('admin.error_read');
    } else {
        $nivell_seleccionat = $_GET['nivell'] ?? 'facil';
        if (!isset($frases[$nivell_seleccionat])) {
            $nivell_seleccionat = 'facil';
        }

    $listado_html = '<table>';
    $listado_html .= '<thead><tr><th>' . htmlspecialchars(t('admin.table_phrase')) . '</th><th>' . htmlspecialchars(t('admin.table_image')) . '</th><th>' . htmlspecialchars(t('admin.table_delete')) . '</th></tr></thead><tbody>';


        // --- Paginacion ---
        $por_pagina = 25;
        $total_frases = count($frases[$nivell_seleccionat]);
        $total_paginas = ceil($total_frases / $por_pagina);

        $pagina_actual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;

        
        $inicio = ($pagina_actual - 1) * $por_pagina;
        $frases_paginadas = array_slice($frases[$nivell_seleccionat], $inicio, $por_pagina);

        foreach ($frases_paginadas as $i => $fraseObj) {
        $index = $inicio + $i;
        $textoFrase = $fraseObj['texto'];

        // lo q hago es comparar si la frase es nueva y su niel de dificultad pa resaltarla o no
        $es_nueva = ($nuevaFrase !== null && $textoFrase === $nuevaFrase && $nivell_seleccionat === $nivellUltim);

        $listado_html .= '<tr' . ($es_nueva ? ' class="highlight"' : '') . '>';
        $listado_html .= '<td>' . htmlspecialchars($textoFrase) . '</td>';

        $nombreFoto = !empty($fraseObj['imagen']) ? htmlspecialchars($fraseObj['imagen']) : '—';
        $listado_html .= '<td class="foto-cell">' . $nombreFoto . '</td>';


        $listado_html .= '<td>
            <form method="POST" action="delete_sentence.php" onsubmit="return confirm(' . t_js('admin.confirm_delete') . ');">
                <input type="hidden" name="nivell" value="' . htmlspecialchars($nivell_seleccionat) . '">
                <input type="hidden" name="index" value="' . $index . '">
                <button type="submit" aria-label="Esborra">X</button>
            </form>
        </td>';
        $listado_html .= '</tr>';
    }


        $listado_html .= '</tbody></table>';

        // --- Navegació de pàgines ---
        if ($total_paginas > 1) {
            $listado_html .= '<div class="pagination">';

            if ($pagina_actual > 1) {
                $prev = $pagina_actual - 1;
                $listado_html .= '<a href="?action=llistar&nivell=' . htmlspecialchars($nivell_seleccionat) . '&pagina=' . $prev . '">' . htmlspecialchars(t('admin.pagination_prev')) . '</a>';
            }

            $listado_html .= '<span>' . htmlspecialchars(t('admin.pagination_page_of', ['current' => $pagina_actual, 'total' => $total_paginas])) . '</span>';

            if ($pagina_actual < $total_paginas) {
                $next = $pagina_actual + 1;
                $listado_html .= '<a href="?action=llistar&nivell=' . htmlspecialchars($nivell_seleccionat) . '&pagina=' . $next . '">' . htmlspecialchars(t('admin.pagination_next')) . '</a>';
            }

            $listado_html .= '</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(pt_current_lang()); ?>">
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars(t('admin.title')); ?></title>
        <link rel="stylesheet" href="../styles.css?<?php echo time(); ?>">
    </head>

    <body class="admin-page-index">
            <!-- Selector de idioma para Admin (fuera del contenedor, fijo) -->
            <form action="index.php" method="post" style="position:fixed; top:10px; left:10px; white-space:nowrap; z-index:9999;">
                <input type="hidden" name="setlang" value="1">
                <label for="lang" style="margin-right:6px;"><?= htmlspecialchars(t('admin.language_label')); ?></label>
                <?php $cur = pt_current_lang(); $names = pt_load_messages($cur)['lang_names'] ?? ['es'=>'Español','ca'=>'Català','en'=>'English']; ?>
                <select name="lang" id="lang" onchange="this.form.submit()">
                    <option value="es" <?= $cur==='es'?'selected':''; ?>><?= htmlspecialchars($names['es'] ?? 'Español'); ?></option>
                    <option value="ca" <?= $cur==='ca'?'selected':''; ?>><?= htmlspecialchars($names['ca'] ?? 'Català'); ?></option>
                    <option value="en" <?= $cur==='en'?'selected':''; ?>><?= htmlspecialchars($names['en'] ?? 'English'); ?></option>
                </select>
            </form>
        <div class="admin-container-index">
            <p><?= htmlspecialchars(t('admin.welcome')); ?>, <strong><?php echo htmlspecialchars($_SESSION['admin_user']); ?></strong></p>

            <h1><?= htmlspecialchars(t('admin.title')); ?></h1>

            <button id="toggleLlistar" type="button"><?= htmlspecialchars(t('admin.list')); ?></button>

            <a href="create_sentence.php" class="admin-btn">
                <?= htmlspecialchars(t('admin.add_sentence')); ?>
            </a>

            <a href="logout.php" class="admin-btn">
                <?= htmlspecialchars(t('admin.logout')); ?>
            </a>

            <div id="llistarContainer" class="<?php echo $mostrar_llistat ? '' : 'hidden'; ?>">
                <form method="GET" action="">
                    <input type="hidden" name="action" value="llistar">
                    <label for="nivell"><?= htmlspecialchars(t('admin.filter_by_level')); ?></label>
                    <select name="nivell" id="nivell" onchange="this.form.submit();">
                        <option value="facil" <?php if (($nivell_seleccionat ?? '') === 'facil') echo 'selected'; ?>><?= htmlspecialchars(t('index.difficulty_facil')); ?></option>
                        <option value="normal" <?php if (($nivell_seleccionat ?? '') === 'normal') echo 'selected'; ?>><?= htmlspecialchars(t('index.difficulty_normal')); ?></option>
                        <option value="dificil" <?php if (($nivell_seleccionat ?? '') === 'dificil') echo 'selected'; ?>><?= htmlspecialchars(t('index.difficulty_dificil')); ?></option>
                    </select>
                </form>

                <!-- ✅ Mensajes ahora ARRIBA del listado -->
                <?php if (isset($_GET['msg'])): ?>
                    <?php
                    $msgs = [
                        'frase_eliminada' => t('admin.msgs.frase_eliminada'),
                        'error_datos' => t('admin.msgs.error_datos'),
                        'error_archivo_no_encontrado' => t('admin.msgs.error_archivo_no_encontrado'),
                        'error_permiso_escritura' => t('admin.msgs.error_permiso_escritura'),
                        'error_json' => t('admin.msgs.error_json'),
                        'error_frase_no_encontrada' => t('admin.msgs.error_frase_no_encontrada'),
                        'error_guardado' => t('admin.msgs.error_guardado')
                    ];

                    $msg_text = $msgs[$_GET['msg']] ?? "Error desconegut.";
                    $msg_class = ($_GET['msg'] === 'frase_eliminada') ? 'success' : 'error';

                    echo "<div class='$msg_class'>$msg_text</div>";
                    ?>
                <?php endif; ?>
                <!-- ✅ FIN zona mensajes -->

                <?php if (isset($error_msg)): ?>
                    <div class="error"><?php echo htmlspecialchars($error_msg); ?></div>
                <?php elseif (isset($listado_html)): ?>
                    <!-- ✅ Ahora la tabla + paginador aparecen debajo del mensaje -->
                    <?php echo $listado_html; ?>
                <?php endif; ?>
            </div>


            <script>
                document.addEventListener("DOMContentLoaded", () => {
                    const toggleBtn = document.getElementById("toggleLlistar");
                    const container = document.getElementById("llistarContainer");
                    const logoutLink = document.querySelector('a[href="logout.php"]');

                    const PT_ADMIN_LIST = <?= t_js('admin.list'); ?>;
                    const PT_ADMIN_HIDE = <?= t_js('admin.hide'); ?>;

                    const actualizarTextos = (visible) => {
                        toggleBtn.textContent = visible ? PT_ADMIN_HIDE : PT_ADMIN_LIST;
                        if (logoutLink) {
                            logoutLink.textContent = <?= t_js('admin.logout'); ?>;
                        }
                    };

                    const inicialmenteVisible = container && !container.classList.contains("hidden");
                    actualizarTextos(inicialmenteVisible);

                    toggleBtn.addEventListener("click", () => {
                        container.classList.toggle("hidden");
                        const visibleAhora = !container.classList.contains("hidden");
                        actualizarTextos(visibleAhora);

                        if (visibleAhora && !window.location.search.includes("action=llistar")) {
                            window.location.href = "?action=llistar&nivell=facil";
                        }
                    });

                    document.addEventListener("keydown", (e) => {
                        const key = e.key.toLowerCase();
                        const estaVisible = container && !container.classList.contains("hidden");

                        switch (key) {
                            case "l":
                                if (estaVisible) {
                                    window.location.href = "logout.php";
                                } else {
                                    toggleBtn?.click();
                                }
                                break;
                            case "o":
                                if (estaVisible) {
                                    toggleBtn?.click();
                                } else {
                                    window.location.href = "logout.php";
                                }
                                break;
                            case "a":
                                window.location.href = "create_sentence.php";
                                break;
                            case "t":
                                window.location.href = "index.php";
                                break;
                        }
                    });
                });
            </script>
        </div>
    </body>

</html>