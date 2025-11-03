<?php
session_name("admin_session");
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
//     header("Location: login.php");
//     exit;
// }

$nivell = $_POST['nivell'] ?? null;
$index = isset($_POST['index']) ? intval($_POST['index']) : null;

if (!$nivell || $index === null) {
    die("Datos incompletos para eliminar frase.");
}

$archivo = '../frases.txt';

if (!file_exists($archivo)) {
    die("Archivo no encontrado.");
}

if (!is_writable($archivo)) {
    die("Archivo no tiene permisos de escritura.");
}

$contenido = file_get_contents($archivo);
$frases = json_decode($contenido, true);

if ($frases === null) {
    die("Error al decodificar frases.txt: " . json_last_error_msg());
}

if (!isset($frases[$nivell][$index])) {
    die("Frase no encontrada o datos incorrectos.");
}

unset($frases[$nivell][$index]);

$frases[$nivell] = array_values($frases[$nivell]);

if (file_put_contents($archivo, json_encode($frases, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    die("Error al guardar frases.");
}

header("Location: llistar_frases_test.php?nivell=$nivell&msg=frase_eliminada");
exit;
?>
