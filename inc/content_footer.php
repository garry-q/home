<?php
global $SETTINGS;
require_once __DIR__ . '/helpers.php';
?>
<div class="content-footer-global" role="contentinfo">
  <div class="content-footer-inner">
    <div class="social-links">
      <?php foreach ($SETTINGS['social_links'] as $key => $url): ?>
        <a href="<?= htmlspecialchars($url) ?>" class="social-btn <?= htmlspecialchars($key) ?>" title="<?= htmlspecialchars(ucfirst($key)) ?>" target="_blank" rel="noopener" aria-label="<?= htmlspecialchars($key) ?>"><?= social_svg($key) ?></a>
      <?php endforeach; ?>
    </div>
    <?php if (!empty($SETTINGS['version'])): ?>
      <div class="footer-version">v<?= htmlspecialchars($SETTINGS['version']) ?></div>
    <?php endif; ?>
  </div>
</div>
