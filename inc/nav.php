<?php
global $CURRENT_PAGE, $CURRENT_LANG;
require_once __DIR__ . '/content.php';

// Default fallback
$fallback = [
  ['slug'=>'about','label'=>['en'=>'About','ru'=>'Обо мне','et'=>'Minust']],
  ['slug'=>'land','label'=>['en'=>'Land','ru'=>'Главная','et'=>'Avaleht']],
  ['slug'=>'projects','label'=>['en'=>'Pro','ru'=>'Проекты','et'=>'Projektid']],
  ['slug'=>'poems','label'=>['en'=>'Poems','ru'=>'Стихи','et'=>'Luule']],
  ['slug'=>'education','label'=>['en'=>'Edu','ru'=>'Образование','et'=>'Haridus']],
  ['slug'=>'support','label'=>['en'=>'Support','ru'=>'Поддержка','et'=>'Toeta']],
];

$navPath = __DIR__ . '/../content/navigation.json';
$nav = file_exists($navPath) ? json_decode(file_get_contents($navPath), true) : null;
$items = ($nav && !empty($nav['items']) && is_array($nav['items'])) ? $nav['items'] : $fallback;
?>
<nav class="site-nav" aria-label="Primary">
  <?php foreach ($items as $it): $slug=$it['slug']; $label=$it['label'][$CURRENT_LANG] ?? $it['label']['en'] ?? ucfirst($slug); ?>
    <a class="nav-link<?= $CURRENT_PAGE === $slug ? ' active' : '' ?>" href="?page=<?= urlencode($slug) ?>&lang=<?= urlencode($CURRENT_LANG) ?>"><?= htmlspecialchars($label) ?></a>
  <?php endforeach; ?>
</nav>
