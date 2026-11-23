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

        <?php elseif ( $order->is_paid() ) : ?>

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

        <?php else : ?>

          <div class="qt-thankyou__tick" aria-hidden="true">…</div>
          <h1 class="qt-thankyou__title">Payment <em>pending.</em></h1>
          <p class="qt-thankyou__sub">
            <?php if ( 'tumizi' === $order->get_payment_method() ) : ?>
              <?php
              $qt_tumizi_attempts = (array) $order->get_meta( '_tumizi_attempts', true );
              $qt_tumizi_attempt = end( $qt_tumizi_attempts );
              $qt_tumizi_method = is_array( $qt_tumizi_attempt ) ? ( $qt_tumizi_attempt['payment_method'] ?? $qt_tumizi_attempt['method'] ?? 'stk' ) : 'stk';
              if ( 'paybill' === $qt_tumizi_method ) :
                  echo esc_html__( 'Complete payment using the order-specific M-PESA Paybill details below. Your order will update after Tumizi confirms the full amount.', 'quiztopia' );
              else :
                  echo esc_html__( 'Check your phone and enter your M-PESA PIN to complete the payment. This page will update after Tumizi confirms it.', 'quiztopia' );
              endif;
              ?>
            <?php else : ?>
              Your payment is awaiting confirmation. We will update your order when it is confirmed.
            <?php endif; ?>
          </p>
          <?php if ( 'tumizi' === $order->get_payment_method() && is_array( $qt_tumizi_attempt ) && 'paybill' === $qt_tumizi_method && ! empty( $qt_tumizi_attempt['collection_reference'] ) ) : ?>
            <div class="qt-thankyou__order-details">
              <h2><?php esc_html_e( 'Paybill instructions', 'quiztopia' ); ?></h2>
              <p><?php esc_html_e( 'Paybill number', 'quiztopia' ); ?>: <strong><?php echo esc_html( $qt_tumizi_attempt['collection_paybill'] ?? '—' ); ?></strong></p>
              <p><?php esc_html_e( 'Account name', 'quiztopia' ); ?>: <strong><?php echo esc_html( $qt_tumizi_attempt['collection_account_name'] ?? 'Quiztopia' ); ?></strong></p>
              <p><?php esc_html_e( 'Account number', 'quiztopia' ); ?>: <strong><?php echo esc_html( $qt_tumizi_attempt['collection_reference'] ); ?></strong></p>
              <p><?php esc_html_e( 'Amount due', 'quiztopia' ); ?>: <strong><?php echo wp_kses_post( wc_price( (float) ( $qt_tumizi_attempt['balance_due'] ?? $order->get_total() ), array( 'currency' => $order->get_currency() ) ) ); ?></strong></p>
            </div>
          <?php endif; ?>
          <div class="qt-thankyou__actions">
            <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="qt-btn-primary">Return to order</a>
            <a href="<?php echo esc_url( wc_get_account_endpoint_url('orders') ); ?>" class="qt-btn-ghost">My orders</a>
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
