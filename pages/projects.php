<?php
global $SETTINGS, $CURRENT_LANG;
require_once __DIR__ . '/../inc/content.php';
$data = load_page_content('projects');
$title = localized($data, 'title', $CURRENT_LANG, 'Projects');
$body = localized($data, 'body_html', $CURRENT_LANG, '');
?>
<div class="main-text">
  <h2><?= htmlspecialchars($title) ?></h2>
  <div class="body-text"><?php echo $body; ?></div>
</div>