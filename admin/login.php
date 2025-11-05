<?php
session_name("admin_session");
session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';
$error_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['user'] ?? '');
    $pass = trim($_POST['pass'] ?? '');

    if ($user === '' || $pass === '') {
        $error = "Tots els camps són obligatoris.";
        $error_type = "empty";
    } else {
        $creds = file(__DIR__ . '/credentials.txt', FILE_IGNORE_NEW_LINES);
        if ($creds) {
            list($stored_user, $stored_pass) = explode(':', trim($creds[0]));

            if ($user !== $stored_user) {
                $error = "Usuari no trobat.";
                $error_type = "user";
            } elseif ($pass !== $stored_pass) {
                $error = "Contrasenya incorrecta.";
                $error_type = "pass";
            } else {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user'] = $user;
                session_regenerate_id(true);
                header("Location: index.php");
                exit;
            }
        } else {
            $error = "No s'han pogut llegir les credencials del servidor.";
            $error_type = "server";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ca">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Administrador</title>
        <link rel="stylesheet" href="../styles.css?<?php echo time(); ?>">
    </head>
    <body class="admin-page">
        <div class="admin-container">
            <img src="../media/piplu.gif" alt="Piplup" class="admin-gif" />
            <h1>Login Administrador</h1>
            <?php 
            if ($error) {
                echo "<p class='error $error_type'>$error</p>";
            }
            ?>
            <form method="post">
                <label>Usuari:</label>
                <input type="text" name="user" value="<?php echo htmlspecialchars($user ?? ''); ?>">
                <label>Contrasenya:</label>
                <input type="password" name="pass">
                <button type="submit">Entrar</button>
            </form>
        </div>
    </body>
    </html>
