<?php
// ==========================
//   CARGA DE DATOS DEL JUGADOR
// ==========================
$name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : "Jugador";
$difficulty = isset($_GET['difficulty']) ? htmlspecialchars($_GET['difficulty']) : "facil";

// ==========================
//   CARGA DE FRASES DESDE JSON
// ==========================
$file = "frases.txt";
$frase = "No hi ha frases disponibles per aquest nivell.";

if (file_exists($file)) {
    $json = file_get_contents($file);
    $frases = json_decode($json, true);

    if (isset($frases[$difficulty]) && count($frases[$difficulty]) > 0) {
        $frase = $frases[$difficulty][array_rand($frases[$difficulty])];
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poketype - Joc</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Poketype</h1>
    <p>Benvingut, <strong><?php echo $name; ?></strong>!</p>
    <p>Dificultat seleccionada: <strong><?php echo ucfirst($difficulty); ?></strong></p>

    <div id="countdown">3</div>
    <div id="game-area" style="display:none;">
        <p id="frase"></p>
    </div>

    <a href="index.php" id="back-btn">⬅️ Tornar</a>

    <script>
        const frase = "<?php echo addslashes($frase); ?>";
    </script>
    <script src="dev9.js"></script>
    <script src="dev10.js"></script>
</body>
</html>
