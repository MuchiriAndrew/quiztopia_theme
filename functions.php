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
        [ 'qt-google-fonts' ], (string) filemtime( get_template_directory() . '/assets/css/quiztopia.css' ) );

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
require_once get_template_directory() . '/inc/email-templates.php';

/* ─── Helpers: event dates + ticket targets ──────────────────── */
function qt_normalize_event_date( $raw_date ) {
    $raw_date = trim( (string) $raw_date );

    if ( $raw_date === '' ) {
        return '';
    }

    if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw_date ) ) {
        return $raw_date;
    }

    if ( preg_match( '/^(\d{4})(\d{2})(\d{2})$/', $raw_date, $m ) ) {
        return "{$m[1]}-{$m[2]}-{$m[3]}";
    }

    $timestamp = strtotime( $raw_date );
    return $timestamp ? date( 'Y-m-d', $timestamp ) : '';
}

function qt_resolve_event_ticket_reference( $reference ) {
    $reference = trim( (string) $reference );

    if ( $reference === '' ) {
        return [
            'product_id' => 0,
            'url'        => '',
        ];
    }

    if ( ctype_digit( $reference ) ) {
        $product_id = absint( $reference );
        if ( $product_id && get_post_type( $product_id ) === 'product' ) {
            return [
                'product_id' => $product_id,
                'url'        => get_permalink( $product_id ),
            ];
        }
    }

    if ( preg_match( '#^https?://#i', $reference ) || str_starts_with( $reference, '/' ) ) {
        $url        = str_starts_with( $reference, '/' ) ? home_url( $reference ) : $reference;
        $product_id = url_to_postid( $url );

        if ( $product_id && get_post_type( $product_id ) === 'product' ) {
            return [
                'product_id' => $product_id,
                'url'        => get_permalink( $product_id ),
            ];
        }

        $path_candidate = basename( wp_parse_url( $url, PHP_URL_PATH ) ?: '' );
        if ( $path_candidate !== '' ) {
            $product = get_page_by_path( $path_candidate, OBJECT, 'product' );
            if ( $product instanceof WP_Post ) {
                return [
                    'product_id' => $product->ID,
                    'url'        => get_permalink( $product->ID ),
                ];
            }
        }

        return [
            'product_id' => 0,
            'url'        => $url,
        ];
    }

    $slug = trim( $reference, "/ \t\n\r\0\x0B" );

    foreach ( [ $slug, basename( $slug ) ] as $candidate ) {
        if ( $candidate === '' ) {
            continue;
        }

        $product = get_page_by_path( $candidate, OBJECT, 'product' );
        if ( $product instanceof WP_Post ) {
            return [
                'product_id' => $product->ID,
                'url'        => get_permalink( $product->ID ),
            ];
        }
    }

    $fallback_url = home_url( '/' . ltrim( $slug, '/' ) . '/' );
    $product_id   = url_to_postid( $fallback_url );

    if ( $product_id && get_post_type( $product_id ) === 'product' ) {
        return [
            'product_id' => $product_id,
            'url'        => get_permalink( $product_id ),
        ];
    }

    return [
        'product_id' => 0,
        'url'        => $fallback_url,
    ];
}

function qt_get_event_ticket_target( $event_id ) {
    $event_id = absint( $event_id );

    if ( ! $event_id ) {
        return [
            'product_id' => 0,
            'url'        => '',
        ];
    }

    $product_id = absint( get_post_meta( $event_id, 'qt_event_wc_product_id', true ) );
    if ( $product_id && get_post_type( $product_id ) === 'product' ) {
        return [
            'product_id' => $product_id,
            'url'        => get_permalink( $product_id ),
        ];
    }

    return qt_resolve_event_ticket_reference(
        get_post_meta( $event_id, 'qt_event_ticket_url', true )
    );
}

/* ─── Helper: get next upcoming event ────────────────────────── */
function qt_get_next_event() {
    $today = current_time( 'Y-m-d' );
    $q = new WP_Query( [
        'post_type'      => 'qt_event',
        'post_status'    => 'publish',
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

    // Duplicate check via meta
    $existing = get_posts( [
        'post_type'      => 'qt_lead',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'meta_query'     => [ [
            'key'     => 'qt_lead_email',
            'value'   => $email,
            'compare' => '=',
        ] ],
    ] );

    if ( ! empty( $existing ) ) {
        wp_send_json_success( [ 'message' => 'You\'re already on the list!' ] );
    }

    // Save lead
    $lead_id = wp_insert_post( [
        'post_type'   => 'qt_lead',
        'post_title'  => $email,
        'post_status' => 'publish',
    ] );

    if ( $lead_id && ! is_wp_error( $lead_id ) ) {
        update_post_meta( $lead_id, 'qt_lead_email', $email );
        update_post_meta( $lead_id, 'qt_lead_source', 'newsletter' );

        // Send confirmation email
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . ( get_bloginfo('name') ?: 'QUIZTOPIA_KE' ) . ' <' . get_bloginfo('admin_email') . '>',
        ];
        wp_mail( $email, 'You\'re in the loop — QUIZTOPIA_KE', qt_email_confirmation( $email ), $headers );
    }

    do_action( 'qt_newsletter_subscribe', $email );
    wp_send_json_success( [ 'message' => 'You\'re in! Check your inbox.' ] );
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

add_filter( 'woocommerce_checkout_fields', function( $fields ) {
    if ( isset( $fields['billing']['billing_phone'] ) ) {
        $fields['billing']['billing_phone']['placeholder'] = '254XXXXXXXXX';
        $fields['billing']['billing_phone']['description'] = __( 'Accepted formats: 07XXXXXXXX, 01XXXXXXXX or 254XXXXXXXXX.', 'quiztopia' );
        $fields['billing']['billing_phone']['custom_attributes']['inputmode'] = 'tel';
    }
    return $fields;
}, 1000 );

add_filter( 'woocommerce_checkout_posted_data', function( $data ) {
    if ( isset( $data['billing_phone'] ) && class_exists( 'WC_Gateway_Tumizi' ) ) {
        $phone = WC_Gateway_Tumizi::normalize_phone( $data['billing_phone'] );
        if ( $phone ) {
            $data['billing_phone'] = $phone;
        }
    }
    return $data;
} );

add_action( 'woocommerce_after_checkout_validation', function( $data, $errors ) {
    if ( ! empty( $data['billing_phone'] ) && class_exists( 'WC_Gateway_Tumizi' ) && ! WC_Gateway_Tumizi::normalize_phone( $data['billing_phone'] ) ) {
        $errors->add( 'billing_phone_kenyan', __( 'Check your phone number. Use 07XXXXXXXX, 01XXXXXXXX or 254XXXXXXXXX.', 'quiztopia' ), [ 'id' => 'billing_phone' ] );
    }
}, 10, 2 );

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
