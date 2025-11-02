<?php
session_name("admin_session");
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $creds = file(__DIR__ . '/credentials.txt', FILE_IGNORE_NEW_LINES);
    list($stored_user, $stored_pass) = explode(':', trim($creds[0]));

    $user = $_POST['user'];
    $pass = $_POST['pass'];

    if ($user === $stored_user && $pass === $stored_pass) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $user;
        header("Location: index.php");
        exit;
    } else {
        $error = "Usuari o contrasenya incorrectes.";
    }
}
?>

<!DOCTYPE html>
<html lang="ca">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Administrador</title>
    </head>
    <body>
        <h1>Login Administrador</h1>
        <?php if ($error) echo "<p style='color:red'>$error</p>"; ?>
        <form method="post">
            <label>Usuari:</label>
            <input type="text" name="user" required><br>
            <label>Contrasenya:</label>
            <input type="password" name="pass" required><br>
            <button type="submit">Entrar</button>
        </form>
    </body>
</html>
