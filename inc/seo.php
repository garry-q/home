<?php
// inc/seo.php
// Centralized SEO meta tags and structured data

function seo_title($page, $lang, $fallbackTitle = null) {
    global $SETTINGS;
    $siteName = isset($SETTINGS['site']['name']) ? $SETTINGS['site']['name'] : 'Site';
    $prefix = isset($SETTINGS['site']['title_prefix']) ? $SETTINGS['site']['title_prefix'] : '';
    $pageTitle = $fallbackTitle ?: ucfirst($page);
    return trim(($prefix ? "$prefix " : '') . $pageTitle . " — " . $siteName);
}

function seo_description($fallback = null) {
    global $SETTINGS;
    if (!empty($SETTINGS['site']['description'])) {
        return $SETTINGS['site']['description'];
    }
    return $fallback ?: '';
}

function seo_canonical($page, $lang) {
    global $SETTINGS;
    $base = rtrim($SETTINGS['site']['base_url'] ?? '', '/');
    if (!$base) return '';
    $query = http_build_query(['page' => $page, 'lang' => $lang]);
    return $base . '/index.php' . ($query ? ("?" . $query) : '');
}

function render_hreflang_links($page, $currentLang) {
    global $SETTINGS;
    // Use supported languages if present, else default to en/ru/et
    $langs = ['en','ru','et'];
    if (!empty($SETTINGS['supported_languages']) && is_array($SETTINGS['supported_languages'])) {
        $langs = $SETTINGS['supported_languages'];
    }
    $base = rtrim($SETTINGS['site']['base_url'] ?? '', '/');
    if (!$base) return;
    foreach ($langs as $lang) {
        $href = seo_canonical($page, $lang);
        echo '<link rel="alternate" hreflang="' . htmlspecialchars($lang) . '" href="' . htmlspecialchars($href) . '">';
    }
    // x-default
    echo '<link rel="alternate" hreflang="x-default" href="' . htmlspecialchars(seo_canonical($page, $currentLang)) . '">';
}

function render_open_graph($title, $description, $canonical) {
    global $SETTINGS;
    $siteName = htmlspecialchars($SETTINGS['site']['name'] ?? '');
    $ogImage = htmlspecialchars($SETTINGS['site']['og_image'] ?? ($SETTINGS['theme']['background_image'] ?? ''));
    echo '<meta property="og:title" content="' . htmlspecialchars($title) . '">';
    echo '<meta property="og:description" content="' . htmlspecialchars($description) . '">';
    echo '<meta property="og:type" content="website">';
    if ($canonical) echo '<meta property="og:url" content="' . htmlspecialchars($canonical) . '">';
    if ($ogImage) echo '<meta property="og:image" content="' . $ogImage . '">';
    if ($siteName) echo '<meta property="og:site_name" content="' . $siteName . '">';
}

function render_twitter_cards($title, $description, $canonical) {
    global $SETTINGS;
    $ogImage = htmlspecialchars($SETTINGS['site']['og_image'] ?? ($SETTINGS['theme']['background_image'] ?? ''));
    echo '<meta name="twitter:card" content="summary_large_image">';
    echo '<meta name="twitter:title" content="' . htmlspecialchars($title) . '">';
    echo '<meta name="twitter:description" content="' . htmlspecialchars($description) . '">';
    if ($canonical) echo '<meta name="twitter:url" content="' . htmlspecialchars($canonical) . '">';
    if ($ogImage) echo '<meta name="twitter:image" content="' . $ogImage . '">';
}

function render_json_ld_person($lang) {
    global $SETTINGS;
    $name = $SETTINGS['site']['person_name'] ?? 'Igor Kuldmaa';
    $base = rtrim($SETTINGS['site']['base_url'] ?? '', '/');
    $sameAs = [];
    if (!empty($SETTINGS['social_links'])) {
        foreach ($SETTINGS['social_links'] as $url) {
            if ($url) $sameAs[] = $url;
        }
    }
    $data = [
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => $name,
        'url' => $base ?: null,
        'sameAs' => $sameAs,
    ];
    echo '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}

function render_seo($page, $lang, $pageTitle = null, $pageDescription = null) {
    $title = seo_title($page, $lang, $pageTitle);
    $description = seo_description($pageDescription);
    $canonical = seo_canonical($page, $lang);
    echo '<title>' . htmlspecialchars($title) . "</title>\n";
    if ($description) echo '<meta name="description" content="' . htmlspecialchars($description) . '">' . "\n";
    if ($canonical) echo '<link rel="canonical" href="' . htmlspecialchars($canonical) . '">' . "\n";
    render_hreflang_links($page, $lang);
    render_open_graph($title, $description, $canonical);
    render_twitter_cards($title, $description, $canonical);
    render_json_ld_person($lang);
}
