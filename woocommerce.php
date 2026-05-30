<?php
/**
 * woocommerce.php — wraps all WooCommerce pages (shop, cart, checkout, account)
 * in the QUIZTOPIA theme header/footer. WooCommerce content is rendered via
 * woocommerce_content() which respects individual page template hooks.
 */
get_header();

$wc_page_title = '';
if (is_shop())            $wc_page_title = 'Get <em>Tickets</em>';
elseif (is_cart())        $wc_page_title = 'Your <em>Cart</em>';
elseif (is_checkout() && !is_wc_endpoint_url('order-received')) $wc_page_title = 'Check<em>out</em>';
elseif (is_account_page()) $wc_page_title = 'My <em>Account</em>';
elseif (is_product())     $wc_page_title = '';
else                      $wc_page_title = woocommerce_page_title(false);

$is_checkout = is_checkout() && !is_wc_endpoint_url('order-received');
?>

<div class="is-woocommerce<?php echo $is_checkout ? ' is-checkout-page' : ''; ?>">
  <div class="qt-wc-wrap">
    <div class="qt-container<?php echo $is_checkout ? ' qt-container--checkout' : ''; ?>">

      <?php if ($wc_page_title && !is_product()) : ?>

        <?php if ($is_checkout) : ?>
        <!-- Checkout: compact inline header — no big display title eating space -->
        <div class="qt-wc-checkout-head">
          <span class="qt-label">QUIZTOPIA_KE / CHECKOUT</span>
        </div>

        <?php else : ?>
        <div class="qt-wc-page-header">
          <span class="qt-label" style="display:block;margin-bottom:var(--qt-md)">QUIZTOPIA_KE</span>
          <h1 class="qt-display qt-display--lg" data-animate>
            <?php echo wp_kses_post($wc_page_title); ?>
          </h1>
        </div>
        <?php endif; ?>

      <?php elseif (is_product()) : ?>
      <div style="padding-bottom:var(--qt-lg)">
        <?php /* Product title rendered by WooCommerce */ ?>
      </div>
      <?php endif; ?>

      <?php woocommerce_content(); ?>

    </div>
  </div>
</div>

<?php get_footer(); ?>
