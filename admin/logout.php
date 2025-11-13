<?php
session_name("admin_session");
session_start();

// Incluir sistema de logs
require_once 'logger.php';

// Log logout antes de destruir la sesión
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    logAdmin("LOGOUT", "logout.php", "Admin cerró sesión");
}

session_destroy();
header("Location: login.php");
exit;
