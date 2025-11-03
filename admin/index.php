<?php
session_name("admin_session");
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ca">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Panell d'Administrador</title>
    </head>
    <body>
        <p>Benvingut, <strong><?php echo htmlspecialchars($_SESSION['admin_user']); ?></strong> | 
        <a href="logout.php">Logout</a></p>

        <h1>Panell d’Administrador</h1>
        <ul>
            <li><a href="#">Llistar frases (per nivells de dificultat)</a></li>
            <li><a href="#">Afegir frase</a></li>
        </ul>
    </body>
</html>
