<?php
global $SETTINGS;
$ga = $SETTINGS['analytics']['ga_id'] ?? '';
if ($ga): ?>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($ga) ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);} 
  gtag('js', new Date());
  gtag('config', '<?= htmlspecialchars($ga) ?>');
</script>
<?php endif; ?>