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

    register_post_type( 'qt_lead', [
        'labels' => [
            'name'          => 'Leads',
            'singular_name' => 'Lead',
            'all_items'     => 'All Leads',
            'add_new_item'  => 'Add Lead',
            'edit_item'     => 'Edit Lead',
            'not_found'     => 'No leads yet',
        ],
        'public'          => false,
        'show_ui'         => true,
        'show_in_rest'    => false,
        'supports'        => [ 'title' ],
        'menu_icon'       => 'dashicons-email-alt',
        'menu_position'   => 9,
        'capability_type' => 'post',
    ] );
}
add_action( 'init', 'qt_register_cpts' );

/* ═══════════════════════════════════════════════════════════════
   META BOXES
   ══════════════════════════════════════════════════════════════ */
function qt_add_meta_boxes() {

    add_meta_box( 'qt_event_details', 'Event Details',
        'qt_event_meta_box', 'qt_event', 'normal', 'high' );

    add_meta_box( 'qt_event_announce', 'Email Leads',
        'qt_event_announce_meta_box', 'qt_event', 'side', 'high' );

    add_meta_box( 'qt_testimonial_details', 'Testimonial Details',
        'qt_testimonial_meta_box', 'qt_testimonial', 'normal', 'high' );

    add_meta_box( 'qt_faq_details', 'Answer',
        'qt_faq_meta_box', 'qt_faq', 'normal', 'high' );

    add_meta_box( 'qt_leaderboard_details', 'Leaderboard Details',
        'qt_leaderboard_meta_box', 'qt_leaderboard', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'qt_add_meta_boxes' );

/* ─── Enqueue WP media uploader on event edit screens ────────── */
add_action( 'admin_enqueue_scripts', function( $hook ) {
    global $post;
    if ( in_array( $hook, [ 'post.php', 'post-new.php' ] ) && isset( $post ) && $post->post_type === 'qt_event' ) {
        wp_enqueue_media();
    }
} );

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

    /* ── Card image picker ── */
    $img_id  = absint( get_post_meta( $post->ID, 'qt_event_image_id', true ) );
    $img_url = $img_id ? wp_get_attachment_image_url( $img_id, 'medium' ) : '';
    echo "<tr><td style='width:180px;padding-right:16px;vertical-align:top;padding-top:6px'><strong>Card Image</strong></td><td>";
    echo "<div id='qt-img-preview' style='margin-bottom:8px'>";
    if ( $img_url ) echo "<img src='" . esc_url( $img_url ) . "' style='max-width:200px;height:auto;display:block;border:1px solid #ddd'>";
    echo "</div>";
    echo "<input type='hidden' name='qt_event_image_id' id='qt_event_image_id' value='" . esc_attr( $img_id ?: '' ) . "'>";
    echo "<button type='button' class='button' id='qt-img-upload'>Set Image</button> ";
    echo "<button type='button' class='button' id='qt-img-remove'" . ( $img_id ? "" : " style='display:none'" ) . ">Remove</button>";
    echo "<p style='color:#666;font-size:12px;margin:4px 0 0'>Displayed on event cards on the homepage and events page</p>";
    echo "</td></tr>";

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

    /* ── What to expect ── */
    $expect_raw = get_post_meta( $post->ID, 'qt_event_expect', true );
    ?>
    <hr style="margin:16px 0 12px">
    <p style="margin-bottom:6px"><strong>What to Expect</strong>
      <span style="color:#666;font-size:12px;font-weight:400;margin-left:6px">One item per line. Leave blank to use the default list.</span>
    </p>
    <textarea name="qt_event_expect" rows="6" style="width:100%;font-family:monospace;font-size:13px"><?php echo esc_textarea( $expect_raw ); ?></textarea>
    <p style="color:#666;font-size:12px;margin:4px 0 0">Default items: rounds, prizes, team size, bar access, theme (when set).</p>
    <?php

    /* ── Media uploader JS ── */
    ?>
    <script>
    (function($){
        var frame;
        $('#qt-img-upload').on('click', function(e){
            e.preventDefault();
            if(frame){ frame.open(); return; }
            frame = wp.media({ title: 'Select Event Card Image', button: { text: 'Use this image' }, multiple: false });
            frame.on('select', function(){
                var att = frame.state().get('selection').first().toJSON();
                $('#qt_event_image_id').val(att.id);
                $('#qt-img-preview').html('<img src="'+att.url+'" style="max-width:200px;height:auto;display:block;border:1px solid #ddd">');
                $('#qt-img-remove').show();
            });
            frame.open();
        });
        $('#qt-img-remove').on('click', function(e){
            e.preventDefault();
            $('#qt_event_image_id').val('');
            $('#qt-img-preview').html('');
            $(this).hide();
        });
    })(jQuery);
    </script>
    <?php
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
    $team   = esc_attr( get_post_meta( $post->ID, 'qt_lb_team',   true ) );
    $venue  = esc_attr( get_post_meta( $post->ID, 'qt_lb_venue',  true ) );
    $status = esc_attr( get_post_meta( $post->ID, 'qt_lb_status', true ) );
    ?>
    <p style="color:#666;font-size:12px;margin-bottom:8px">The post title is the trivia night name (e.g. "Grand Trivia Night · June 2026").</p>
    <table style="width:100%;border-spacing:0 10px">
    <tr><td style="width:140px"><strong>Winning Team</strong></td><td>
    <input type="text" name="qt_lb_team" value="<?php echo $team; ?>" style="width:100%;max-width:320px" placeholder="e.g. The Trivia Titans">
    </td></tr>
    <tr><td><strong>Venue</strong></td><td>
    <input type="text" name="qt_lb_venue" value="<?php echo $venue; ?>" style="width:100%;max-width:320px" placeholder="e.g. Wooden Barrels">
    </td></tr>
    <tr><td><strong>Status</strong></td><td>
    <input type="text" name="qt_lb_status" value="<?php echo $status; ?>" style="width:100%;max-width:320px" placeholder="e.g. Champions · Sold Out · Past Event">
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
        if ( isset( $_POST['qt_event_image_id'] ) ) {
            update_post_meta( $post_id, 'qt_event_image_id', absint( $_POST['qt_event_image_id'] ) );
        }
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
        if ( isset( $_POST['qt_event_expect'] ) ) {
            update_post_meta( $post_id, 'qt_event_expect', sanitize_textarea_field( $_POST['qt_event_expect'] ) );
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
        update_post_meta( $post_id, 'qt_lb_team',   sanitize_text_field( $_POST['qt_lb_team']   ?? '' ) );
        update_post_meta( $post_id, 'qt_lb_venue',  sanitize_text_field( $_POST['qt_lb_venue']  ?? '' ) );
        update_post_meta( $post_id, 'qt_lb_status', sanitize_text_field( $_POST['qt_lb_status'] ?? '' ) );
    }
}
add_action( 'save_post', 'qt_save_meta' );

