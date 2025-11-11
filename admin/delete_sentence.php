<?php
session_name("admin_session");
session_start();

require_once 'logger.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Obtener datos del POST
$nivell = $_POST['nivell'] ?? null;
$index  = isset($_POST['index']) ? intval($_POST['index']) : null;
$lang   = $_POST['lang'] ?? 'ca';

// Función de redirección con error
function redirigir_error($codigo, $nivell = 'facil') {
    header("Location: index.php?action=llistar&nivell=$nivell&msg=$codigo");
    exit;
}

// Validar datos
if (!$nivell || $index === null) {
    redirigir_error("error_datos");
}

// Determinar archivo según idioma
$archivo = match($lang) {
    'es' => '../frases_es.txt',
    'en' => '../frases_en.txt',
    default => '../frases_ca.txt'
};

// Validar archivo
if (!file_exists($archivo)) {
    redirigir_error("error_archivo_no_encontrado", $nivell);
}

if (!is_writable($archivo)) {
    redirigir_error("error_permiso_escritura", $nivell);
}

// Leer frases
$contenido = file_get_contents($archivo);
$frases = json_decode($contenido, true);

if ($frases === null) {
    redirigir_error("error_json", $nivell);
}

if (!isset($frases[$nivell][$index])) {
    redirigir_error("error_frase_no_encontrada", $nivell);
}

// Guardar info de la frase antes de eliminarla para el log
$fraseEliminada = $frases[$nivell][$index];
$textoFrase = $fraseEliminada['texto'] ?? $fraseEliminada;

// Eliminar frase
unset($frases[$nivell][$index]);
$frases[$nivell] = array_values($frases[$nivell]); // Reindexar

// Guardar cambios
if (file_put_contents($archivo, json_encode($frases, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    redirigir_error("error_guardado", $nivell);
}

// Registrar log
logAdmin("DELETE_SENTENCE", "delete_sentence.php", "Frase eliminada del nivel '$nivell' en '$lang': '$textoFrase'");

// Redirigir con mensaje de éxito
header("Location: index.php?action=llistar&nivell=$nivell&msg=frase_eliminada");
exit;
?>
