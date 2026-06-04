<?php
/**
 * QUIZTOPIA — Branded thank-you / order confirmation page.
 * Overrides WooCommerce's default thankyou.php.
 *
 * @package quiztopia
 */
defined( 'ABSPATH' ) || exit;

$instagram_url    = qt_opt( 'qt_instagram_url', 'https://instagram.com/quiztopia_ke' );
$instagram_handle = qt_opt( 'qt_instagram_handle', '@quiztopia_ke' );
?>

<div class="qt-thankyou">
  <div class="qt-container">
    <div class="qt-thankyou__inner">

      <?php if ( $order = wc_get_order( $order ) ) : // $order is passed by WC hook ?>

        <?php if ( $order->has_status( 'failed' ) ) : ?>

          <div class="qt-thankyou__tick" aria-hidden="true">✗</div>
          <h1 class="qt-thankyou__title">Payment <em>failed.</em></h1>
          <p class="qt-thankyou__sub">
            Something went wrong. No charge was made. Give it another try or reach us on
            <?php if ($instagram_url) : ?><a href="<?php echo esc_url($instagram_url); ?>" style="color:var(--qt-accent)"><?php echo esc_html($instagram_handle); ?></a><?php endif; ?>.
          </p>
          <div class="qt-thankyou__actions">
            <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="qt-btn-primary">Try again</a>
            <a href="<?php echo esc_url( wc_get_page_permalink('shop') ); ?>" class="qt-btn-ghost">Back to shop</a>
          </div>

        <?php else : ?>

          <div class="qt-thankyou__tick" aria-hidden="true">✓</div>
          <h1 class="qt-thankyou__title">You're <em>on the list.</em></h1>
          <p class="qt-thankyou__sub">
            Order #<?php echo esc_html( $order->get_order_number() ); ?> confirmed.
            We've sent confirmation to <strong><?php echo esc_html( $order->get_billing_email() ); ?></strong>.
            See you on the night.
          </p>

          <div class="qt-thankyou__order-details">
            <h2>Order Summary</h2>
            <table class="woocommerce-table shop_table" style="width:100%">
              <thead>
                <tr>
                  <th>Item</th>
                  <th style="text-align:right">Total</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ( $order->get_items() as $item_id => $item ) : ?>
                <tr>
                  <td><?php echo esc_html( $item->get_name() ); ?> &times; <?php echo esc_html( $item->get_quantity() ); ?></td>
                  <td style="text-align:right"><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr>
                  <td><strong>Total</strong></td>
                  <td style="text-align:right"><strong><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong></td>
                </tr>
              </tfoot>
            </table>
          </div>

          <?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>

          <div class="qt-thankyou__actions">
           
            <a href="<?php echo esc_url( wc_get_account_endpoint_url('orders') ); ?>" class="qt-btn-ghost">My orders</a>
            <?php if ($instagram_url) : ?>
            <a href="<?php echo esc_url($instagram_url); ?>" class="qt-btn-ghost" target="_blank" rel="noopener noreferrer">Follow us <?php echo esc_html($instagram_handle); ?></a>
            <?php endif; ?>
          </div>

        <?php endif; ?>

      <?php else : ?>

        <div class="qt-thankyou__tick" aria-hidden="true">✓</div>
        <h1 class="qt-thankyou__title">Order <em>received.</em></h1>
        <p class="qt-thankyou__sub">Thank you for your order. Check your email for confirmation.</p>
        <?php do_action( 'woocommerce_thankyou', 0 ); ?>
        <div class="qt-thankyou__actions">
          <a href="<?php echo esc_url( home_url('/') ); ?>" class="qt-btn-primary">Back home</a>
        </div>

      <?php endif; ?>

    </div>
  </div>
</div>
