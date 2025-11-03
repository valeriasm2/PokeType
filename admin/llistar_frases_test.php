<?php
session_name("admin_session");
session_start();

// comentado por ahora para facilitar las pruebas
// if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
//     header("Location: login.php");
//     exit;
// }

$archivo = '../frases.txt';
$contenido = file_get_contents($archivo);
$frases = json_decode($contenido, true);

if ($frases === null) {
    die("Error al leer o decodificar el archivo de frases");
}

// Obtenemos el nivel seleccionado via GET, por defecto 'facil'
$nivell_seleccionat = $_GET['nivell'] ?? 'facil';

// Validar que el nivel existe
if (!isset($frases[$nivell_seleccionat])) {
    $nivell_seleccionat = 'facil';
}

$listado_html = '<table class="frases-table">';
$listado_html .= '<thead><tr><th>Frase</th><th>Esborra</th></tr></thead><tbody>';

foreach ($frases[$nivell_seleccionat] as $index => $frase) {
    $listado_html .= '<tr>';
    $listado_html .= '<td>' . htmlspecialchars($frase) . '</td>';
    $listado_html .= '<td class="accion-cell">
        <form method="POST" action="delete_sentence.php" onsubmit="return confirm(\'Segur que vols eliminar aquesta frase?\');" class="form-eliminar">
            <input type="hidden" name="nivell" value="' . htmlspecialchars($nivell_seleccionat) . '">
            <input type="hidden" name="index" value="' . $index . '">
            <button type="submit" aria-label="Esborra">X</button>
        </form>
    </td>';
    $listado_html .= '</tr>';
}

$listado_html .= '</tbody></table>';


?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8" />
    <title>Llistar Frases Admin</title>
    <link rel="stylesheet" href="../styles.css?<?php echo time(); ?>">
</head>
<body>
    <div class="llistarfrases-container">
        <form method="GET" action="">
            <label for="nivell">Mostra segons nivell de dificultat:</label>
            <select name="nivell" id="nivell" onchange="this.form.submit()">
                <option value="facil" <?php if ($nivell_seleccionat === 'facil') echo 'selected'; ?>>Fàcil</option>
                <option value="normal" <?php if ($nivell_seleccionat === 'normal') echo 'selected'; ?>>Normal</option>
                <option value="dificil" <?php if ($nivell_seleccionat === 'dificil') echo 'selected'; ?>>Difícil</option>
            </select>
        </form>

        <?php echo $listado_html; ?>
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'frase_eliminada'): ?>
            <div class="alert success">Frase eliminada correctament.</div>
        <?php endif; ?>
        </div>
</body>
</html>