/* ─── Leaderboard: admin columns ─────────────────────────────── */
add_filter( 'manage_qt_leaderboard_posts_columns', function( $cols ) {
    $cols['lb_team']   = 'Winning Team';
    $cols['lb_venue']  = 'Venue';
    $cols['lb_status'] = 'Status';
    return $cols;
} );
add_action( 'manage_qt_leaderboard_posts_custom_column', function( $col, $post_id ) {
    if ( $col === 'lb_team'   ) echo esc_html( get_post_meta( $post_id, 'qt_lb_team',   true ) );
    if ( $col === 'lb_venue'  ) echo esc_html( get_post_meta( $post_id, 'qt_lb_venue',  true ) );
    if ( $col === 'lb_status' ) echo esc_html( get_post_meta( $post_id, 'qt_lb_status', true ) );
}, 10, 2 );
add_filter( 'manage_edit-qt_leaderboard_sortable_columns', function( $cols ) {
    $cols['lb_team'] = 'lb_team';
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

/* ─── Leads: admin columns ────────────────────────────────────── */
add_filter( 'manage_qt_lead_posts_columns', function( $cols ) {
    unset( $cols['date'] );
    $cols['lead_email']    = 'Email';
    $cols['lead_source']   = 'Source';
    $cols['lead_subscribed'] = 'Subscribed';
    return $cols;
} );
add_action( 'manage_qt_lead_posts_custom_column', function( $col, $post_id ) {
    if ( $col === 'lead_email' ) {
        $email = get_post_meta( $post_id, 'qt_lead_email', true );
        echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
    }
    if ( $col === 'lead_source' )   echo esc_html( get_post_meta( $post_id, 'qt_lead_source', true ) ?: 'newsletter' );
    if ( $col === 'lead_subscribed' ) echo esc_html( get_the_date( 'j M Y', $post_id ) );
}, 10, 2 );

/* ─── Announce meta box on event edit screen ─────────────────── */
function qt_event_announce_meta_box( $post ) {
    $lead_count = (int) wp_count_posts('qt_lead')->publish;
    $last_sent  = get_post_meta( $post->ID, 'qt_announce_last_sent', true );
    $ev_title   = get_the_title( $post->ID ) ?: 'this event';

    $admin_notice = '';
    if ( isset( $_GET['qt_announce'] ) ) {
        if ( $_GET['qt_announce'] === 'sent' ) {
            $n = absint( $_GET['qt_n'] ?? 0 );
            $admin_notice = "<div style='background:#1c3a1c;border:1px solid #3a6b3a;color:#a0d0a0;padding:10px 12px;border-radius:4px;margin-bottom:12px;font-size:13px'>
                ✓ Announcement sent to <strong>{$n}</strong> subscriber" . ( $n !== 1 ? 's' : '' ) . ".</div>";
        } elseif ( $_GET['qt_announce'] === 'none' ) {
            $admin_notice = "<div style='background:#3a2a1c;border:1px solid #6b4a2a;color:#d0b080;padding:10px 12px;border-radius:4px;margin-bottom:12px;font-size:13px'>
                No leads to email yet.</div>";
        } elseif ( $_GET['qt_announce'] === 'error' ) {
            $admin_notice = "<div style='background:#3a1c1c;border:1px solid #6b2a2a;color:#d08080;padding:10px 12px;border-radius:4px;margin-bottom:12px;font-size:13px'>
                ✗ Invalid request — please try again.</div>";
        }
    }

    $default_subject = 'New event: ' . $ev_title;
    $ev_date_raw = get_post_meta( $post->ID, 'qt_event_date', true );
    if ( $ev_date_raw ) $default_subject .= ' — ' . date( 'j F Y', strtotime( $ev_date_raw ) );

    echo $admin_notice;
    ?>
    <p style="margin:0 0 6px;font-size:13px;color:#666">
      <strong style="color:#1d2327"><?php echo esc_html($lead_count); ?></strong> subscriber<?php echo $lead_count !== 1 ? 's' : ''; ?> on your list.
    </p>

    <?php if ($last_sent) : ?>
    <p style="margin:0 0 12px;font-size:12px;color:#999">Last sent: <?php echo esc_html($last_sent); ?></p>
    <?php endif; ?>

    <?php if ($lead_count > 0) : ?>
    <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <?php wp_nonce_field( 'qt_announce_' . $post->ID, 'qt_announce_nonce' ); ?>
        <input type="hidden" name="action"   value="qt_send_announcement">
        <input type="hidden" name="event_id" value="<?php echo esc_attr($post->ID); ?>">
        <label for="qt_announce_subject" style="display:block;font-size:12px;font-weight:600;color:#1d2327;margin-bottom:4px">Email subject</label>
        <input type="text" id="qt_announce_subject" name="qt_announce_subject"
               value="<?php echo esc_attr($default_subject); ?>"
               style="width:100%;margin-bottom:10px;padding:6px 8px;border:1px solid #8c8f94;border-radius:3px;font-size:13px">
        <input type="submit" class="button button-primary" value="Send to all leads →"
               style="background:#e0890d;border-color:#c07009;color:#fff"
               onclick="return confirm('Send this announcement to <?php echo esc_js($lead_count); ?> subscriber<?php echo $lead_count !== 1 ? 's' : ''; ?>?')">
    </form>
    <?php else : ?>
    <p style="font-size:13px;color:#999;margin:0">No subscribers yet — share the newsletter link to build your list.</p>
    <?php endif; ?>
    <?php
}

/* ─── Handle announcement send ───────────────────────────────── */
add_action( 'admin_post_qt_send_announcement', function() {
    $event_id = absint( $_POST['event_id'] ?? 0 );
    if ( ! $event_id || ! current_user_can( 'edit_post', $event_id )
         || ! isset( $_POST['qt_announce_nonce'] )
         || ! wp_verify_nonce( $_POST['qt_announce_nonce'], 'qt_announce_' . $event_id ) ) {
        wp_redirect( add_query_arg( 'qt_announce', 'error', get_edit_post_link( $event_id, 'url' ) ) );
        exit;
    }

    $subject = sanitize_text_field( $_POST['qt_announce_subject'] ?? '' );
    $leads   = get_posts( [
        'post_type'      => 'qt_lead',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ] );

    if ( empty( $leads ) ) {
        wp_redirect( add_query_arg( 'qt_announce', 'none', get_edit_post_link( $event_id, 'url' ) ) );
        exit;
    }

    $email_data = qt_email_announcement( $event_id, $subject );
    $headers    = [ 'Content-Type: text/html; charset=UTF-8' ];
    $from_name  = get_bloginfo('name') ?: 'QUIZTOPIA_KE';
    $from_email = get_bloginfo('admin_email');
    $headers[]  = "From: {$from_name} <{$from_email}>";

    $sent = 0;
    foreach ( $leads as $lead_id ) {
        $email = get_post_meta( $lead_id, 'qt_lead_email', true );
        if ( ! is_email( $email ) ) continue;
        if ( wp_mail( $email, $email_data['subject'], $email_data['html'], $headers ) ) {
            $sent++;
        }
    }

    update_post_meta( $event_id, 'qt_announce_last_sent', current_time('j M Y, H:i') );

    wp_redirect( add_query_arg( [ 'qt_announce' => 'sent', 'qt_n' => $sent ], get_edit_post_link( $event_id, 'url' ) ) );
    exit;
} );
