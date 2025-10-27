<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poketype</title>
</head>

<style>
    .error-alert {
        color: red;
        font-weight: bold;
    }
    
</style>

<body>
    <?php
    function mostrarError($error) {
        if (!empty($error)) {
            echo '<div class="error-alert">' . $error . '</div>';
            echo "<script>document.getElementById('name').focus();</script>";
        }
    }
    ?>
    
    <?php
    $error = "";
    $name = "";
    $dificultad = "";

    if ($_POST) {
        $name = trim($_POST['name']);

        if (empty($name)){
            $error = "el camp nom no pot estar buit";
            
        }
        else{
            $dificultad = $_POST['difficulty'];  
            header("Location: play.php?name=" . urlencode($name) . "&difficulty=" . urlencode($dificultad));
            exit();
        }
        }

    ?>
    <h1>Poketype</h1>
    <p>Benvingut a Poketype! Un joc per aprendre els tipus de especies Pokémon i les seves regions.</p>
    <img src="media/mew.png" alt="Mew Image" width="300">
    <form action="index.php" method="post">

        <label for="name">Nom:</label>
        <!-- html specialchars es por seeguidad, para que no puedan meter codigo malicioso -->
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>"><br>
        
        <?php mostrarError($error); ?>
        <br>
        
        <label for="difficultad">Dificultat:</label>
        <select name="difficulty" id="difficultad">

        <option value="facil">Fàcil</option>
        <option value="normal">Normal</option>
        <option value="dificil">Difícil</option>

        </select><br><br>
        <!-- El boton jugar esta desabilitado hasta que se detecta que JS esta activo -->
        <button type="submit" id="play-button" disabled>Jugar</button>
        
        <script>
            //habilitar el boto jugar si JS està actiu
            document.getElementById('play-button').disabled = false;
        </script>

    </form>

</body>
</html>