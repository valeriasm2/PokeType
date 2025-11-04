<?php
session_name("admin_session");
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['admin_logged_in'])  $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$nivell = $_POST['nivell'] ?? null;
$index = isset($_POST['index']) ? intval($_POST['index']) : null;

function redirigir_error($codigo, $nivell = 'facil') {
    header("Location: index.php?action=llistar&nivell=$nivell&msg=$codigo");
    exit;
}

if (!$nivell || $index === null) {
>>>>>>> 6346943 (admin index mejorado)
    redirigir_error("error_datos");
}

$archivo = '../frases.txt';

if (!file_exists($archivo)) {
    redirigir_error("error_archivo_no_encontrado", $nivell);
}

if (!is_writable($archivo)) {
    redirigir_error("error_permiso_escritura", $nivell);
}

$contenido = file_get_contents($archivo);
$frases = json_decode($contenido, true);

if ($frases === null) {
    redirigir_error("error_json", $nivell);
}

if (!isset($frases[$nivell][$index])) {
    redirigir_error("error_frase_no_encontrada", $nivell);
}

unset($frases[$nivell][$index]);
$frases[$nivell] = array_values($frases[$nivell]);

if (file_put_contents($archivo, json_encode($frases, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    redirigir_error("error_guardado", $nivell);
}

header("Location: index.php?action=llistar&nivell=$nivell&msg=frase_eliminada");
exit;
?>