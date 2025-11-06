<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="ca">        
    <head>
        <meta charset="UTF-8">
        <title>404 - Pàgina no trobada</title>
        <link rel="stylesheet" href="../styles.css?<?php echo time(); ?>">
    </head>

    <body>
        <div class="error-container">
            <h2 class="fontError">404</h2>
            <h1 class="fontNameError">¡Pàgina no trobada!</h1>
            <p>La ruta que busques no existeix. Sembla que un Pokémon trapella se la va emportar.</p>
            <button onclick="location.href='/index.php'">
                <span class="underline-letter">T</span>ornar a l'inici
            </button>

            <button onclick="location.href='/ranking.php'">
                <span class="underline-letter">V</span>eure rànquing
            </button>
            </div>

            <script>
            document.addEventListener("keydown", (e) => {
                const key = e.key.toLowerCase();
                if (key === "t") {
                window.location.href = "/index.php";
                } else if (key === "v") {
                window.location.href = "/ranking.php";
                }
            });
            </script>
        </div>
    </body>
</html>
