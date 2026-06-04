<?php get_header(); ?>

<div class="qt-events-archive">
  <div class="qt-container">

    <div class="qt-page-header" style="padding-top:0;padding-bottom:var(--qt-3xl);background:transparent">
      <span class="qt-label" data-animate>QUIZTOPIA_KE</span>
      <h1 class="qt-display qt-display--lg" data-animate style="margin-top:var(--qt-md)">
        All <em style="font-style:normal;color:var(--qt-accent)">Events</em>
      </h1>
    </div>

    <?php if (have_posts()) : ?>
    <div class="qt-events-grid" data-stagger>
    <?php $card_index = 0; while (have_posts()) : the_post();
      $is_featured = ($card_index === 0);
      $ev_id       = get_the_ID();
      $ev_date_raw = get_post_meta($ev_id, 'qt_event_date', true);
      $ev_date_d   = $ev_date_raw ? date('j F', strtotime($ev_date_raw)) : get_the_title();
      $ev_venue    = get_post_meta($ev_id, 'qt_event_venue_short', true) ?: get_post_meta($ev_id, 'qt_event_venue', true);
      $ev_price    = get_post_meta($ev_id, 'qt_event_price', true);
      $ev_seats    = get_post_meta($ev_id, 'qt_event_seats_left', true);
      $ev_badge    = get_post_meta($ev_id, 'qt_event_badge', true);
      $ev_status   = get_post_meta($ev_id, 'qt_event_status', true);
      $ev_wc_id    = get_post_meta($ev_id, 'qt_event_wc_product_id', true);
      $ev_url      = $ev_wc_id ? get_permalink($ev_wc_id) : get_permalink();
      $ev_img_id   = get_post_meta($ev_id, 'qt_event_image_id', true);
      $ev_thumb    = ($ev_img_id ? wp_get_attachment_image_url($ev_img_id, 'large') : '')
                  ?: get_the_post_thumbnail_url($ev_id, 'large')
                  ?: ($ev_wc_id ? get_the_post_thumbnail_url($ev_wc_id, 'large') : '');
      $card_class  = 'qt-event-card' . ($is_featured ? ' qt-event-card--featured' : '');
    ?>
    <article class="<?php echo esc_attr($card_class); ?>" data-animate>
      <a href="<?php echo esc_url(get_permalink()); ?>" class="qt-event-card__photo" style="display:block">
        <div class="qt-event-card__photo-img"
             <?php if ($ev_thumb) : ?>style="background-image:url('<?php echo esc_url($ev_thumb); ?>');background-size:cover;background-position:center"<?php endif; ?>>
        </div>
        <?php if ($ev_badge) : ?><span class="qt-event-card__badge"><?php echo esc_html($ev_badge); ?></span><?php endif; ?>
        <?php if ($ev_seats && $ev_status !== 'sold-out') : ?><span class="qt-event-card__seats"><?php echo esc_html($ev_seats); ?> seats left</span><?php endif; ?>
      </a>

      <div class="qt-event-card__body">
        <div class="qt-event-card__date"><?php echo esc_html($ev_date_d); ?></div>
        <h2 class="qt-event-card__name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <?php if ($ev_venue) : ?><p class="qt-event-card__venue"><?php echo esc_html($ev_venue); ?></p><?php endif; ?>
      </div>

      <div class="qt-event-card__footer">
        <div class="qt-event-card__price">
          <?php if ($ev_status === 'sold-out') : ?>
            <span style="color:var(--qt-muted)">Sold Out</span>
          <?php elseif ($ev_status === 'coming-soon' || !$ev_price) : ?>
            Coming Soon
          <?php else : ?>
            KES <?php echo esc_html(number_format((float)$ev_price)); ?> <span>per person</span>
          <?php endif; ?>
        </div>
        <?php if ($ev_status === 'sold-out') : ?>
          <span class="qt-event-card__btn" style="opacity:.5">Sold Out</span>
        <?php elseif ($ev_status === 'coming-soon') : ?>
          <a href="#newsletter" class="qt-event-card__btn">Notify Me</a>
        <?php else : ?>
          <a href="<?php echo esc_url($ev_url); ?>" class="qt-event-card__btn">Book Now</a>
        <?php endif; ?>
      </div>
    </article>
    <?php $card_index++; endwhile; ?>
    </div>

    <?php the_posts_pagination([
      'mid_size'  => 2,
      'prev_text' => '← Older',
      'next_text' => 'Newer →',
    ]); ?>

    <?php else : ?>
    <p style="color:var(--qt-muted);font-size:var(--qt-text-md);padding:var(--qt-4xl) 0">
      No events scheduled yet. <a href="#newsletter" style="color:var(--qt-accent)">Get notified when dates drop →</a>
    </p>
    <?php endif; ?>

  </div>
</div>

<?php get_footer(); ?>
