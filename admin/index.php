<?php
session_name("admin_session");
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$mostrar_llistat = isset($_GET['action']) && $_GET['action'] === 'llistar';

// Obtengo la frase nueva  que viene de create_sentece para resaltar
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

        foreach ($frases[$nivell_seleccionat] as $index => $fraseObj) {
            $textoFrase = $fraseObj['texto']; // Extraer el texto
            //comparo si es la nueva frase y su nivel para resaltarla
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


        <!-- operador ternario para mostrar/ocultar el llistat -->
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

            <?php if (isset($error_msg)): ?>
                <div class="error"><?php echo htmlspecialchars($error_msg); ?></div>
            <?php elseif (isset($listado_html)): ?>
                <?php echo $listado_html; ?>
                
            <?php endif; ?>

            <?php if (isset($_GET['msg'])): ?>
                <?php
                $msgs = [ //array de mensajes con formato clave => valor
                    'frase_eliminada' => "Frase eliminada correctament.",
                    'error_datos' => "Error: dades incompletes per eliminar la frase.",
                    'error_archivo_no_encontrado' => "Error: fitxer de frases no trobat.",
                    'error_permiso_escritura' => "Error: sense permís d'escriptura al fitxer.",
                    'error_json' => "Error: fitxer de frases mal format.",
                    'error_frase_no_encontrada' => "Error: frase no trobada.",
                    'error_guardado' => "Error: no s'ha pogut guardar el fitxer."
                ];
                echo "<div>" . ($msgs[$_GET['msg']] ?? "Error desconegut.") . "</div>";
                ?>
            <?php endif; ?>
        </div>


        <script>
    document.addEventListener("DOMContentLoaded", () => {
        const toggleBtn = document.getElementById("toggleLlistar");
        const container = document.getElementById("llistarContainer");
        const logoutLink = document.querySelector('a[href="logout.php"]');

        // ✅ Actualiza texto del botón y del logout según visibilidad
        const actualizarTextos = (visible) => {
            // Botón listar/ocultar
            if (visible) {
                toggleBtn.innerHTML = '<span class="underline-letter">O</span>cultar frases';
            } else {
                toggleBtn.innerHTML = '<span class="underline-letter">L</span>listar frases';
            }

            // Enlace logout (subrayar O o L según estado)
            if (logoutLink) {
                if (visible) {
                    logoutLink.innerHTML = '<span class="underline-letter">L</span>ogout';
                } else {
                    logoutLink.innerHTML = 'L<span class="underline-letter">o</span>gout';
                }
            }
        };

        // Estado inicial
        const inicialmenteVisible = container && !container.classList.contains("hidden");
        actualizarTextos(inicialmenteVisible);

        // Acción del botón
        toggleBtn.addEventListener("click", () => {
            container.classList.toggle("hidden");
            const visibleAhora = !container.classList.contains("hidden");
            actualizarTextos(visibleAhora);

            if (visibleAhora && !window.location.search.includes("action=llistar")) {
                window.location.href = "?action=llistar&nivell=facil";
            }
        });

        // 🎹 Atajos de teclado
        document.addEventListener("keydown", (e) => {
            const key = e.key.toLowerCase();
            const estaVisible = container && !container.classList.contains("hidden");

            switch (key) {
                case "l":
                    if (estaVisible) {
                        // Si listado visible → L = Logout
                        window.location.href = "logout.php";
                    } else {
                        // Si listado oculto → L = Listar frases
                        toggleBtn?.click();
                    }
                    break;

                case "o":
                    if (estaVisible) {
                        // Si visible → O = Ocultar frases
                        toggleBtn?.click();
                    } else {
                        // Si oculto → O = Logout
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




</body>
</html>
