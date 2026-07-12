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
        'show_in_rest'       => false,
        'has_archive'        => true,
        'rewrite'            => [ 'slug' => 'events' ],
        'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
        'menu_icon'          => 'dashicons-calendar-alt',
        'menu_position'      => 5,
    ] );

    register_post_type( 'qt_gallery', [
        'labels' => [
            'name'               => 'Galleries',
            'singular_name'      => 'Gallery',
            'add_new_item'       => 'Add New Gallery',
            'edit_item'          => 'Edit Gallery',
            'new_item'           => 'New Gallery',
            'view_item'          => 'View Gallery',
            'search_items'       => 'Search Galleries',
            'not_found'          => 'No galleries found',
            'not_found_in_trash' => 'No galleries in trash',
            'menu_name'          => 'Galleries',
        ],
        'public'        => true,
        'show_in_rest'  => false,
        'has_archive'   => true,
        'rewrite'       => [ 'slug' => 'gallery' ],
        'supports'      => [ 'title' ],
        'menu_icon'     => 'dashicons-format-gallery',
        'menu_position' => 6,
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
        'show_in_rest' => false,
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
        'show_in_rest' => false,
        'supports'     => [ 'title' ],
        'menu_icon'    => 'dashicons-editor-help',
        'menu_position'=> 7,
    ] );

    register_post_type( 'qt_leaderboard', [
        'labels' => [
            'name'               => 'Leaderboards',
            'singular_name'      => 'Leaderboard',
            'add_new_item'       => 'Add New Leaderboard',
            'edit_item'          => 'Edit Leaderboard',
            'new_item'           => 'New Leaderboard',
            'view_item'          => 'View Leaderboard',
            'search_items'       => 'Search Leaderboards',
            'not_found'          => 'No leaderboards found',
            'not_found_in_trash' => 'No leaderboards in trash',
            'menu_name'          => 'Leaderboard',
        ],
        'public'        => true,
        'show_in_rest'  => false,
        'has_archive'   => true,
        'rewrite'       => [ 'slug' => 'leaderboard' ],
        'supports'      => [ 'title' ],
        'menu_icon'     => 'dashicons-awards',
        'menu_position' => 8,
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

/* ─── Use classic editor for meta-box driven CPTs ────────────── */
add_filter( 'use_block_editor_for_post_type', function( $use_block_editor, $post_type ) {
    if ( in_array( $post_type, [ 'qt_event', 'qt_gallery', 'qt_testimonial', 'qt_faq', 'qt_leaderboard' ], true ) ) {
        return false;
    }

    return $use_block_editor;
}, 10, 2 );

/* ─── Flush rewrite rules once after CPT slugs change ────────── */
add_action( 'init', function() {
    if ( get_option( 'qt_rewrite_flushed_v2' ) !== '1' ) {
        flush_rewrite_rules( false );
        update_option( 'qt_rewrite_flushed_v2', '1' );
    }
}, 20 );

/* ─── Helper: gallery attachment IDs (ordered) ───────────────── */
function qt_get_gallery_image_ids( $post_id ) {
    $raw = get_post_meta( $post_id, 'qt_gallery_images', true );
    if ( ! $raw ) {
        return [];
    }
    return array_values( array_filter( array_map( 'absint', explode( ',', $raw ) ) ) );
}

/* ─── Helper: leaderboard teams (ranked order) ───────────────── */
function qt_get_leaderboard_teams( $post_id ) {
    $teams = get_post_meta( $post_id, 'qt_lb_teams', true );

    if ( is_array( $teams ) && ! empty( $teams ) ) {
        $clean = [];
        foreach ( $teams as $t ) {
            $name = isset( $t['name'] ) ? (string) $t['name'] : '';
            $note = isset( $t['note'] ) ? (string) $t['note'] : '';
            if ( $name === '' && $note === '' ) {
                continue;
            }
            $clean[] = [ 'name' => $name, 'note' => $note ];
        }
        return $clean;
    }

    /* Legacy single-team fallback */
    $legacy_team = get_post_meta( $post_id, 'qt_lb_team', true );
    if ( $legacy_team ) {
        return [ [
            'name' => $legacy_team,
            'note' => get_post_meta( $post_id, 'qt_lb_status', true ),
        ] ];
    }

    return [];
}

/* ─── Helper: leaderboard subtitle (venue / date line) ───────── */
function qt_get_leaderboard_subtitle( $post_id ) {
    return get_post_meta( $post_id, 'qt_lb_subtitle', true )
        ?: get_post_meta( $post_id, 'qt_lb_venue', true );
}

/* ─── Render one leaderboard group (title + ranked teams) ────── */
function qt_render_leaderboard_group( $post_id ) {
    $subtitle = qt_get_leaderboard_subtitle( $post_id );
    $teams    = qt_get_leaderboard_teams( $post_id );
    ?>
    <div class="qt-lb-group" data-animate>
      <div class="qt-lb-group__head">
        <h3 class="qt-lb-group__title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
        <?php if ( $subtitle ) : ?><span class="qt-lb-group__sub"><?php echo esc_html( $subtitle ); ?></span><?php endif; ?>
      </div>
      <?php if ( $teams ) : ?>
      <ol class="qt-lb-list">
        <?php foreach ( $teams as $rank => $team ) : ?>
        <li class="qt-lb-row<?php echo $rank === 0 ? ' qt-lb-row--lead' : ''; ?>">
          <span class="qt-lb-rank"><?php echo esc_html( $rank + 1 ); ?></span>
          <span class="qt-lb-name"><?php echo esc_html( $team['name'] ); ?></span>
          <?php if ( ! empty( $team['note'] ) ) : ?><span class="qt-lb-note"><?php echo esc_html( $team['note'] ); ?></span><?php endif; ?>
        </li>
        <?php endforeach; ?>
      </ol>
      <?php else : ?>
      <p class="qt-lb-group__empty">No teams added yet.</p>
      <?php endif; ?>
    </div>
    <?php
}

/* ═══════════════════════════════════════════════════════════════
   META BOXES
   ══════════════════════════════════════════════════════════════ */
function qt_add_meta_boxes() {

    add_meta_box( 'qt_event_details', 'Event Details',
        'qt_event_meta_box', 'qt_event', 'normal', 'high' );

    add_meta_box( 'qt_event_announce', 'Email Leads',
        'qt_event_announce_meta_box', 'qt_event', 'side', 'high' );

    add_meta_box( 'qt_gallery_details', 'Gallery Images',
        'qt_gallery_meta_box', 'qt_gallery', 'normal', 'high' );

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
    if ( in_array( $hook, [ 'post.php', 'post-new.php' ] ) && isset( $post ) ) {
        if ( in_array( $post->post_type, [ 'qt_event', 'qt_gallery' ], true ) ) {
            wp_enqueue_media();
        }
        if ( in_array( $post->post_type, [ 'qt_gallery', 'qt_leaderboard' ], true ) ) {
            wp_enqueue_script( 'jquery-ui-sortable' );
        }
    }
} );

/* ─── Sortable placeholder + drag styles (admin) ─────────────── */
add_action( 'admin_head', function() {
    $screen = get_current_screen();
    if ( ! $screen || ! in_array( $screen->post_type, [ 'qt_gallery', 'qt_leaderboard' ], true ) ) {
        return;
    }
    echo '<style>
        .qt-sort-placeholder { border: 2px dashed #c3c4c7 !important; background: #f0f0f1; border-radius: 4px; visibility: visible !important; }
        .qt-gallery-thumb.qt-sort-placeholder { width: 90px; height: 90px; }
        .qt-lb-row-edit.qt-sort-placeholder { height: 46px; }
        .ui-sortable-helper { box-shadow: 0 6px 18px rgba(0,0,0,.18); }
    </style>';
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

/* ─── Gallery meta box ────────────────────────────────────────── */
function qt_gallery_meta_box( $post ) {
    wp_nonce_field( 'qt_gallery_save', 'qt_gallery_nonce' );

    $subtitle = esc_attr( get_post_meta( $post->ID, 'qt_gallery_subtitle', true ) );
    $ids      = qt_get_gallery_image_ids( $post->ID );
    ?>
    <p style="color:#666;font-size:12px;margin:0 0 12px">The post <strong>title</strong> is the gallery name — usually the event it belongs to (e.g. "Grand Trivia Night · June 2026").</p>

    <table style="width:100%;border-spacing:0 12px">
      <tr>
        <td style="width:180px;padding-right:16px;vertical-align:top;padding-top:6px"><label for="qt_gallery_subtitle"><strong>Subtitle</strong></label></td>
        <td>
          <input type="text" name="qt_gallery_subtitle" id="qt_gallery_subtitle" value="<?php echo $subtitle; ?>" style="width:100%;max-width:480px" placeholder="e.g. April 2026 · Wooden Barrels, Nairobi">
          <p style="color:#666;font-size:12px;margin:4px 0 0">Optional date / venue line shown beneath the title.</p>
        </td>
      </tr>
    </table>

    <hr style="margin:8px 0 12px">

    <p style="margin:0 0 8px"><strong>Photos</strong>
      <span style="color:#666;font-size:12px;font-weight:400;margin-left:6px">Click <em>Add Photos</em> to pick images. Drag thumbnails to reorder.</span>
    </p>

    <input type="hidden" name="qt_gallery_images" id="qt-gallery-images" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>">

    <div id="qt-gallery-previews" style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px;min-height:10px">
      <?php foreach ( $ids as $id ) :
        $thumb = wp_get_attachment_image_url( $id, 'thumbnail' );
        if ( ! $thumb ) continue;
      ?>
      <div class="qt-gallery-thumb" data-id="<?php echo esc_attr( $id ); ?>" style="position:relative;width:90px;height:90px;border:1px solid #ddd;border-radius:4px;overflow:hidden;cursor:move;background:#f6f7f7">
        <img src="<?php echo esc_url( $thumb ); ?>" style="width:100%;height:100%;object-fit:cover;display:block;pointer-events:none">
        <button type="button" class="qt-gallery-thumb-remove" aria-label="Remove" style="position:absolute;top:2px;right:2px;width:20px;height:20px;line-height:18px;text-align:center;border:none;border-radius:50%;background:rgba(0,0,0,.65);color:#fff;cursor:pointer;font-size:14px">&times;</button>
      </div>
      <?php endforeach; ?>
    </div>

    <button type="button" class="button button-primary" id="qt-gallery-add">Add Photos</button>
    <button type="button" class="button" id="qt-gallery-clear"<?php echo $ids ? '' : ' style="display:none"'; ?>>Clear All</button>

    <script>
    jQuery(function($){
      var frame;
      var $previews = $('#qt-gallery-previews');
      var $field    = $('#qt-gallery-images');
      var $clear    = $('#qt-gallery-clear');

      function sync(){
        var ids = [];
        $previews.find('.qt-gallery-thumb').each(function(){ ids.push($(this).data('id')); });
        $field.val(ids.join(','));
        $clear.toggle(ids.length > 0);
      }

      function addThumb(id, url){
        if ($previews.find('.qt-gallery-thumb[data-id="'+id+'"]').length) return;
        var $t = $('<div class="qt-gallery-thumb" data-id="'+id+'" style="position:relative;width:90px;height:90px;border:1px solid #ddd;border-radius:4px;overflow:hidden;cursor:move;background:#f6f7f7">'
          + '<img src="'+url+'" style="width:100%;height:100%;object-fit:cover;display:block;pointer-events:none">'
          + '<button type="button" class="qt-gallery-thumb-remove" aria-label="Remove" style="position:absolute;top:2px;right:2px;width:20px;height:20px;line-height:18px;text-align:center;border:none;border-radius:50%;background:rgba(0,0,0,.65);color:#fff;cursor:pointer;font-size:14px">&times;</button>'
          + '</div>');
        $previews.append($t);
      }

      $('#qt-gallery-add').on('click', function(e){
        e.preventDefault();
        if (frame){ frame.open(); return; }
        frame = wp.media({ title: 'Select gallery photos', button: { text: 'Add to gallery' }, multiple: 'add', library: { type: 'image' } });
        frame.on('select', function(){
          frame.state().get('selection').each(function(att){
            att = att.toJSON();
            var url = (att.sizes && att.sizes.thumbnail) ? att.sizes.thumbnail.url : att.url;
            addThumb(att.id, url);
          });
          sync();
        });
        frame.open();
      });

      $previews.on('click', '.qt-gallery-thumb-remove', function(e){
        e.preventDefault();
        $(this).closest('.qt-gallery-thumb').remove();
        sync();
      });

      $('#qt-gallery-clear').on('click', function(e){
        e.preventDefault();
        $previews.empty();
        sync();
      });

      if ($.fn.sortable){
        $previews.sortable({
          items: '.qt-gallery-thumb',
          tolerance: 'pointer',
          cursor: 'move',
          forcePlaceholderSize: true,
          placeholder: 'qt-sort-placeholder',
          update: sync
        });
      }
    });
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

    $subtitle = esc_attr( qt_get_leaderboard_subtitle( $post->ID ) );
    $teams    = qt_get_leaderboard_teams( $post->ID );
    if ( empty( $teams ) ) {
        $teams = [ [ 'name' => '', 'note' => '' ] ];
    }

    $render_row = function( $name, $note ) {
        ?>
        <div class="qt-lb-row-edit" style="display:flex;gap:8px;align-items:center;background:#f6f7f7;border:1px solid #dcdcde;border-radius:4px;padding:8px 10px">
          <span class="qt-lb-handle" title="Drag to reorder" style="cursor:move;color:#8c8f94;font-size:18px;line-height:1;user-select:none">&#9776;</span>
          <span class="qt-lb-rank" style="min-width:22px;text-align:center;font-weight:700;color:#e0890d"></span>
          <input type="text" name="qt_lb_team_name[]" value="<?php echo esc_attr( $name ); ?>" placeholder="Team name" style="flex:2 1 180px;min-width:0">
          <input type="text" name="qt_lb_team_note[]" value="<?php echo esc_attr( $note ); ?>" placeholder="Note / score / status (optional)" style="flex:2 1 180px;min-width:0">
          <button type="button" class="qt-lb-remove button-link" aria-label="Remove team" style="color:#b32d2e;text-decoration:none;font-size:20px;line-height:1;cursor:pointer">&times;</button>
        </div>
        <?php
    };
    ?>
    <p style="color:#666;font-size:12px;margin:0 0 12px">The post <strong>title</strong> is the event / trivia night name (e.g. "Grand Trivia Night · June 2026").</p>

    <table style="width:100%;border-spacing:0 12px">
      <tr>
        <td style="width:160px;padding-right:16px;vertical-align:top;padding-top:6px"><label for="qt_lb_subtitle"><strong>Subtitle</strong></label></td>
        <td>
          <input type="text" name="qt_lb_subtitle" id="qt_lb_subtitle" value="<?php echo $subtitle; ?>" style="width:100%;max-width:480px" placeholder="e.g. Wooden Barrels · June 2026">
          <p style="color:#666;font-size:12px;margin:4px 0 0">Optional venue / date line shown under the title.</p>
        </td>
      </tr>
    </table>

    <hr style="margin:8px 0 12px">

    <p style="margin:0 0 8px"><strong>Teams</strong>
      <span style="color:#666;font-size:12px;font-weight:400;margin-left:6px">Best team first. Drag the <span style="font-size:14px">&#9776;</span> handle to reorder — the rank number updates automatically.</span>
    </p>

    <div id="qt-lb-rows" style="display:flex;flex-direction:column;gap:8px;margin-bottom:12px">
      <?php foreach ( $teams as $t ) { $render_row( $t['name'], $t['note'] ); } ?>
    </div>

    <template id="qt-lb-row-tpl"><?php $render_row( '', '' ); ?></template>

    <button type="button" class="button button-primary" id="qt-lb-add">+ Add Team</button>

    <script>
    jQuery(function($){
      var $rows = $('#qt-lb-rows');
      var tpl   = document.getElementById('qt-lb-row-tpl');

      function renumber(){
        $rows.children('.qt-lb-row-edit').each(function(i){
          $(this).find('.qt-lb-rank').text(i + 1);
        });
      }

      $('#qt-lb-add').on('click', function(e){
        e.preventDefault();
        var node = tpl.content.firstElementChild.cloneNode(true);
        $rows.append(node);
        renumber();
        $(node).find('input').first().focus();
      });

      $rows.on('click', '.qt-lb-remove', function(e){
        e.preventDefault();
        if ($rows.children('.qt-lb-row-edit').length <= 1){
          $(this).closest('.qt-lb-row-edit').find('input').val('');
        } else {
          $(this).closest('.qt-lb-row-edit').remove();
        }
        renumber();
      });

      if ($.fn.sortable){
        $rows.sortable({
          handle: '.qt-lb-handle',
          items: '.qt-lb-row-edit',
          tolerance: 'pointer',
          axis: 'y',
          cursor: 'move',
          forcePlaceholderSize: true,
          placeholder: 'qt-sort-placeholder',
          update: renumber
        });
      }

      renumber();
    });
    </script>
    <?php
}

/* ─── Save meta ───────────────────────────────────────────────── */
function qt_save_meta( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( wp_is_post_autosave( $post_id ) ) return;
    if ( $parent_id = wp_is_post_revision( $post_id ) ) {
        $post_id = $parent_id;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // Event
    if ( isset( $_POST['qt_event_nonce'] ) && wp_verify_nonce( $_POST['qt_event_nonce'], 'qt_event_save' ) ) {
        if ( isset( $_POST['qt_event_image_id'] ) ) {
            $image_id = absint( $_POST['qt_event_image_id'] );
            if ( $image_id ) {
                update_post_meta( $post_id, 'qt_event_image_id', $image_id );
            } else {
                delete_post_meta( $post_id, 'qt_event_image_id' );
            }
        }

        $event_fields = [
            'qt_event_time',
            'qt_event_venue',
            'qt_event_venue_short',
            'qt_event_price',
            'qt_event_seats_left',
            'qt_event_theme',
            'qt_event_badge',
            'qt_event_status',
        ];

        foreach ( $event_fields as $field ) {
            if ( isset( $_POST[ $field ] ) ) {
                update_post_meta( $post_id, $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
            }
        }

        $event_date = qt_normalize_event_date( wp_unslash( $_POST['qt_event_date'] ?? '' ) );
        if ( $event_date ) {
            update_post_meta( $post_id, 'qt_event_date', $event_date );
        } else {
            delete_post_meta( $post_id, 'qt_event_date' );
        }

        $ticket_reference = sanitize_text_field( wp_unslash( $_POST['qt_event_ticket_url'] ?? '' ) );
        if ( $ticket_reference !== '' ) {
            update_post_meta( $post_id, 'qt_event_ticket_url', $ticket_reference );
        } else {
            delete_post_meta( $post_id, 'qt_event_ticket_url' );
        }

        $submitted_product_id = absint( $_POST['qt_event_wc_product_id'] ?? 0 );
        $resolved_target      = $ticket_reference !== '' ? qt_resolve_event_ticket_reference( $ticket_reference ) : [ 'product_id' => 0 ];
        $resolved_product_id  = 0;

        if ( $submitted_product_id && get_post_type( $submitted_product_id ) === 'product' ) {
            $resolved_product_id = $submitted_product_id;
        } elseif ( ! empty( $resolved_target['product_id'] ) ) {
            $resolved_product_id = absint( $resolved_target['product_id'] );
        }

        if ( $resolved_product_id ) {
            update_post_meta( $post_id, 'qt_event_wc_product_id', $resolved_product_id );
        } else {
            delete_post_meta( $post_id, 'qt_event_wc_product_id' );
        }

        if ( isset( $_POST['qt_event_expect'] ) ) {
            update_post_meta( $post_id, 'qt_event_expect', sanitize_textarea_field( wp_unslash( $_POST['qt_event_expect'] ) ) );
        }
    }

    // Gallery
    if ( isset( $_POST['qt_gallery_nonce'] ) && wp_verify_nonce( $_POST['qt_gallery_nonce'], 'qt_gallery_save' ) ) {
        update_post_meta( $post_id, 'qt_gallery_subtitle', sanitize_text_field( wp_unslash( $_POST['qt_gallery_subtitle'] ?? '' ) ) );

        $raw_ids = wp_unslash( $_POST['qt_gallery_images'] ?? '' );
        $ids     = array_values( array_filter( array_map( 'absint', explode( ',', $raw_ids ) ) ) );
        if ( $ids ) {
            update_post_meta( $post_id, 'qt_gallery_images', implode( ',', $ids ) );
        } else {
            delete_post_meta( $post_id, 'qt_gallery_images' );
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
        update_post_meta( $post_id, 'qt_lb_subtitle', sanitize_text_field( wp_unslash( $_POST['qt_lb_subtitle'] ?? '' ) ) );

        $names = (array) wp_unslash( $_POST['qt_lb_team_name'] ?? [] );
        $notes = (array) wp_unslash( $_POST['qt_lb_team_note'] ?? [] );
        $teams = [];
        foreach ( $names as $i => $name ) {
            $name = sanitize_text_field( $name );
            $note = sanitize_text_field( $notes[ $i ] ?? '' );
            if ( $name === '' && $note === '' ) {
                continue;
            }
            $teams[] = [ 'name' => $name, 'note' => $note ];
        }

        if ( $teams ) {
            update_post_meta( $post_id, 'qt_lb_teams', $teams );
        } else {
            delete_post_meta( $post_id, 'qt_lb_teams' );
        }

        // Retire legacy single-team meta once migrated to the new structure.
        delete_post_meta( $post_id, 'qt_lb_team' );
        delete_post_meta( $post_id, 'qt_lb_status' );
        delete_post_meta( $post_id, 'qt_lb_venue' );
    }
}
add_action( 'save_post', 'qt_save_meta' );

/* ─── Leaderboard: admin columns ─────────────────────────────── */
add_filter( 'manage_qt_leaderboard_posts_columns', function( $cols ) {
    $date = $cols['date'] ?? null;
    unset( $cols['date'] );
    $cols['lb_subtitle'] = 'Subtitle';
    $cols['lb_teams']    = 'Teams';
    if ( $date ) {
        $cols['date'] = $date;
    }
    return $cols;
} );
add_action( 'manage_qt_leaderboard_posts_custom_column', function( $col, $post_id ) {
    if ( $col === 'lb_subtitle' ) {
        echo esc_html( qt_get_leaderboard_subtitle( $post_id ) );
    }
    if ( $col === 'lb_teams' ) {
        $teams = qt_get_leaderboard_teams( $post_id );
        $names = array_slice( wp_list_pluck( $teams, 'name' ), 0, 3 );
        $label = $names ? implode( ', ', array_filter( $names ) ) : '—';
        if ( count( $teams ) > 3 ) {
            $label .= ' +' . ( count( $teams ) - 3 );
        }
        echo esc_html( $label );
    }
}, 10, 2 );

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

/* ─── Galleries: photo count admin column ────────────────────── */
add_filter( 'manage_qt_gallery_posts_columns', function( $cols ) {
    $date = $cols['date'] ?? null;
    unset( $cols['date'] );
    $cols['gallery_photos'] = 'Photos';
    if ( $date ) {
        $cols['date'] = $date;
    }
    return $cols;
} );
add_action( 'manage_qt_gallery_posts_custom_column', function( $col, $post_id ) {
    if ( $col === 'gallery_photos' ) {
        echo esc_html( count( qt_get_gallery_image_ids( $post_id ) ) );
    }
}, 10, 2 );

/* ─── Announce meta box on event edit screen ─────────────────── */
function qt_event_announce_meta_box( $post ) {
    $lead_count = (int) wp_count_posts('qt_lead')->publish;
    $last_sent  = get_post_meta( $post->ID, 'qt_announce_last_sent', true );
    $ev_title   = get_the_title( $post->ID ) ?: 'this event';
    $is_unsaved = ! $post->ID || $post->post_status === 'auto-draft';

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

    <?php if ($is_unsaved) : ?>
    <p style="font-size:13px;color:#999;margin:0">Save the event first, then come back here to email your leads.</p>
    <?php elseif ($lead_count > 0) : ?>
        <?php wp_nonce_field( 'qt_announce_' . $post->ID, 'qt_announce_nonce' ); ?>
        <input type="hidden" name="event_id" value="<?php echo esc_attr($post->ID); ?>">
        <label for="qt_announce_subject" style="display:block;font-size:12px;font-weight:600;color:#1d2327;margin-bottom:4px">Email subject</label>
        <input type="text" id="qt_announce_subject" name="qt_announce_subject"
               value="<?php echo esc_attr($default_subject); ?>"
               style="width:100%;margin-bottom:10px;padding:6px 8px;border:1px solid #8c8f94;border-radius:3px;font-size:13px">
        <button type="submit" class="button button-primary"
               name="action" value="qt_send_announcement"
               formaction="<?php echo esc_url(admin_url('admin-post.php')); ?>"
               formmethod="post" formnovalidate
               style="background:#e0890d;border-color:#c07009;color:#fff"
               onclick="return confirm('Send this announcement to <?php echo esc_js($lead_count); ?> subscriber<?php echo $lead_count !== 1 ? 's' : ''; ?>?')">
          Send to all leads →
        </button>
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
