<?php get_header(); ?>

<?php while (have_posts()) : the_post();
  $ev_id       = get_the_ID();
  $ev_date_raw = get_post_meta($ev_id, 'qt_event_date', true);
  $ev_date_f   = $ev_date_raw ? date('l, j F Y', strtotime($ev_date_raw)) : '';
  $ev_date_d   = $ev_date_raw ? date('j F', strtotime($ev_date_raw)) : '';
  $ev_time     = get_post_meta($ev_id, 'qt_event_time', true);
  $ev_venue    = get_post_meta($ev_id, 'qt_event_venue', true);
  $ev_price    = get_post_meta($ev_id, 'qt_event_price', true);
  $ev_seats    = get_post_meta($ev_id, 'qt_event_seats_left', true);
  $ev_theme    = get_post_meta($ev_id, 'qt_event_theme', true);
  $ev_badge    = get_post_meta($ev_id, 'qt_event_badge', true);
  $ev_status   = get_post_meta($ev_id, 'qt_event_status', true);
  $ev_wc_id    = get_post_meta($ev_id, 'qt_event_wc_product_id', true);
  $ev_thumb    = get_the_post_thumbnail_url($ev_id, 'full');
  $ev_excerpt  = get_the_excerpt();
  $buy_url     = $ev_wc_id ? get_permalink($ev_wc_id) : (function_exists('WC') ? wc_get_page_permalink('shop') : '/shop');
  $is_sold_out = ($ev_status === 'sold-out');
  $is_soon     = ($ev_status === 'coming-soon');
?>

