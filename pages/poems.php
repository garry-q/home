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
  <time class="poem-date" datetime="<?= htmlspecialchars($poem['dateISO'] ?? '') ?>"><?= htmlspecialchars($poem['date']) ?></time>
      </header>
  <div class="poem-body"><?= htmlspecialchars($poem['body']) ?></div>
    </article>
    <?php endforeach; ?>
    
    <?php if (empty($poems)): ?>
      <p>No poems yet.</p>
    <?php endif; ?>
  </main>
</div>

<script>
// Scroll logic confined to the scrollable poems timeline, not the window
document.addEventListener('DOMContentLoaded', () => {
  const timeline = document.querySelector('.poems-timeline');
  const poems = document.querySelectorAll('.poem-item');
  const hash = window.location.hash;

  function scrollIntoViewInTimeline(el, behavior = 'instant') {
    if (!el) return;
    // Use native behavior so the nearest scrollable ancestor (timeline) scrolls
    el.scrollIntoView({ behavior, block: 'start' });
  }

  // Initial position: if hash present -> go to that poem; else go to the newest (bottom)
  if (hash) {
    const target = document.querySelector(hash);
    if (target) {
      // Wait next frame to ensure layout & sizes are ready
      requestAnimationFrame(() => scrollIntoViewInTimeline(target, 'instant'));
    }
  } else if (timeline && poems.length) {
    const last = poems[poems.length - 1];
    requestAnimationFrame(() => scrollIntoViewInTimeline(last, 'instant'));
  }

  // Smooth scroll for anchor clicks inside the page
  document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const href = e.currentTarget.getAttribute('href');
      const target = document.querySelector(href);
      if (target) {
        scrollIntoViewInTimeline(target, 'smooth');
        if (history.replaceState) history.replaceState(null, '', href);
        else location.hash = href;
      }
    });
  });
});
</script>