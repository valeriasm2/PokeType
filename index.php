<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poketype</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div id="index-container">
        <?php
        function mostrarError($error) {
            if (!empty($error)) {
                echo '<div class="error-alert">' . $error . '</div>';
                echo "<script>document.getElementById('name').focus();</script>";
            }
        }

        $error = "";
        $name = "";
        $dificultad = "";

        if ($_POST) {
            $name = trim($_POST['name']);

            if (empty($name)) {
                $error = "⚠️ El camp nom no pot estar buit";
            } else {
                $dificultad = $_POST['difficulty'];
                header("Location: play.php?name=" . urlencode($name) . "&difficulty=" . urlencode($dificultad));
                exit();
            }
        }
        ?>

        <h1>Poketype</h1>
        <p>Benvingut a Poketype! Un joc per aprendre els tipus de Pokémon i millorar la teva velocitat d’escriptura.</p>
        <img src="https://images.steamusercontent.com/ugc/156901856787352192/57490018F1B024CC09706D3106251C92A4E5B5F0/?imw=5000&imh=5000&ima=fit&impolicy=Letterbox&imcolor=%23000000&letterbox=false" alt="Mew GIF" width="300">


        <form action="index.php" method="post">
            <label for="name">Nom:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>"><br>
            <?php mostrarError($error); ?>
            <br>

            <label for="dificultat">Dificultat:</label>
            <select name="difficulty" id="dificultat">
                <option value="facil">Fàcil</option>
                <option value="normal">Normal</option>
                <option value="dificil">Difícil</option>
            </select><br><br>

            <button type="submit" id="play-button" disabled>Jugar</button>

            <script>
                document.getElementById('play-button').disabled = false;
            </script>

            <noscript>
                <div class="error-alert">
                    ⚠️ Aquest joc necessita JavaScript per funcionar. Si us plau, habilita JavaScript al teu navegador. ⚠️
                </div>
            </noscript>
        </form>
    </div>
</body>
</html>