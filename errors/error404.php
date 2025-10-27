<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>404 - Pàgina no trobada</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container gameover-container">
        <h1>404</h1>
        <h2>¡Pàgina no trobada!</h2>
        <p>La ruta que cerques no existeix. Sembla que un Pokémon entremaliat se la va emportar.</p>
        <button onclick="location.href='index.php'">Tornar a l'inici</button>
        <button onclick="location.href='ranking.php'">Veure rànquing</button>
    </div>
</body>
</html>
