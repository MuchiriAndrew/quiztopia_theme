<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
$instagram_url = qt_opt('qt_instagram_url', 'https://instagram.com/quiztopia_ke');
$has_wc = function_exists('WC');
$shop_url = $has_wc ? wc_get_page_permalink('shop') : '/shop';
?>

<!-- ╔══ NAV — N5 Floating Pill ═══════════════════════════════════╗ -->
<nav class="qt-nav" aria-label="Site navigation">
  <a href="<?php echo esc_url(home_url('/')); ?>" class="qt-nav__wordmark" aria-label="QUIZTOPIA_KE home">
    QUIZTOPIA_KE
  </a>

  <ul class="qt-nav__links" role="list">
    <li><a href="<?php echo esc_url(home_url('/#events')); ?>">Events</a></li>
    <li><a href="<?php echo esc_url(home_url('/#experience')); ?>">The Night</a></li>
    <li><a href="<?php echo esc_url(home_url('/#leaderboard')); ?>">Rankings</a></li>
    <li><a href="<?php echo esc_url(home_url('/#faq')); ?>">FAQ</a></li>
    <?php if ($instagram_url) : ?>
    <li><a href="<?php echo esc_url($instagram_url); ?>" target="_blank" rel="noopener noreferrer">Instagram</a></li>
    <?php endif; ?>
  </ul>

  <a href="<?php echo esc_url($shop_url); ?>" class="qt-nav__cta">Get Tickets</a>

  <?php if ($has_wc && WC()->cart) :
    $qt_cart_count = WC()->cart->get_cart_contents_count();
  ?>
  <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="qt-nav__cart" aria-label="<?php echo $qt_cart_count ? esc_attr('Cart — ' . $qt_cart_count . ' item' . ($qt_cart_count > 1 ? 's' : '')) : 'Cart'; ?>">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
    <?php if ($qt_cart_count > 0) : ?><span class="qt-nav__cart-count" aria-hidden="true"><?php echo esc_html($qt_cart_count); ?></span><?php endif; ?>
  </a>
  <?php endif; ?>

  <button class="qt-nav__burger" aria-expanded="false" aria-controls="qt-mobile-drawer" aria-label="Open navigation">
    <span aria-hidden="true"></span>
    <span aria-hidden="true"></span>
    <span aria-hidden="true"></span>
  </button>
</nav>

<!-- Mobile drawer -->
<div class="qt-nav__drawer" id="qt-mobile-drawer" role="dialog" aria-label="Mobile navigation">
  <a href="<?php echo esc_url(home_url('/#events')); ?>">Events</a>
  <a href="<?php echo esc_url(home_url('/#experience')); ?>">The Night</a>
  <a href="<?php echo esc_url(home_url('/#leaderboard')); ?>">Rankings</a>
  <a href="<?php echo esc_url(home_url('/#faq')); ?>">FAQ</a>
  <?php if ($instagram_url) : ?>
  <a href="<?php echo esc_url($instagram_url); ?>" target="_blank" rel="noopener noreferrer">Instagram</a>
  <?php endif; ?>
  <a href="<?php echo esc_url($shop_url); ?>" style="color: var(--qt-accent)">Get Tickets →</a>
  <?php if ($has_wc && WC()->cart && WC()->cart->get_cart_contents_count() > 0) : ?>
  <a href="<?php echo esc_url(wc_get_cart_url()); ?>">Cart (<?php echo esc_html(WC()->cart->get_cart_contents_count()); ?>)</a>
  <?php endif; ?>
</div>
