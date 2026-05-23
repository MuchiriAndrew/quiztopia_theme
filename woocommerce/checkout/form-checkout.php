<?php
/**
 * QUIZTOPIA — Checkout form: billing left, order summary + payment right.
 * @package quiztopia
 */
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
    echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
    return;
}
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout qt-checkout-form"
      action="<?php echo esc_url( wc_get_checkout_url() ); ?>"
      enctype="multipart/form-data"
      aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">

  <div class="qt-checkout-grid">

    <!-- ── Left: billing & shipping details ─────────────────────── -->
    <div class="qt-checkout-fields">
      <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
      <?php do_action( 'woocommerce_checkout_billing' ); ?>
      <?php do_action( 'woocommerce_checkout_shipping' ); ?>
      <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
    </div>

    <!-- ── Right: order summary + payment + place order ─────────── -->
    <aside class="qt-checkout-summary">
      <h2 class="qt-checkout-summary__title">Order Summary</h2>

      <div id="order_review" class="woocommerce-checkout-review-order">
        <?php do_action( 'woocommerce_checkout_order_review' ); ?>
      </div>
    </aside>

  </div>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