<article class="qt-event-single">

  <!-- ─── Hero ─────────────────────────────────────────────────── -->
  <div class="qt-event-single__hero"
       <?php if ($ev_thumb) : ?>style="background-image:url('<?php echo esc_url($ev_thumb); ?>')"<?php endif; ?>>
    <div class="qt-event-single__hero-overlay"></div>
    <div class="qt-event-single__hero-content qt-container">
      <div class="qt-event-single__hero-top">
        <?php if ($ev_badge) : ?>
        <span class="qt-event-single__badge qt-hero-in qt-hero-in--d1"><?php echo esc_html($ev_badge); ?></span>
        <?php endif; ?>
        <?php if ($is_sold_out) : ?>
        <span class="qt-event-single__status-tag qt-event-single__status-tag--out qt-hero-in qt-hero-in--d1">Sold out</span>
        <?php elseif ($is_soon) : ?>
        <span class="qt-event-single__status-tag qt-hero-in qt-hero-in--d1">Coming soon</span>
        <?php endif; ?>
      </div>
      <h1 class="qt-event-single__title qt-hero-in qt-hero-in--d2"><?php the_title(); ?></h1>
      <?php if ($ev_date_f) : ?>
      <div class="qt-event-single__date-chip qt-hero-in qt-hero-in--d3">
        <?php echo esc_html($ev_date_f); ?>
        <?php if ($ev_time) : ?>&nbsp;&middot;&nbsp;<?php echo esc_html($ev_time); ?><?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ─── Meta strip ───────────────────────────────────────────── -->
  <?php if ($ev_date_f || $ev_venue || $ev_theme || $ev_seats) : ?>
  <div class="qt-event-single__meta-strip">
    <div class="qt-container">
      <dl class="qt-event-single__meta-list">
        <?php if ($ev_date_f) : ?>
        <div class="qt-event-single__meta-item" data-animate>
          <dt>Date</dt>
          <dd><?php echo esc_html($ev_date_f); ?></dd>
        </div>
        <?php endif; ?>
        <?php if ($ev_time) : ?>
        <div class="qt-event-single__meta-item" data-animate>
          <dt>Doors open</dt>
          <dd><?php echo esc_html($ev_time); ?></dd>
        </div>
        <?php endif; ?>
        <?php if ($ev_venue) : ?>
        <div class="qt-event-single__meta-item" data-animate>
          <dt>Venue</dt>
          <dd><?php echo esc_html($ev_venue); ?></dd>
        </div>
        <?php endif; ?>
        <?php if ($ev_theme) : ?>
        <div class="qt-event-single__meta-item" data-animate>
          <dt>Theme</dt>
          <dd><?php echo esc_html($ev_theme); ?></dd>
        </div>
        <?php endif; ?>
        <?php if ($ev_seats && !$is_sold_out) : ?>
        <div class="qt-event-single__meta-item qt-event-single__meta-item--seats" data-animate>
          <dt>Seats left</dt>
          <dd><?php echo esc_html($ev_seats); ?></dd>
        </div>
        <?php endif; ?>
      </dl>
    </div>
  </div>
  <?php endif; ?>

  <!-- ─── Body ─────────────────────────────────────────────────── -->
  <div class="qt-container">
    <div class="qt-event-single__body">

      <!-- Left: editorial write-up -->
      <div class="qt-event-single__editorial">

        <?php if ($ev_excerpt) : ?>
        <p class="qt-event-single__lead" data-animate><?php echo esc_html($ev_excerpt); ?></p>
        <?php endif; ?>

        <?php if (get_the_content()) : ?>
        <div class="qt-event-single__prose" data-animate>
          <?php the_content(); ?>
        </div>
        <?php else : ?>
        <div class="qt-event-single__prose" data-animate>
          <p>Join us for another unforgettable night of live trivia at <?php echo esc_html($ev_venue ?: 'our venue'); ?>. Gather your team of 4–6, grab a drink, and get ready for ten rounds of competitive, culture-heavy fun.</p>
          <p>Expect questions that test your pop culture instincts, local knowledge, sports trivia, science nerd-outs, and a few wildcards that nobody sees coming. Our host keeps the energy high, the music going between rounds, and the scores close until the final answer.</p>
        </div>
        <?php endif; ?>

        <div class="qt-event-single__expect" data-animate>
          <h2 class="qt-event-single__expect-title">What to expect</h2>
          <ul class="qt-event-single__expect-list">
            <li>10 competitive rounds of live-hosted trivia</li>
            <li>Prizes for the top 3 teams — and bragging rights, forever</li>
            <li>Best in a team of 4–6 (solo players welcome)</li>
            <li>Full bar access — no extra cover beyond your ticket</li>
            <?php if ($ev_theme) : ?><li>Tonight's theme: <strong><?php echo esc_html($ev_theme); ?></strong></li><?php endif; ?>
          </ul>
        </div>

      </div>

      <!-- Right: sticky ticket sidebar -->
      <aside class="qt-event-single__sidebar">

        <div class="qt-event-single__ticket-box" data-animate="fade-left">

          <?php if ($is_sold_out) : ?>
            <p class="qt-event-single__ticket-status qt-event-single__ticket-status--out">Sold out</p>
            <p class="qt-event-single__ticket-sub">This event is fully booked.</p>
            <a href="#newsletter" class="qt-btn-primary" style="width:100%;justify-content:center;margin-bottom:var(--qt-md)">
              Notify me for the next one
            </a>

          <?php elseif ($is_soon) : ?>
            <p class="qt-event-single__ticket-status">Coming soon</p>
            <p class="qt-event-single__ticket-sub">Tickets not yet on sale.</p>
            <a href="#newsletter" class="qt-btn-primary" style="width:100%;justify-content:center;margin-bottom:var(--qt-md)">
              Notify me when live
            </a>

          <?php else : ?>
            <?php if ($ev_price) : ?>
            <p class="qt-event-single__ticket-price">KES <?php echo esc_html(number_format((float)$ev_price)); ?></p>
            <p class="qt-event-single__ticket-per">per person &middot; entry included</p>
            <?php endif; ?>
            <a href="<?php echo esc_url($buy_url); ?>" class="qt-btn-primary" style="width:100%;justify-content:center;margin-bottom:var(--qt-md)">
              Book now
            </a>
          <?php endif; ?>

          <p class="qt-event-single__ticket-note">Non-refundable but transferable.</p>

          <?php if ($ev_date_f || $ev_venue) : ?>
          <ul class="qt-event-single__ticket-details">
            <?php if ($ev_date_f) : ?><li><span>Date</span><?php echo esc_html($ev_date_f); ?></li><?php endif; ?>
            <?php if ($ev_time) : ?><li><span>Time</span><?php echo esc_html($ev_time); ?></li><?php endif; ?>
            <?php if ($ev_venue) : ?><li><span>Venue</span><?php echo esc_html($ev_venue); ?></li><?php endif; ?>
          </ul>
          <?php endif; ?>

        </div>

        <p class="qt-event-single__team-note" data-animate="fade-in">
          Trivia is a team sport. Bring 4–6 friends and take the crown.
        </p>

      </aside>

    </div>
  </div>

  <!-- ─── More events ───────────────────────────────────────────── -->
  <?php
  $qt_more = new WP_Query([
    'post_type'      => 'qt_event',
    'posts_per_page' => 3,
    'post__not_in'   => [$ev_id],
    'meta_key'       => 'qt_event_date',
    'orderby'        => 'meta_value',
    'order'          => 'ASC',
    'meta_query'     => [[
      'key'     => 'qt_event_date',
      'value'   => current_time('Y-m-d'),
      'compare' => '>=',
      'type'    => 'DATE',
    ]],
  ]);
  if ($qt_more->have_posts()) :
  ?>
  <section class="qt-event-single__more">
    <div class="qt-container">
      <div class="qt-event-single__more-head" data-animate>
        <span class="qt-label">More nights</span>
        <h2 class="qt-event-single__more-title">Also coming up</h2>
      </div>
      <div class="qt-event-single__more-grid" data-stagger>
        <?php while ($qt_more->have_posts()) : $qt_more->the_post();
          $m_id     = get_the_ID();
          $m_date   = get_post_meta($m_id, 'qt_event_date', true);
          $m_date_d = $m_date ? date('j F', strtotime($m_date)) : '';
          $m_venue  = get_post_meta($m_id, 'qt_event_venue_short', true)
                   ?: get_post_meta($m_id, 'qt_event_venue', true);
          $m_price  = get_post_meta($m_id, 'qt_event_price', true);
          $m_thumb  = get_the_post_thumbnail_url($m_id, 'medium_large');
        ?>
        <a href="<?php the_permalink(); ?>" class="qt-event-mini-card" data-animate>
          <div class="qt-event-mini-card__photo"
               <?php if ($m_thumb) : ?>style="background-image:url('<?php echo esc_url($m_thumb); ?>')"<?php endif; ?>>
          </div>
          <div class="qt-event-mini-card__body">
            <?php if ($m_date_d) : ?>
            <span class="qt-event-mini-card__date"><?php echo esc_html($m_date_d); ?></span>
            <?php endif; ?>
            <h3 class="qt-event-mini-card__title"><?php the_title(); ?></h3>
            <?php if ($m_venue) : ?>
            <span class="qt-event-mini-card__venue"><?php echo esc_html($m_venue); ?></span>
            <?php endif; ?>
            <?php if ($m_price) : ?>
            <span class="qt-event-mini-card__price">KES <?php echo esc_html(number_format((float)$m_price)); ?></span>
            <?php endif; ?>
          </div>
        </a>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
