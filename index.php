<?php
// Simple router: ?page=land&lang=en
require_once __DIR__ . '/inc/settings.php';

$allowedPages = ['land','about','education','projects','poems','support'];
$page = isset($_GET['page']) && in_array($_GET['page'], $allowedPages, true) ? $_GET['page'] : 'land';
$lang = isset($_GET['lang']) && in_array($_GET['lang'], ['en','ru','et'], true) ? $_GET['lang'] : $SETTINGS['default_language'];

// expose for includes
$CURRENT_PAGE = $page;
$CURRENT_LANG = $lang;

require __DIR__ . '/inc/header.php';
require __DIR__ . '/pages/' . $page . '.php';
require __DIR__ . '/inc/footer.php';