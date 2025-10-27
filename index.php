
<!-- 6.
Quan accedim a la pàgina index.php el servidor retornarà un html amb:
- Títol de la aplicació

-Descripció del joc + image

-Input per introduir el nom

-Select per seleccionar el nivell de dificultat, hi ha tres, per exemple: fàcil, normal i difícil. Es poden adaptar segons la temática.

-Botó per iniciar el joc -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poketype</title>
</head>
<body>
    <h1>Poketype</h1>
    <p>Benvingut a Poketype! Un joc per aprendre els tipus de especies Pokémon i les seves regions.</p>
    <img src="media/mew.png" alt="Pokémon Image" width="300">
    <form action="game.php" method="post">
        <label for="name">Nom:</label>
        <input type="text" id="name" name="name"><br><br>
    </form>

    <select name="difficulty" id="difficultad">
        <option value="facil">Fàcil</option>
        <option value="normal">Normal</option>
        <option value="dificil">Difícil</option>
    </select><br><br>
    <button type="submit">Iniciar el joc</button>


    
</body>
</html>