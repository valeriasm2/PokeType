<?php
session_name("admin_session");
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$mostrar_llistat = isset($_GET['action']) && $_GET['action'] === 'llistar';

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

        foreach ($frases[$nivell_seleccionat] as $index => $frase) {
            $listado_html .= '<tr>';
            $listado_html .= '<td>' . htmlspecialchars($frase) . '</td>';
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
    const logoutLink = document.querySelector('.admin-btn[href="logout.php"]');

    // 🔹 Función para actualizar el texto del botón listar/ocultar
    const actualizarTexto = (mostrando) => {
        if (mostrando) {
            toggleBtn.innerHTML = '<span class="underline-letter">O</span>cultar frases';
        } else {
            toggleBtn.innerHTML = '<span class="underline-letter">L</span>listar frases';
        }
    };

    // 🔹 Función para actualizar el subrayado del logout según el estado
    const actualizarLogout = (mostrando) => {
        if (mostrando) {
            logoutLink.innerHTML = '<span class="underline-letter">L</span>ogout';
        } else {
            logoutLink.innerHTML = 'L<span class="underline-letter">o</span>gout';
        }
    };

    if (toggleBtn && container && logoutLink) {
        const esMostrado = !container.classList.contains("hidden");
        actualizarTexto(esMostrado);
        actualizarLogout(esMostrado);

        toggleBtn.addEventListener("click", () => {
            const estaOculto = container.classList.contains("hidden");

            if (estaOculto && !esMostrado) {
                window.location.href = "?action=llistar&nivell=facil";
                return;
            }

            container.classList.toggle("hidden");
            const visibleAhora = !container.classList.contains("hidden");

            actualizarTexto(visibleAhora);
            actualizarLogout(visibleAhora);
        });
    }

    // 🔹 Teclas de acceso rápido dinámicas
    document.addEventListener("keydown", (e) => {
        const key = e.key.toLowerCase();
        const estaVisible = !container.classList.contains("hidden");

        switch (key) {
            case "l":
                if (estaVisible) {
                    window.location.href = "logout.php";
                } else {
                    toggleBtn?.click();
                }
                break;
            case "a":
                window.location.href = "create_sentence.php";
                break;
            case "t":
                window.location.href = "index.php";
                break;
            case "o":
                if (estaVisible) toggleBtn?.click();
                break;
        }
    });
});
</script>




</body>
</html>
