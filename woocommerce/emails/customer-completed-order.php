<?php
/**
 * Quiztopia customer completed order email.
 *
 * This override follows WooCommerce 10.4.0's customer completed order template
 * and replaces the generic introduction with Quiztopia's order confirmation copy.
 *
 * @package Quiztopia\WooCommerce\Emails
 * @version 10.4.0
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

defined( 'ABSPATH' ) || exit;

$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );

do_action( 'woocommerce_email_header', $email_heading, $email );
?>
<?php echo $email_improvements_enabled ? '<div class="email-introduction">' : ''; ?>
<p>
<?php if ( $order->get_billing_first_name() ) : ?>
	<?php printf( esc_html__( 'Hi %s,', 'quiztopia' ), esc_html( $order->get_billing_first_name() ) ); ?>
<?php else : ?>
	<?php esc_html_e( 'Hi there,', 'quiztopia' ); ?>
<?php endif; ?>
</p>
<p><?php esc_html_e( 'Good news: your Quiztopia order is complete. Take a look at the details below, then gather your crew and bring your sharpest trivia brain. We can’t wait to see you there.', 'quiztopia' ); ?></p>
<?php if ( $email_improvements_enabled ) : ?>
	<p><?php esc_html_e( 'Here’s what you’ve got lined up:', 'quiztopia' ); ?></p>
<?php endif; ?>
<?php echo $email_improvements_enabled ? '</div>' : ''; ?>

<?php do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email ); ?>
<?php do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email ); ?>
<?php do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email ); ?>

<p><?php esc_html_e( 'See you at the quiz,', 'quiztopia' ); ?><br /><?php esc_html_e( 'The Quiztopia crew', 'quiztopia' ); ?></p>

<?php if ( $additional_content ) : ?>
	<?php echo $email_improvements_enabled ? '<table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation"><tr><td class="email-additional-content">' : ''; ?>
	<?php echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) ); ?>
	<?php echo $email_improvements_enabled ? '</td></tr></table>' : ''; ?>
<?php endif; ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
