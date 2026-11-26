<?php
defined( 'ABSPATH' ) || exit;

function qt_tumizi_product_events( $product_id ) {
	$events = get_posts( array(
		'post_type' => 'qt_event',
		'post_status' => array( 'publish', 'draft', 'pending', 'private', 'future', 'trash' ),
		'posts_per_page' => -1,
		'fields' => 'ids',
		'meta_query' => array(
			'relation' => 'OR',
			array( 'key' => 'qt_event_wc_product_id', 'value' => $product_id ),
			array( 'key' => 'qt_event_ticket_url', 'compare' => 'EXISTS' ),
		),
		'no_found_rows' => true,
	) );
	return array_values( array_filter( $events, static function ( $event_id ) use ( $product_id ) {
		if ( (int) get_post_meta( $event_id, 'qt_event_wc_product_id', true ) === (int) $product_id ) return true;
		$target = qt_get_event_ticket_target( $event_id );
		return (int) ( $target['product_id'] ?? 0 ) === (int) $product_id;
	} ) );
}

function qt_tumizi_direct_receipt_product_eligibility( $eligible, $product, $rule, $receipt ) {
	if ( true !== $eligible || ! ( $product instanceof WC_Product ) ) return $eligible;
	$events = qt_tumizi_product_events( $product->get_id() );
	if ( ! $events ) return get_post_meta( $product->get_id(), '_qt_tumizi_ticket_product', true )
		? new WP_Error( 'quiztopia_event_missing', __( 'This ticket product has no available event. Review its event mapping.', 'quiztopia' ) ) : true;
	update_post_meta( $product->get_id(), '_qt_tumizi_ticket_product', 1 );
	if ( 1 !== count( $events ) ) {
		return new WP_Error( 'quiztopia_event_product_ambiguous', __( 'This ticket product is linked to more than one event. Review the event setup before creating an order.', 'quiztopia' ) );
	}
	$event_id = absint( $events[0] );
	$event_date = get_post_meta( $event_id, 'qt_event_date', true );
	$date = DateTimeImmutable::createFromFormat( '!Y-m-d', (string) $event_date );
	$event_status = get_post_meta( $event_id, 'qt_event_status', true );
	if ( 'publish' !== get_post_status( $event_id ) || 'upcoming' !== $event_status || ! $date || $date->format( 'Y-m-d' ) !== $event_date || $event_date < current_time( 'Y-m-d' ) ) {
		return new WP_Error( 'quiztopia_event_not_available', __( 'This event is not open for ticket orders. Review its publication status, sale status, and date.', 'quiztopia' ) );
	}
	return true;
}
add_filter( 'tumizi_wc_direct_receipt_product_eligibility', 'qt_tumizi_direct_receipt_product_eligibility', 10, 4 );

function qt_tumizi_ticket_order( $is_ticket_order, $order ) {
	if ( $is_ticket_order || ! $order instanceof WC_Order || ! $order->get_item_count() ) return $is_ticket_order;
	foreach ( $order->get_items() as $item ) {
		$product = $item->get_product();
		if ( ! $product || ! qt_tumizi_product_events( $product->get_parent_id() ?: $product->get_id() ) ) return false;
	}
	return true;
}
add_filter( 'tumizi_wc_ticket_order', 'qt_tumizi_ticket_order', 10, 2 );

function qt_tumizi_remember_event_product( $event_id ) {
	if ( 'qt_event' !== get_post_type( $event_id ) ) return;
	$target = qt_get_event_ticket_target( $event_id );
	$product_id = absint( get_post_meta( $event_id, 'qt_event_wc_product_id', true ) ) ?: absint( $target['product_id'] ?? 0 );
	if ( $product_id ) update_post_meta( $product_id, '_qt_tumizi_ticket_product', 1 );
}
add_action( 'before_delete_post', 'qt_tumizi_remember_event_product' );
function qt_tumizi_remember_event_mapping( $meta_id, $event_id, $meta_key ) {
	if ( in_array( $meta_key, array( 'qt_event_wc_product_id', 'qt_event_ticket_url' ), true ) ) qt_tumizi_remember_event_product( $event_id );
}
add_action( 'added_post_meta', 'qt_tumizi_remember_event_mapping', 10, 3 );
add_action( 'updated_post_meta', 'qt_tumizi_remember_event_mapping', 10, 3 );
