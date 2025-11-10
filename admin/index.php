<?php
session_name("admin_session");
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$mostrar_llistat = isset($_GET['action']) && $_GET['action'] === 'llistar';

// Obtengo la frase nueva que viene de create_sentence para resaltar
$nuevaFrase = $_SESSION['ultima_frase'] ?? null;
$nivellUltim = $_SESSION['ultim_nivell'] ?? null;

if ($mostrar_llistat) {
    $archivo = '../frases.txt';
    $contenido = file_get_contents($archivo);
    $frases = json_decode($contenido, true);

    if ($frases === null) {
        $error_msg = "Error al llegir o decodificar el fitxer de frases.";
    } else {
        $nivell_seleccionat = $_GET['nivell'] ?? 'facil';
        if (!isset($frases[$nivell_seleccionat])) {
            $nivell_seleccionat = 'facil';
        }

        $listado_html = '<table>';
        $listado_html .= '<thead><tr><th>Frase</th><th>Esborra</th></tr></thead><tbody>';

        // --- Paginació ---
        $por_pagina = 25;
        $total_frases = count($frases[$nivell_seleccionat]);
        $total_paginas = ceil($total_frases / $por_pagina);

        $pagina_actual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;

        // Calculem l’índex d’inici i final
        $inicio = ($pagina_actual - 1) * $por_pagina;
        $frases_paginadas = array_slice($frases[$nivell_seleccionat], $inicio, $por_pagina);

        foreach ($frases_paginadas as $i => $fraseObj) {
            $index = $inicio + $i;
            $textoFrase = $fraseObj['texto'];

            // Comparo si és la nova frase i el seu nivell per ressaltar-la
            $es_nueva = ($nuevaFrase !== null && $textoFrase === $nuevaFrase && $nivell_seleccionat === $nivellUltim);

            $listado_html .= '<tr' . ($es_nueva ? ' class="highlight"' : '') . '>';
            $listado_html .= '<td>' . htmlspecialchars($textoFrase) . '</td>';
            $listado_html .= '<td>
                <form method="POST" action="delete_sentence.php" onsubmit="return confirm(\'Segur que vols eliminar aquesta frase?\');">
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
                $listado_html .= '<a href="?action=llistar&nivell=' . htmlspecialchars($nivell_seleccionat) . '&pagina=' . $prev . '">&laquo; Anterior</a>';
            }

            $listado_html .= "<span>Pàgina $pagina_actual de $total_paginas</span>";

            if ($pagina_actual < $total_paginas) {
                $next = $pagina_actual + 1;
                $listado_html .= '<a href="?action=llistar&nivell=' . htmlspecialchars($nivell_seleccionat) . '&pagina=' . $next . '">Següent &raquo;</a>';
            }

            $listado_html .= '</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
    <head>
        <meta charset="UTF-8">
        <title>Panell d'Administrador</title>
        <link rel="stylesheet" href="../styles.css?<?php echo time(); ?>">
    </head>

    <body class="admin-page-index">


        <div class="admin-container-index">
            <p>Benvingut, <strong><?php echo htmlspecialchars($_SESSION['admin_user']); ?></strong></p>

            <h1>Panell d’Administrador</h1>

            <button id="toggleLlistar" type="button">
                <span class="underline-letter">L</span>listar frases
            </button>

            <a href="create_sentence.php" class="admin-btn">
                <span class="underline-letter">A</span>fegir frase
            </a>

            <a href="logout.php" class="admin-btn">
                L<span class="underline-letter">o</span>gout
            </a>

            <div id="llistarContainer" class="<?php echo $mostrar_llistat ? '' : 'hidden'; ?>">
                <form method="GET" action="">
                    <input type="hidden" name="action" value="llistar">
                    <label for="nivell">Mostra segons nivell de dificultat:</label>
                    <select name="nivell" id="nivell" onchange="this.form.submit();">
                        <option value="facil" <?php if (($nivell_seleccionat ?? '') === 'facil') echo 'selected'; ?>>Fàcil</option>
                        <option value="normal" <?php if (($nivell_seleccionat ?? '') === 'normal') echo 'selected'; ?>>Normal</option>
                        <option value="dificil" <?php if (($nivell_seleccionat ?? '') === 'dificil') echo 'selected'; ?>>Difícil</option>
                    </select>
                </form>

                <!-- ✅ Mensajes ahora ARRIBA del listado -->
                <?php if (isset($_GET['msg'])): ?>
                    <?php
                    $msgs = [
                        'frase_eliminada' => "Frase eliminada correctament.",
                        'error_datos' => "Error: dades incompletes per eliminar la frase.",
                        'error_archivo_no_encontrado' => "Error: fitxer de frases no trobat.",
                        'error_permiso_escritura' => "Error: sense permís d'escriptura al fitxer.",
                        'error_json' => "Error: fitxer de frases mal format.",
                        'error_frase_no_encontrada' => "Error: frase no trobada.",
                        'error_guardado' => "Error: no s'ha pogut guardar el fitxer."
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

                    const actualizarTextos = (visible) => {
                        if (visible) {
                            toggleBtn.innerHTML = '<span class="underline-letter">O</span>cultar frases';
                        } else {
                            toggleBtn.innerHTML = '<span class="underline-letter">L</span>listar frases';
                        }
                        if (logoutLink) {
                            if (visible) {
                                logoutLink.innerHTML = '<span class="underline-letter">L</span>ogout';
                            } else {
                                logoutLink.innerHTML = 'L<span class="underline-letter">o</span>gout';
                            }
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