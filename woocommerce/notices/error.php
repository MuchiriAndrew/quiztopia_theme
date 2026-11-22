<?php
defined( 'ABSPATH' ) || exit;

if ( ! $notices ) {
    return;
}
?>
<div class="woocommerce-error qt-checkout-notice" role="alert" tabindex="-1">
    <span class="qt-checkout-notice__icon" aria-hidden="true">!</span>
    <div class="qt-checkout-notice__content">
        <h2 class="qt-checkout-notice__title"><?php esc_html_e( 'Please check your details', 'quiztopia' ); ?></h2>
        <ul class="qt-checkout-notice__messages">
            <?php foreach ( $notices as $notice ) : ?>
                <li<?php echo wc_get_notice_data_attr( $notice ); ?>><?php echo wc_kses_notice( $notice['notice'] ); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
