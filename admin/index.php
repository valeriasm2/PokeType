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
        die("Error al leer o decodificar el archivo de frases");
    }

    $nivell_seleccionat = $_GET['nivell'] ?? 'facil';
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
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Panell d'Administrador</title>
     <link rel="stylesheet" href="<?php echo time(); ?>"> 
</head>
<body>
    <p>
        Benvingut, <strong><?php echo htmlspecialchars($_SESSION['admin_user']); ?></strong> | 
        <a href="logout.php">Logout</a>
    </p>

    <h1>Panell d’Administrador</h1>

    <!-- Opcions del panell -->
    <ul>
        <li><a href="index.php?action=llistar">Llistar frases (per nivells de dificultat)</a></li>
        <li><a href="#">Afegir frase</a></li>
    </ul>

    <!-- Bloque de listado, solo visible si se pulsa Llistar -->
    <?php if ($mostrar_llistat): ?>
        <div class="llistarfrases-container">
            <form method="GET" action="">
                <input type="hidden" name="action" value="llistar">
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
    <?php endif; ?>
</body>
</html>
