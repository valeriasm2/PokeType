<?php
/**
 * Sistema de Logs para PokeType
 * Registra todas las acciones importantes del sistema
 */

/**
 * Escribe un log en el archivo logs.txt
 * esto son ejemplos de uso:
 * escribirLog("LOGIN_SUCCESS", "login.php", "Admin logueado correctamente");
 * @param string $accion - Tipo de acción (LOGIN, CREATE_SENTENCE, etc.)
 * @param string $archivo - Archivo donde se genera el log
 * @param string $mensaje - Mensaje descriptivo de la acción
 */

function escribirLog($accion, $archivo, $mensaje) {
    $fecha = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    // mostrará la [fecha] [IP] [archivo] acción: mensaje
    $linea_log = "[$fecha] [$ip] [$archivo] $accion: $mensaje\n";
    
    //esto es la ruta del archivo de logs
    $ruta_logs = __DIR__ . '/logs.txt';
    file_put_contents($ruta_logs, $linea_log, FILE_APPEND | LOCK_EX);
}

/*Este se encarga de los logs del admin
 */
function logAdmin($accion, $archivo, $mensaje) {
    $usuario = $_SESSION['admin_user'] ?? 'unknown';
    escribirLog($accion, $archivo, "$mensaje (Usuario: $usuario)");
}

/*este se encarga de los logs del juego
 */
function logJuego($accion, $archivo, $mensaje) {
    escribirLog($accion, $archivo, $mensaje);
}
?>
