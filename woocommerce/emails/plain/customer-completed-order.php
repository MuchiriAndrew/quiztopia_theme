<?php
/**
 * Quiztopia customer completed order email (plain text).
 *
 * @package Quiztopia\WooCommerce\Emails
 * @version 9.9.0
 */

defined( 'ABSPATH' ) || exit;

echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n" . str_repeat( '=', strlen( wp_strip_all_tags( $email_heading ) ) ) . "\n\n";

if ( $order->get_billing_first_name() ) {
	printf( esc_html__( 'Hi %s,', 'quiztopia' ), esc_html( $order->get_billing_first_name() ) );
} else {
	esc_html_e( 'Hi there,', 'quiztopia' );
}

echo "\n\n" . esc_html__( 'Good news: your Quiztopia order is complete. Take a look at the details below, then gather your crew and bring your sharpest trivia brain. We can’t wait to see you there.', 'quiztopia' ) . "\n\n";
echo esc_html__( 'Here’s what you’ve got lined up:', 'quiztopia' ) . "\n\n";

do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n----------------------------------------\n\n";
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n" . esc_html__( 'See you at the quiz,', 'quiztopia' ) . "\n" . esc_html__( 'The Quiztopia crew', 'quiztopia' ) . "\n\n";
if ( $additional_content ) {
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) ) . "\n\n";
}
echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
