<?php
session_start();

require_once 'admin/logger.php';
$name = $_SESSION['name'];

// Log logout de jugador
logJuego("PLAYER_LOGOUT", "destroy_session.php", "Usuario '$name' cerró sesión del juego");

session_destroy();
header("Location: index.php");
exit();
?>