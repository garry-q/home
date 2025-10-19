<?php
function load_page_content($page){
    $path = __DIR__ . '/../content/' . basename($page) . '.json';
    if (!file_exists($path)) return [];
    $json = file_get_contents($path);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function localized($data, $field, $lang, $fallback = ''){
    if (!isset($data[$field])) return $fallback;
    $val = $data[$field];
    if (is_array($val)) {
        return $val[$lang] ?? (is_string($fallback) ? $fallback : '');
    }
    return $val; // non-localized scalar
}
?>