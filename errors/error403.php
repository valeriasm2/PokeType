<?php
session_start();
http_response_code(403);

// Detectar idioma según la sesión
$lang = $_SESSION['lang'] ?? 'ca';

// Cargar archivo de idioma
$lang_file = __DIR__ . "/../lang/{$lang}.php";
$langArray = file_exists($lang_file) ? include $lang_file : [];

// Textos de error desde el archivo de idioma
$errorTexts = $langArray['errors']['403'] ?? [];

// Determinar URL de retorno
$homeUrl = (session_name() === 'admin_session') ? '../admin/index.php' : '../index.php';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($errorTexts['title'] ?? '403 - Accés Prohibit') ?></title>
    <link rel="stylesheet" href="../styles.css?<?= time(); ?>">
</head>
<body>
    <div class="error-container">
        <h2 class="fontError">403</h2>
        <h1 class="fontNameError"><?= htmlspecialchars($errorTexts['title'] ?? 'Accés Prohibit') ?></h1>
        <p><?= htmlspecialchars($errorTexts['msg1'] ?? 'No pots visitar directament aquesta pàgina.') ?></p>
        <p><?= htmlspecialchars($errorTexts['msg2'] ?? 'Un Pokémon guardià bloqueja el pas.') ?></p>
        <button onclick="location.href='<?= $homeUrl ?>'">
            <span class="underline-letter"><?= htmlspecialchars(substr($errorTexts['btn'] ?? 'Tornar a l\'inici', 0, 1)) ?></span>
            <?= htmlspecialchars(substr($errorTexts['btn'] ?? 'Tornar a l\'inici', 1)) ?>
        </button>
    </div>
</body>
</html>
