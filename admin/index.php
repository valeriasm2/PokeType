<?php
session_name("admin_session");
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Detectamos si el usuario ha hecho clic en "Llistar frases"
$mostrar_llistat = isset($_GET['action']) && $_GET['action'] === 'llistar';

// Si se pide listar, cargamos las frases
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
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const toggleBtn = document.getElementById("toggleLlistar");
            const container = document.getElementById("llistarContainer");

            if (toggleBtn && container) {
                toggleBtn.addEventListener("click", () => {
                    if (container.innerHTML.trim() === "") {
                        fetch("?action=llistar")
                            .then(response => response.text())
                            .then(html => {
                                const temp = document.createElement("div");
                                temp.innerHTML = html;
                                const newContent = temp.querySelector("#llistarContainer");
                                if (newContent) {
                                    container.innerHTML = newContent.innerHTML;
                                }
                            });
                    } else {
                        container.innerHTML = "";
                    }
                });
            }
        });
    </script>
</head>

<body>
    <p>
        Benvingut, <strong><?php echo htmlspecialchars($_SESSION['admin_user']); ?></strong> |
        <a href="logout.php">Logout</a>
    </p>

    <h1>Panell d’Administrador</h1>

    <ul>
        <li><button id="toggleLlistar" type="button">Llistar frases</button></li>
        <li><button><a href="create_sentence.php">Afegir frase</a></button></li>
    </ul>

    <div id="llistarContainer">
        <?php if ($mostrar_llistat): ?>
            <form method="GET" action="">
                <input type="hidden" name="action" value="llistar">
                <label for="nivell">Mostra segons nivell de dificultat:</label>
                <select name="nivell" id="nivell" onchange="this.form.submit()">
                    <option value="facil" <?php if ($nivell_seleccionat === 'facil') echo 'selected'; ?>>Fàcil</option>
                    <option value="normal" <?php if ($nivell_seleccionat === 'normal') echo 'selected'; ?>>Normal</option>
                    <option value="dificil" <?php if ($nivell_seleccionat === 'dificil') echo 'selected'; ?>>Difícil</option>
                </select>
            </form>

            <?php if (isset($error_msg)): ?>
                <div><?php echo htmlspecialchars($error_msg); ?></div>
            <?php elseif (isset($listado_html)): ?>
                <?php echo $listado_html; ?>
            <?php endif; ?>

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
                echo "<div>" . ($msgs[$_GET['msg']] ?? "Error desconegut.") . "</div>";
                ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>

</html>