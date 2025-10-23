<?php
global $SETTINGS, $CURRENT_PAGE, $CURRENT_LANG;
require_once __DIR__ . '/helpers.php';
?>
    </div><!-- /.content -->
    <?php // Socials + version now live INSIDE the container as part of the fixed block ?>
    <?php require __DIR__ . '/content_footer.php'; ?>
  </div><!-- /.container -->

  <!-- Fixed footer with donations only (socials+version now in content) -->
  <footer class="site-footer">
      <a href="<?= htmlspecialchars($SETTINGS['donations']['paypal_url'] ?? 'https://paypal.me/igorkuldmaa') ?>" target="_blank" rel="noopener noreferrer" class="paypal-btn" title="Support via PayPal">
        <svg class="paypal-logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 32" aria-hidden="true" focusable="false">
          <path fill="#fff" d="M20.6 4.1c-1.3-1.5-3.4-2.1-6.1-2.1H8.7a1 1 0 0 0-1 .8L5.2 23.7a.8.8 0 0 0 .8 1h4a1 1 0 0 0 1-.8l1-6a1 1 0 0 1 1-.8h2.2c4.8 0 8.4-2 9.5-7.6.3-2.1 0-3.8-1.1-5.4zM22.8 9.7c-.8 4.3-3.7 7.1-8.3 7.1h-2.1a.7.7 0 0 0-.7.6l-.8 4.8c0 .3.2.5.5.5h3.5c.3 0 .5-.2.6-.5l.1-.4 1-5.4a.7.7 0 0 1 .7-.6h.5c3.4 0 6-1.4 6.8-5.3.4-1.8.2-3.3-.7-4.4-.8-1.1-2.2-1.7-4.2-1.7h-3.2l.4-2.6h3.2c2.2 0 3.9.5 5.1 1.8 1.1 1.3 1.4 3 1 5.2z"/>
        </svg>
        <span>PayPal</span>
      </a>

      <div id="crypto-container" aria-label="Crypto donations">
        <?php if (!empty($SETTINGS['donations']['crypto'])): foreach ($SETTINGS['donations']['crypto'] as $c): ?>
          <button type="button" class="crypto-btn" data-address="<?= htmlspecialchars($c['address']) ?>" title="<?= htmlspecialchars($c['symbol']) ?>: Click to copy address">
            <svg class="crypto-logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" aria-hidden="true">
              <circle cx="16" cy="16" r="16" fill="<?= htmlspecialchars($c['color'] ?? '#333') ?>"/>
              <?php if (!empty($c['svgPath'])): ?><path fill="#fff" d="<?= htmlspecialchars($c['svgPath']) ?>"/><?php endif; ?>
            </svg>
            <?= htmlspecialchars($c['symbol']) ?>
            <div class="tooltip" role="tooltip">
              <?php if (!empty($c['qr'])): ?><img src="./img/<?= htmlspecialchars($c['qr']) ?>" alt="<?= htmlspecialchars($c['symbol']) ?> QR"><?php endif; ?>
              <span class="address"><?= htmlspecialchars($c['address']) ?></span>
              <div class="copy-text">Click to Copy</div>
            </div>
          </button>
        <?php endforeach; endif; ?>
      </div>

    </footer>


    <!-- Third-party widgets -->
    <script src='https://storage.ko-fi.com/cdn/scripts/overlay-widget.js'></script>
    <script>
      kofiWidgetOverlay.draw('garryq', {
        'type': 'floating-chat',
        'floating-chat.donateButton.text': 'Support me',
        'floating-chat.donateButton.background-color': 'f45d22',
        'floating-chat.donateButton.text-color': '#fff'
      });
    </script>
    <script data-name="BMC-Widget" data-cfasync="false" src="https://cdnjs.buymeacoffee.com/1.0.0/widget.prod.min.js" data-id="garryq" data-description="Support me on Buy me a coffee!" data-message="Buy me a Coffee" data-color="#FF813F" data-position="Right" data-x_margin="18" data-y_margin="18"></script>

    <!-- App scripts -->
    <script type="module" src="./src/js/app.js"></script>
    <script type="module" src="./src/js/donations.js"></script>
    <?php if ($CURRENT_PAGE === 'land'): ?>
    <script type="module" src="./src/js/land_arrows.js"></script>
    <?php endif; ?>
  </body>
  </html>