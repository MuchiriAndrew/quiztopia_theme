<?php
$instagram_url    = qt_opt('qt_instagram_url', 'https://instagram.com/quiztopia_ke');
$instagram_handle = qt_opt('qt_instagram_handle', '@quiztopia_ke');
$tiktok_url       = qt_opt('qt_tiktok_url', '');
$whatsapp_url     = qt_opt('qt_whatsapp_url', '');
$nl_headline      = qt_opt('qt_nl_headline', 'Stay in the');
$nl_accent        = qt_opt('qt_nl_accent', 'loop.');
$nl_sub           = qt_opt('qt_nl_sub', 'New events, new venues, early-bird tickets — straight to your inbox.');
$footer_copy      = qt_opt('qt_footer_copy', '© ' . date('Y') . ' QUIZTOPIA_KE. All rights reserved.');
?>

<!-- ╔══ FOOTER — Ft7 Newsletter-first ═══════════════════════════╗ -->
<footer class="qt-footer" id="newsletter">
  <div class="qt-container">

    <div class="qt-footer__newsletter" data-animate>
      <p class="qt-footer__nl-label">
        <?php echo esc_html($nl_headline); ?> <em><?php echo esc_html($nl_accent); ?></em>
      </p>
      <p class="qt-footer__nl-sub"><?php echo esc_html($nl_sub); ?></p>

      <form class="qt-footer__nl-form" novalidate>
        <label for="qt-nl-email" class="screen-reader-text">Email address</label>
        <input
          type="email"
          id="qt-nl-email"
          class="qt-footer__nl-input"
          placeholder="Your email"
          autocomplete="email"
          required
        >
        <button type="submit" class="qt-footer__nl-btn">Notify me</button>
      </form>
      <p class="qt-footer__nl-msg" aria-live="polite"></p>
    </div>

    <div class="qt-footer__bottom">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="qt-footer__wordmark">QUIZTOPIA_KE</a>

      <div class="qt-footer__meta">
        <?php echo esc_html($footer_copy); ?>
        <a href="<?php echo esc_url(get_privacy_policy_url()); ?>">Privacy</a>
        <?php if (function_exists('WC')) : ?>
        <a href="<?php echo esc_url(wc_get_page_permalink('terms')); ?>">Terms</a>
        <?php endif; ?>
      </div>

      <div class="qt-footer__social">
        <?php if ($instagram_url) : ?>
        <a href="<?php echo esc_url($instagram_url); ?>" class="qt-footer__social-link" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
          <?php echo esc_html($instagram_handle ?: 'Instagram'); ?>
        </a>
        <?php endif; ?>
        <?php if ($tiktok_url) : ?>
        <a href="<?php echo esc_url($tiktok_url); ?>" class="qt-footer__social-link" target="_blank" rel="noopener noreferrer" aria-label="TikTok">TikTok</a>
        <?php endif; ?>
        <?php if ($whatsapp_url) : ?>
        <a href="<?php echo esc_url($whatsapp_url); ?>" class="qt-footer__social-link" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">WhatsApp</a>
        <?php endif; ?>
      </div>
    </div>

  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
