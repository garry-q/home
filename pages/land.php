<?php
global $SETTINGS, $CURRENT_LANG;
require_once __DIR__ . '/../inc/content.php';
$data = load_page_content('land');
$intro = localized($data, 'title', $CURRENT_LANG, "I'm Igor, I'm a translator.");
$body = localized($data, 'body_html', $CURRENT_LANG, "");
$image = localized($data, 'image', $CURRENT_LANG, 'missing_en.png');
?>
<div class="main-text">
  <div class="first-paragraph">
  <span class="intro-text" id="intro-text"><?= htmlspecialchars($intro) ?></span>
  </div>
  <div class="static-image">
  <img src="./img/<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars(strtoupper($CURRENT_LANG)) ?>" id="main-image">
  </div>
  <div class="body-text" id="body-text"><?php echo $body; ?></div>
</div>