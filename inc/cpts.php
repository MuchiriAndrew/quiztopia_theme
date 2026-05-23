<?php
defined( 'ABSPATH' ) || exit;

/* ═══════════════════════════════════════════════════════════════
   CUSTOM POST TYPES
   ══════════════════════════════════════════════════════════════ */

/* ─── Events ──────────────────────────────────────────────────── */
function qt_register_cpts() {

    register_post_type( 'qt_event', [
        'labels' => [
            'name'               => 'Events',
            'singular_name'      => 'Event',
            'add_new_item'       => 'Add New Event',
            'edit_item'          => 'Edit Event',
            'new_item'           => 'New Event',
            'view_item'          => 'View Event',
            'search_items'       => 'Search Events',
            'not_found'          => 'No events found',
            'not_found_in_trash' => 'No events in trash',
        ],
        'public'             => true,
        'show_in_rest'       => true,
        'has_archive'        => true,
        'rewrite'            => [ 'slug' => 'events' ],
        'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
        'menu_icon'          => 'dashicons-calendar-alt',
        'menu_position'      => 5,
    ] );

    register_post_type( 'qt_testimonial', [
        'labels' => [
            'name'          => 'Testimonials',
            'singular_name' => 'Testimonial',
            'add_new_item'  => 'Add Testimonial',
            'edit_item'     => 'Edit Testimonial',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_rest' => true,
        'supports'     => [ 'title' ],
        'menu_icon'    => 'dashicons-format-quote',
        'menu_position'=> 6,
    ] );

    register_post_type( 'qt_faq', [
        'labels' => [
            'name'          => 'FAQ',
            'singular_name' => 'FAQ Item',
            'add_new_item'  => 'Add FAQ',
            'edit_item'     => 'Edit FAQ',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_rest' => true,
        'supports'     => [ 'title' ],
        'menu_icon'    => 'dashicons-editor-help',
        'menu_position'=> 7,
    ] );

    register_post_type( 'qt_leaderboard', [
        'labels' => [
            'name'          => 'Leaderboard',
            'singular_name' => 'Leaderboard Entry',
            'add_new_item'  => 'Add Entry',
            'edit_item'     => 'Edit Entry',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_rest' => true,
        'supports'     => [ 'title' ],
        'menu_icon'    => 'dashicons-awards',
        'menu_position'=> 8,
    ] );
}
add_action( 'init', 'qt_register_cpts' );

/* ═══════════════════════════════════════════════════════════════
   META BOXES
   ══════════════════════════════════════════════════════════════ */
function qt_add_meta_boxes() {

    add_meta_box( 'qt_event_details', 'Event Details',
        'qt_event_meta_box', 'qt_event', 'normal', 'high' );

    add_meta_box( 'qt_testimonial_details', 'Testimonial Details',
        'qt_testimonial_meta_box', 'qt_testimonial', 'normal', 'high' );

    add_meta_box( 'qt_faq_details', 'Answer',
        'qt_faq_meta_box', 'qt_faq', 'normal', 'high' );

    add_meta_box( 'qt_leaderboard_details', 'Leaderboard Details',
        'qt_leaderboard_meta_box', 'qt_leaderboard', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'qt_add_meta_boxes' );

/* ─── Event meta box ──────────────────────────────────────────── */
function qt_event_meta_box( $post ) {
    wp_nonce_field( 'qt_event_save', 'qt_event_nonce' );
    $fields = [
        'qt_event_date'          => [ 'Date', 'date', 'YYYY-MM-DD e.g. 2026-05-21' ],
        'qt_event_time'          => [ 'Time', 'text', 'e.g. 7:00 PM' ],
        'qt_event_venue'         => [ 'Venue', 'text', 'e.g. Wooden Barrels, APIC Center' ],
        'qt_event_venue_short'   => [ 'Venue (short)', 'text', 'e.g. Wooden Barrels — used in cards' ],
        'qt_event_price'         => [ 'Ticket Price (KES)', 'text', 'e.g. 500' ],
        'qt_event_seats_left'    => [ 'Seats Left', 'number', 'Leave blank if unlimited' ],
        'qt_event_ticket_url'    => [ 'Ticket URL / WooCommerce Product Slug', 'text', 'e.g. /shop/grand-trivia-april or WooCommerce product ID' ],
        'qt_event_wc_product_id' => [ 'WooCommerce Product ID', 'number', 'ID of the WooCommerce ticket product' ],
        'qt_event_theme'         => [ 'Event Theme', 'text', 'e.g. Pop Culture · Sports · Mixed Bag' ],
        'qt_event_badge'         => [ 'Card Badge', 'text', 'e.g. Next Up · Sold Out · Coming Soon — leave blank to hide' ],
        'qt_event_status'        => [ 'Status', 'select:upcoming,sold-out,coming-soon,past', '' ],
    ];
    echo '<table style="width:100%;border-spacing:0 12px">';
    foreach ( $fields as $key => $f ) {
        $val   = esc_attr( get_post_meta( $post->ID, $key, true ) );
        $label = esc_html( $f[0] );
        $hint  = esc_html( $f[2] );
        echo "<tr><td style='width:180px;padding-right:16px;vertical-align:top;padding-top:6px'><label for='{$key}'><strong>{$label}</strong></label></td><td>";
        if ( str_starts_with( $f[1], 'select:' ) ) {
            $opts = explode( ',', substr( $f[1], 7 ) );
            echo "<select name='{$key}' id='{$key}' style='width:100%;max-width:320px'>";
            foreach ( $opts as $o ) {
                $sel = selected( $val, $o, false );
                echo "<option value='{$o}' {$sel}>" . esc_html( ucfirst( str_replace( '-', ' ', $o ) ) ) . "</option>";
            }
            echo "</select>";
        } else {
            echo "<input type='{$f[1]}' name='{$key}' id='{$key}' value='{$val}' style='width:100%;max-width:480px'>";
        }
        if ( $hint ) echo "<p style='color:#666;font-size:12px;margin:4px 0 0'>{$hint}</p>";
        echo '</td></tr>';
    }
    echo '</table>';
}

/* ─── Testimonial meta box ────────────────────────────────────── */
function qt_testimonial_meta_box( $post ) {
    wp_nonce_field( 'qt_testimonial_save', 'qt_testimonial_nonce' );
    $quote  = get_post_meta( $post->ID, 'qt_testimonial_quote', true );
    $name   = esc_attr( get_post_meta( $post->ID, 'qt_testimonial_name', true ) );
    $detail = esc_attr( get_post_meta( $post->ID, 'qt_testimonial_detail', true ) );
    ?>
    <p><label><strong>Quote</strong></label><br>
    <textarea name="qt_testimonial_quote" rows="4" style="width:100%"><?php echo esc_textarea( $quote ); ?></textarea></p>
    <p><label><strong>Name</strong></label><br>
    <input type="text" name="qt_testimonial_name" value="<?php echo $name; ?>" style="width:100%">
    <span style="color:#666;font-size:12px">e.g. Amina W.</span></p>
    <p><label><strong>Detail</strong></label><br>
    <input type="text" name="qt_testimonial_detail" value="<?php echo $detail; ?>" style="width:100%">
    <span style="color:#666;font-size:12px">e.g. Team "Final Answer" · April 2026</span></p>
    <?php
}

/* ─── FAQ meta box ────────────────────────────────────────────── */
function qt_faq_meta_box( $post ) {
    wp_nonce_field( 'qt_faq_save', 'qt_faq_nonce' );
    $answer = get_post_meta( $post->ID, 'qt_faq_answer', true );
    $order  = esc_attr( get_post_meta( $post->ID, 'qt_faq_order', true ) );
    ?>
    <p><label><strong>Answer</strong></label><br>
    <textarea name="qt_faq_answer" rows="5" style="width:100%"><?php echo esc_textarea( $answer ); ?></textarea></p>
    <p><label><strong>Display Order</strong></label><br>
    <input type="number" name="qt_faq_order" value="<?php echo $order; ?>" style="width:80px">
    <span style="color:#666;font-size:12px">Lower number = shown first</span></p>
    <?php
}

/* ─── Leaderboard meta box ────────────────────────────────────── */
function qt_leaderboard_meta_box( $post ) {
    wp_nonce_field( 'qt_lb_save', 'qt_lb_nonce' );
    $venue = esc_attr( get_post_meta( $post->ID, 'qt_lb_venue', true ) );
    $score = esc_attr( get_post_meta( $post->ID, 'qt_lb_score', true ) );
    $badge = esc_attr( get_post_meta( $post->ID, 'qt_lb_badge', true ) );
    ?>
    <table style="width:100%;border-spacing:0 10px">
    <tr><td style="width:140px"><strong>Venue</strong></td><td>
    <input type="text" name="qt_lb_venue" value="<?php echo $venue; ?>" style="width:100%;max-width:320px">
    </td></tr>
    <tr><td><strong>Score</strong></td><td>
    <input type="number" name="qt_lb_score" value="<?php echo $score; ?>" style="width:120px">
    </td></tr>
    <tr><td><strong>Badge</strong></td><td>
    <input type="text" name="qt_lb_badge" value="<?php echo $badge; ?>" style="width:100%;max-width:320px">
    <span style="color:#666;font-size:12px">e.g. Champions · Runners Up — leave blank to hide</span>
    </td></tr>
    </table>
    <?php
}

/* ─── Save meta ───────────────────────────────────────────────── */
function qt_save_meta( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // Event
    if ( isset( $_POST['qt_event_nonce'] ) && wp_verify_nonce( $_POST['qt_event_nonce'], 'qt_event_save' ) ) {
        $event_fields = [
            'qt_event_date', 'qt_event_time', 'qt_event_venue', 'qt_event_venue_short',
            'qt_event_price', 'qt_event_seats_left', 'qt_event_ticket_url',
            'qt_event_wc_product_id', 'qt_event_theme', 'qt_event_badge', 'qt_event_status',
        ];
        foreach ( $event_fields as $f ) {
            if ( isset( $_POST[ $f ] ) ) {
                update_post_meta( $post_id, $f, sanitize_text_field( $_POST[ $f ] ) );
            }
        }
    }

    // Testimonial
    if ( isset( $_POST['qt_testimonial_nonce'] ) && wp_verify_nonce( $_POST['qt_testimonial_nonce'], 'qt_testimonial_save' ) ) {
        update_post_meta( $post_id, 'qt_testimonial_quote',  wp_kses_post( $_POST['qt_testimonial_quote'] ?? '' ) );
        update_post_meta( $post_id, 'qt_testimonial_name',   sanitize_text_field( $_POST['qt_testimonial_name'] ?? '' ) );
        update_post_meta( $post_id, 'qt_testimonial_detail', sanitize_text_field( $_POST['qt_testimonial_detail'] ?? '' ) );
    }

    // FAQ
    if ( isset( $_POST['qt_faq_nonce'] ) && wp_verify_nonce( $_POST['qt_faq_nonce'], 'qt_faq_save' ) ) {
        update_post_meta( $post_id, 'qt_faq_answer', wp_kses_post( $_POST['qt_faq_answer'] ?? '' ) );
        update_post_meta( $post_id, 'qt_faq_order',  absint( $_POST['qt_faq_order'] ?? 0 ) );
    }

    // Leaderboard
    if ( isset( $_POST['qt_lb_nonce'] ) && wp_verify_nonce( $_POST['qt_lb_nonce'], 'qt_lb_save' ) ) {
        update_post_meta( $post_id, 'qt_lb_venue', sanitize_text_field( $_POST['qt_lb_venue'] ?? '' ) );
        update_post_meta( $post_id, 'qt_lb_score', absint( $_POST['qt_lb_score'] ?? 0 ) );
        update_post_meta( $post_id, 'qt_lb_badge', sanitize_text_field( $_POST['qt_lb_badge'] ?? '' ) );
    }
}
add_action( 'save_post', 'qt_save_meta' );

/* ─── Leaderboard: show score in admin column ─────────────────── */
add_filter( 'manage_qt_leaderboard_posts_columns', function( $cols ) {
    $cols['lb_score'] = 'Score';
    $cols['lb_venue'] = 'Venue';
    return $cols;
} );
add_action( 'manage_qt_leaderboard_posts_custom_column', function( $col, $post_id ) {
    if ( $col === 'lb_score' ) echo get_post_meta( $post_id, 'qt_lb_score', true );
    if ( $col === 'lb_venue' ) echo get_post_meta( $post_id, 'qt_lb_venue', true );
}, 10, 2 );
add_filter( 'manage_edit-qt_leaderboard_sortable_columns', function( $cols ) {
    $cols['lb_score'] = 'lb_score';
    return $cols;
} );

/* ─── Events: show date + venue in admin column ───────────────── */
add_filter( 'manage_qt_event_posts_columns', function( $cols ) {
    $cols['event_date']  = 'Date';
    $cols['event_venue'] = 'Venue';
    $cols['event_price'] = 'Price (KES)';
    return $cols;
} );
add_action( 'manage_qt_event_posts_custom_column', function( $col, $post_id ) {
    if ( $col === 'event_date'  ) echo get_post_meta( $post_id, 'qt_event_date', true );
    if ( $col === 'event_venue' ) echo get_post_meta( $post_id, 'qt_event_venue_short', true );
    if ( $col === 'event_price' ) echo get_post_meta( $post_id, 'qt_event_price', true );
}, 10, 2 );
add_filter( 'manage_edit-qt_event_sortable_columns', function( $cols ) {
    $cols['event_date'] = 'qt_event_date';
    return $cols;
} );
