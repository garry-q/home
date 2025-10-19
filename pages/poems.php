<?php
global $SETTINGS, $CURRENT_LANG;
require_once __DIR__ . '/../inc/content.php';
require_once __DIR__ . '/../inc/poems_parser.php';

$data = load_page_content('poems');
$title = localized($data, 'title', $CURRENT_LANG, 'Poems');

$poemsFile = __DIR__ . '/../content/poems.txt';
$poems = parse_poems($poemsFile);
$alphabetIndex = get_alphabet_index($poems);
?>
<div class="poems-container">
  <aside class="poems-nav">
    <h3><?= htmlspecialchars($title) ?></h3>
    <div class="poems-list">
      <?php foreach ($alphabetIndex as $letter => $letterPoems): ?>
        <div class="alphabet-group-title"><?= htmlspecialchars($letter) ?></div>
        <?php foreach ($letterPoems as $poem): ?>
          <a href="#<?= htmlspecialchars($poem['slug']) ?>" class="poem-title-link"><?= htmlspecialchars($poem['title']) ?></a>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </div>
  </aside>
  
  <main class="poems-timeline">
    <?php foreach ($poems as $poem): ?>
    
    <article id="<?= htmlspecialchars($poem['slug']) ?>" class="poem-item">
      <header class="poem-header">
        <h2 class="poem-title"><?= htmlspecialchars($poem['title']) ?></h2>
        <time class="poem-date" datetime="<?= htmlspecialchars($poem['date']) ?>"><?= htmlspecialchars($poem['date']) ?></time>
      </header>
      <div class="poem-body"><?= nl2br(htmlspecialchars($poem['body'])) ?></div>
    </article>
    <?php endforeach; ?>
    
    <?php if (empty($poems)): ?>
      <p>No poems yet.</p>
    <?php endif; ?>
  </main>
</div>

<script>
// Scroll to newest poem (first in list) on load
document.addEventListener('DOMContentLoaded', () => {
  const firstPoem = document.querySelector('.poem-item');
  if (firstPoem) {
    firstPoem.scrollIntoView({ behavior: 'instant', block: 'start' });
  }
  
  // Smooth scroll for anchor clicks
  document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const href = e.currentTarget.getAttribute('href');
      const target = document.querySelector(href);
      if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
});
</script>