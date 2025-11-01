<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <title>404 - Pàgina no trobada</title>
    <link rel="stylesheet" href="/styles.css">
</head>

<body>
    <div class="container gameover-container">
        <h2 class="fontError">404</h2>
        <h1 class="fontNameError">¡Pàgina no trobada!</h1>
        <p>La ruta que busques no existeix. Sembla que un Pokémon trapella se la va emportar.</p>
        <button onclick="location.href='/index.php'">Tornar a l'inici</button>
        <button onclick="location.href='/ranking.php'">Veure rànquing</button>
    </div>
</body>

</html>
