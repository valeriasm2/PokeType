<?php
http_response_code(403);
?>
<!DOCTYPE html>
<html lang="ca">
    <head>
        <meta charset="UTF-8">
        <title>403 - Accés Prohibit</title>
        <link rel="stylesheet" href="../styles.css?<?php echo time(); ?>">
    </head>
    <body>
        <div class="error-container">
            <h2 class="fontError">403</h2>
            <h1 class="fontNameError">¡Accés Prohibit!</h1>
            <p>No pots visitar directament aquesta pàgina.</p>
            <p>Un Pokémon guardià bloqueja el pas per protegir el joc.</p>
            <button onclick="location.href='/index.php'">Tornar a l'inici</button>
        </div>
    </body>
</html>
