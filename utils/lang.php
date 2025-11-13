<?php

function pt_supported_langs(): array {
    return ['es', 'ca', 'en'];
}

function pt_current_lang(): string {
    $default = 'es';
    if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], pt_supported_langs(), true)) {
        return $_SESSION['lang'];
    }
    // Detectar por cabecera si no hay sesión (opcional)
    $accept = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    if ($accept) {
        $accept = strtolower($accept);
        foreach (pt_supported_langs() as $l) {
            if (strpos($accept, $l) === 0 || strpos($accept, $l.'-') === 0) {
                $_SESSION['lang'] = $l;
                return $l;
            }
        }
    }
    $_SESSION['lang'] = $default;
    return $default;
}

function pt_load_messages(string $lang): array {
    static $cache = [];
    if (isset($cache[$lang])) return $cache[$lang];

    $base = __DIR__ . '/../lang';
    $file = $base . '/' . $lang . '.php';
    if (!is_file($file)) {
        // Fallback a español
        $file = $base . '/es.php';
    }
    $msgs = include $file;
    if (!is_array($msgs)) $msgs = [];

    // Mezclar con español como fallback de claves faltantes
    if ($lang !== 'es') {
        $es = include $base . '/es.php';
        if (is_array($es)) {
            $msgs = array_replace_recursive($es, $msgs);
        }
    }

    $cache[$lang] = $msgs;
    return $msgs;
}

function pt_get(array $arr, string $path) {
    $parts = explode('.', $path);
    $cur = $arr;
    foreach ($parts as $p) {
        if (!is_array($cur) || !array_key_exists($p, $cur)) return null;
        $cur = $cur[$p];
    }
    return $cur;
}

function t(string $key, array $params = []): string {
    $lang = pt_current_lang();
    $msgs = pt_load_messages($lang);
    $val = pt_get($msgs, $key);
    if ($val === null) {
        // Si no existe, devolver clave para detectar fácilmente
        return '[' . $key . ']';
    }
    if ($params) {
        foreach ($params as $k => $v) {
            $val = str_replace('{' . $k . '}', (string)$v, $val);
        }
    }
    return (string)$val;
}

function t_js(string $key, array $params = []): string {
    // Devuelve string seguro para JS
    return json_encode(t($key, $params), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function pt_label_with_hotkey(string $labelKey, string $hotkeyKey): string {
    $label = t($labelKey);
    $lang = pt_current_lang();
    $msgs = pt_load_messages($lang);
    $hot = strtolower($msgs['hotkeys'][$hotkeyKey] ?? '');
    if ($hot === '') {
        return htmlspecialchars($label);
    }
    // Buscar primera ocurrencia (case-insensitive)
    if (!function_exists('mb_stripos')) {
        $pos = stripos($label, $hot);
        $len = 1;
        if ($pos === false) return htmlspecialchars($label);
        return htmlspecialchars(substr($label, 0, $pos)) . '<span class="underline-letter">' . htmlspecialchars(substr($label, $pos, $len)) . '</span>' . htmlspecialchars(substr($label, $pos + $len));
    }
    $pos = mb_stripos($label, $hot, 0, 'UTF-8');
    if ($pos === false) return htmlspecialchars($label);
    $before = mb_substr($label, 0, $pos, 'UTF-8');
    $char = mb_substr($label, $pos, 1, 'UTF-8');
    $after = mb_substr($label, $pos + 1, null, 'UTF-8');
    return htmlspecialchars($before) . '<span class="underline-letter">' . htmlspecialchars($char) . '</span>' . htmlspecialchars($after);
}
