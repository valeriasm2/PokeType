<?php
session_start();
http_response_code(404);

// Detectar idioma según la sesión
$lang = $_SESSION['lang'] ?? 'ca';

// Cargar archivo de idioma
$lang_file = __DIR__ . "/../lang/{$lang}.php";
$langArray = file_exists($lang_file) ? include $lang_file : [];

// Textos de error desde el archivo de idioma
$errorTexts = $langArray['errors']['404'] ?? [];

// URLs de retorno absolutas
$homeUrl = '/index.php';
$rankingUrl = '/ranking.php';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($errorTexts['title'] ?? '404 - Pàgina no trobada') ?></title>
    <link rel="stylesheet" href="../styles.css?<?= time(); ?>">
</head>

<body>
    <div class="error-container">
        <h2 class="fontError">404</h2>
        <h1 class="fontNameError"><?= htmlspecialchars($errorTexts['title'] ?? 'Pàgina no trobada') ?></h1>
        <p><?= htmlspecialchars($errorTexts['msg1'] ?? 'La ruta que busques no existeix.') ?></p>

        <!-- Botón hacia index.php -->
        <!-- Botón hacia index.php -->
        <button onclick="location.href='<?= $homeUrl ?>'">
            <?=
            '<span class="underline-letter">' .
                htmlspecialchars(substr($errorTexts['btn1'] ?? "Tornar a l'inici", 0, 1)) .
                '</span>' .
                htmlspecialchars(substr($errorTexts['btn1'] ?? "Tornar a l'inici", 1))
            ?>
        </button>

        <!-- Botón hacia ranking.php -->
        <button onclick="location.href='<?= $rankingUrl ?>'">
            <?=
            '<span class="underline-letter">' .
                htmlspecialchars(substr($errorTexts['btn2'] ?? 'Veure rànquing', 0, 1)) .
                '</span>' .
                htmlspecialchars(substr($errorTexts['btn2'] ?? 'Veure rànquing', 1))
            ?>
        </button>

    </div>
</body>

</html>