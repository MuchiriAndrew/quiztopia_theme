<?php
defined( 'ABSPATH' ) || exit;

/* ─── Theme setup ─────────────────────────────────────────────── */
function qt_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'woocommerce', [
        'thumbnail_image_width' => 800,
        'gallery_thumbnail_image_width' => 400,
    ] );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );

    register_nav_menus( [
        'primary' => __( 'Primary Navigation', 'quiztopia' ),
        'footer'  => __( 'Footer Links', 'quiztopia' ),
    ] );
}
add_action( 'after_setup_theme', 'qt_setup' );

/* ─── Content width ───────────────────────────────────────────── */
function qt_content_width() {
    $GLOBALS['content_width'] = 1360;
}
add_action( 'after_setup_theme', 'qt_content_width', 0 );

/* ─── Enqueue assets ──────────────────────────────────────────── */
function qt_enqueue() {
    $ver = wp_get_theme()->get( 'Version' );

    // Fonts
    wp_enqueue_style( 'qt-google-fonts',
        'https://fonts.googleapis.com/css2?family=Big+Shoulders+Display:wght@400;700;900&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap',
        [], null );

    // Main stylesheet
    wp_enqueue_style( 'quiztopia',
        get_template_directory_uri() . '/assets/css/quiztopia.css',
        [ 'qt-google-fonts' ], $ver );

    // Main JS
    wp_enqueue_script( 'quiztopia',
        get_template_directory_uri() . '/assets/js/quiztopia.js',
        [], $ver, [ 'strategy' => 'defer', 'in_footer' => true ] );

    // Pass dynamic data to JS
    $next_event = qt_get_next_event();
    wp_localize_script( 'quiztopia', 'QT', [
        'nextEventDate' => $next_event ? get_post_meta( $next_event->ID, 'qt_event_date', true ) : '',
        'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
        'nonce'         => wp_create_nonce( 'qt_nonce' ),
    ] );
}
add_action( 'wp_enqueue_scripts', 'qt_enqueue' );

/* ─── Preconnect hints ────────────────────────────────────────── */
function qt_preconnect() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action( 'wp_head', 'qt_preconnect', 1 );

/* ─── Remove emoji / block library bloat ─────────────────────── */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
add_filter( 'wp_enqueue_scripts', function() {
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'wc-blocks-style' );
} );

/* ─── Includes ────────────────────────────────────────────────── */
require_once get_template_directory() . '/inc/cpts.php';
require_once get_template_directory() . '/inc/customizer.php';

/* ─── Helper: get next upcoming event ────────────────────────── */
function qt_get_next_event() {
    $today = date( 'Ymd' );
    $q = new WP_Query( [
        'post_type'      => 'qt_event',
        'posts_per_page' => 1,
        'meta_key'       => 'qt_event_date',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
        'meta_query'     => [ [
            'key'     => 'qt_event_date',
            'value'   => $today,
            'compare' => '>=',
            'type'    => 'DATE',
        ] ],
    ] );
    return $q->have_posts() ? $q->posts[0] : null;
}

/* ─── Helper: get customizer value with fallback ─────────────── */
function qt_opt( $key, $fallback = '' ) {
    return get_theme_mod( $key, $fallback );
}

/* ─── Newsletter AJAX handler ─────────────────────────────────── */
function qt_newsletter_subscribe() {
    check_ajax_referer( 'qt_nonce', 'nonce' );
    $email = sanitize_email( $_POST['email'] ?? '' );
    if ( ! is_email( $email ) ) {
        wp_send_json_error( [ 'message' => 'Please enter a valid email address.' ] );
    }
    // Hook into Mailchimp or any list — emit action for integration
    do_action( 'qt_newsletter_subscribe', $email );
    wp_send_json_success( [ 'message' => 'You\'re on the list.' ] );
}
add_action( 'wp_ajax_qt_subscribe', 'qt_newsletter_subscribe' );
add_action( 'wp_ajax_nopriv_qt_subscribe', 'qt_newsletter_subscribe' );

/* ─── WooCommerce: remove default sidebar ────────────────────── */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/* ─── WooCommerce: hide coupon (checkout + cart) ─────────────── */
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
remove_action( 'woocommerce_cart_coupon',          'woocommerce_cart_coupon',          10 );
add_filter( 'woocommerce_coupons_enabled', '__return_false' );

/* ─── WooCommerce: breadcrumb defaults ───────────────────────── */
add_filter( 'woocommerce_breadcrumb_defaults', function( $defaults ) {
    $defaults['delimiter'] = ' / ';
    return $defaults;
} );

/* ─── Body class for WooCommerce pages ───────────────────────── */
add_filter( 'body_class', function( $classes ) {
    if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
        $classes[] = 'is-woocommerce';
    }
    return $classes;
} );

/* ─── Add to cart redirect to checkout ───────────────────────── */
add_filter( 'woocommerce_add_to_cart_redirect', function() {
    return wc_get_checkout_url();
} );

/* ─── Remove reviews tab on single product pages ─────────────── */
add_filter( 'woocommerce_product_tabs', function( $tabs ) {
    unset( $tabs['reviews'] );
    return $tabs;
}, 98 );

/* ─── Remove product category/tag meta line ───────────────────── */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

/* ─── Shop: products only, suppress subcategory listings ─────── */
add_filter( 'woocommerce_show_product_subcategories', '__return_false' );

/* ─── Related products: 3 items, 3 columns ───────────────────── */
add_filter( 'woocommerce_output_related_products_args', function( $args ) {
    $args['posts_per_page'] = 3;
    $args['columns']        = 3;
    return $args;
} );

/* ─── Checkout: suppress default order review heading ────────── */
add_filter( 'woocommerce_order_review_heading', '__return_empty_string' );

/* ─── Shop: hide sort dropdown & result count ─────────────────── */
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count',     20 );

/* ─── Shop: only show in-stock products (hides coming-soon items) ─ */
add_action( 'woocommerce_product_query', function( $q ) {
    if ( ! is_admin() ) {
        $meta = (array) $q->get( 'meta_query' );
        $meta[] = [
            'key'     => '_stock_status',
            'value'   => 'instock',
            'compare' => '=',
        ];
        $q->set( 'meta_query', $meta );
    }
} );
