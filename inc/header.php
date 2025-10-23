<?php
global $SETTINGS, $CURRENT_PAGE, $CURRENT_LANG;
require_once __DIR__ . '/helpers.php';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($CURRENT_LANG) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Igor || Editor, Translator</title>
    <meta name="description" content="Igor Kuldmaa — translation, editing, scripting."><?php
      require_once __DIR__ . '/seo.php';
      $canonical = seo_canonical($CURRENT_PAGE, $CURRENT_LANG);
      if ($canonical) echo '<link rel="canonical" href="' . htmlspecialchars($canonical) . '">';
      render_hreflang_links($CURRENT_PAGE, $CURRENT_LANG);
      render_open_graph('Igor || Editor, Translator', 'Igor Kuldmaa — translation, editing, scripting.', $canonical);
      render_twitter_cards('Igor || Editor, Translator', 'Igor Kuldmaa — translation, editing, scripting.', $canonical);
      render_json_ld_person($CURRENT_LANG);
    ?>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💾</text></svg>">
    <link rel="stylesheet" href="./style.css">
    <style><?php emit_theme_css_vars(); ?><?php if ($bg = theme_var('background_image')): ?>
body{background-image: url('./img/<?= htmlspecialchars($bg) ?>'), linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);background-repeat: no-repeat, no-repeat;background-position: center, center;background-size: cover, cover;background-attachment: fixed, fixed;}<?php endif; ?></style><?php if ($bg = theme_var('background_image')): ?>
    <link rel="preload" as="image" href="./img/<?= htmlspecialchars($bg) ?>"><?php endif; ?>
<?php require __DIR__ . '/analytics.php'; ?>
</head>
<body data-ssr="1" class="page-<?= htmlspecialchars($CURRENT_PAGE) ?>">
        <!-- Fixed header with nav (left) and language (right) -->
        <div class="site-header">
            <?php require __DIR__ . '/nav.php'; ?>
            <div class="language-switcher">
            <a data-lang="ru" class="lang-btn<?= $CURRENT_LANG==='ru'?' active':'' ?>" href="?page=<?= urlencode($CURRENT_PAGE) ?>&lang=ru">RU</a>
            <a data-lang="et" class="lang-btn<?= $CURRENT_LANG==='et'?' active':'' ?>" href="?page=<?= urlencode($CURRENT_PAGE) ?>&lang=et">ET</a>
            <a data-lang="en" class="lang-btn<?= $CURRENT_LANG==='en'?' active':'' ?>" href="?page=<?= urlencode($CURRENT_PAGE) ?>&lang=en">EN</a>
            </div>
        </div>

    <div class="container">
        <div class="content">