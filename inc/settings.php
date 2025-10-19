<?php
// Load settings.json into PHP array
$settingsPath = __DIR__ . '/../settings.json';
$json = file_exists($settingsPath) ? file_get_contents($settingsPath) : '{}';
$SETTINGS = json_decode($json, true) ?: [];

// Defaults
if (!isset($SETTINGS['default_language'])) $SETTINGS['default_language'] = 'en';
if (!isset($SETTINGS['theme'])) $SETTINGS['theme'] = [];
if (!isset($SETTINGS['social_links'])) $SETTINGS['social_links'] = [];
if (!isset($SETTINGS['version'])) $SETTINGS['version'] = '';

// Helper: get theme var with fallback
function theme_var($key, $fallback = null) {
    global $SETTINGS;
    return $SETTINGS['theme'][$key] ?? $fallback;
}

// Helper: echo CSS variables from theme
function emit_theme_css_vars() {
    echo ':root{';
    $map = [
        'gradient_start' => '--gradient-start',
        'gradient_end'   => '--gradient-end',
        'accent'         => '--accent',
    ];
    foreach ($map as $k => $css) {
        $val = theme_var($k);
        if ($val) echo $css . ':' . $val . ';';
    }
    $angle = theme_var('arrow_angle_deg');
    if (is_numeric($angle)) echo '--arrow-angle-deg:' . intval($angle) . ';';
    $len = theme_var('arrow_length_vw');
    if (is_numeric($len)) echo '--arrow-length:' . intval($len) . 'vw;';
    $slope = $angle ? tan($angle * M_PI / 180) : 0.087;
    echo '--arrow-slope:' . $slope . ';';
    $wm = theme_var('widget_margin');
    if (is_numeric($wm)) echo '--widget-margin:' . intval($wm) . 'px;';
    $wh = theme_var('widget_height');
    if (is_numeric($wh)) echo '--widget-height:' . intval($wh) . 'px;';
    echo '}';
}