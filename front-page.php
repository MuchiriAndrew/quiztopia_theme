<?php get_header(); ?>

<?php
/* ─── Pull next event for hero ─────────────────────────────────── */
$next_event        = qt_get_next_event();
$next_event_date   = $next_event ? get_post_meta($next_event->ID, 'qt_event_date', true) : '';
$next_event_venue  = $next_event ? get_post_meta($next_event->ID, 'qt_event_venue', true) : '';
$next_event_date_f = $next_event_date ? date('j F Y', strtotime($next_event_date)) : '';

/* ─── Customizer values ─────────────────────────────────────────── */
$hero_kicker = qt_opt('qt_hero_kicker', "Nairobi's loudest trivia night");
$hero_line1  = qt_opt('qt_hero_line1', 'Bring your');
$hero_line2  = qt_opt('qt_hero_line2', 'sharpest mind.');
$hero_line3  = qt_opt('qt_hero_line3', 'Or your funniest friend.');
$hero_sub    = qt_opt('qt_hero_sub', 'Live trivia. Real prizes. Ice cold drinks. Every round hits different when you\'re competing with the whole room.');
$hero_cta1   = qt_opt('qt_hero_cta1', 'Get Tickets');
$hero_cta2   = qt_opt('qt_hero_cta2', 'See what happens');
$hero_photo  = qt_opt('qt_hero_photo', '');
$hero_video  = qt_opt('qt_hero_video', '');
$shop_url    = function_exists('WC') ? wc_get_page_permalink('shop') : '/shop';
?>

<!-- ╔══ HERO — Marquee Hero ════════════════════════════════════════╗ -->
<section class="qt-hero" aria-labelledby="qt-hero-headline">
  <div class="qt-hero__bg" aria-hidden="true"></div>

  <?php
  $media_cls = 'qt-hero__media';
  if ($hero_video)     $media_cls .= ' has-video';
  elseif ($hero_photo) $media_cls .= ' has-image';
  ?>
  <div class="<?php echo esc_attr($media_cls); ?>" aria-hidden="true"
       <?php if ($hero_photo && !$hero_video) : ?>style="--hero-photo-url: url('<?php echo esc_url($hero_photo); ?>')"<?php endif; ?>>
    <?php if ($hero_video) : ?>
    <video class="qt-hero__media-video" autoplay muted loop playsinline preload="metadata">
      <source src="<?php echo esc_url($hero_video); ?>" type="video/mp4">
    </video>
    <?php endif; ?>
  </div>

  <div class="qt-hero__inner">

    <div class="qt-hero__kicker qt-hero-in qt-hero-in--d1">
      <span class="qt-hero__kicker-dot" aria-hidden="true"></span>
      <span class="qt-label"><?php echo esc_html($hero_kicker); ?></span>
    </div>

    <h1 id="qt-hero-headline" class="qt-hero__headline qt-hero-in qt-hero-in--d2">
      <?php echo esc_html($hero_line1); ?><br>
      <em><?php echo esc_html($hero_line2); ?></em><br>
      <?php echo esc_html($hero_line3); ?>
    </h1>

    <p class="qt-hero__sub qt-hero-in qt-hero-in--d3"><?php echo esc_html($hero_sub); ?></p>

    <?php if ($next_event_date) : ?>
    <div class="qt-hero__countdown qt-hero-in qt-hero-in--d4" aria-label="Time until next event">
      <div class="qt-countdown-unit">
        <span class="qt-countdown-unit__num" id="qt-cd-days">––</span>
        <span class="qt-countdown-unit__label">Days</span>
      </div>
      <span class="qt-countdown-divider" aria-hidden="true">:</span>
      <div class="qt-countdown-unit">
        <span class="qt-countdown-unit__num" id="qt-cd-hours">––</span>
        <span class="qt-countdown-unit__label">Hours</span>
      </div>
      <span class="qt-countdown-divider" aria-hidden="true">:</span>
      <div class="qt-countdown-unit">
        <span class="qt-countdown-unit__num" id="qt-cd-mins">––</span>
        <span class="qt-countdown-unit__label">Mins</span>
      </div>
      <span class="qt-countdown-divider" aria-hidden="true">:</span>
      <div class="qt-countdown-unit">
        <span class="qt-countdown-unit__num" id="qt-cd-secs">––</span>
        <span class="qt-countdown-unit__label">Secs</span>
      </div>
    </div>
    <?php endif; ?>

    <div class="qt-hero__actions qt-hero-in qt-hero-in--d5">
      <a href="<?php echo esc_url($shop_url); ?>" class="qt-btn-primary"><?php echo esc_html($hero_cta1); ?></a>
      <a href="#experience" class="qt-btn-ghost"><?php echo esc_html($hero_cta2); ?></a>
    </div>

  </div>

  <?php if ($next_event) : ?>
  <div class="qt-hero__venue-strip qt-hero-in--fade qt-hero-in--d6" aria-hidden="true">
    <?php if ($next_event_date_f) : ?>
    <div class="qt-venue-strip__item">
      <strong><?php echo esc_html($next_event_date_f); ?></strong>
      <span>Next Event</span>
    </div>
    <?php endif; ?>
    <?php if ($next_event_venue) : ?>
    <div class="qt-venue-strip__item">
      <strong><?php echo esc_html($next_event_venue); ?></strong>
      <span>Nairobi</span>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

</section>

<hr class="qt-rule">

<!-- ╔══ UPCOMING EVENTS ════════════════════════════════════════════╗ -->
<?php
$events_query = new WP_Query([
  'post_type'      => 'qt_event',
  'posts_per_page' => 3,
  'meta_key'       => 'qt_event_date',
  'orderby'        => 'meta_value',
  'order'          => 'ASC',
  'meta_query'     => [[
    'key'     => 'qt_event_date',
    'value'   => date('Ymd'),
    'compare' => '>=',
    'type'    => 'DATE',
  ]],
]);
?>
<section class="qt-upcoming" id="events" aria-labelledby="qt-events-title">
  <div class="qt-container">

    <div class="qt-upcoming__head" data-animate>
      <h2 id="qt-events-title" class="qt-upcoming__title">
        Next <em>Sessions</em>
      </h2>
      <a href="<?php echo esc_url(get_post_type_archive_link('qt_event')); ?>" class="qt-upcoming__see-all">All events →</a>
    </div>

    <?php if ($events_query->have_posts()) : ?>
    <div class="qt-events-grid" data-stagger>

      <?php $card_index = 0; while ($events_query->have_posts()) : $events_query->the_post();
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

      <a href="<?php echo esc_url(get_permalink()); ?>" class="<?php echo esc_attr($card_class); ?>" data-animate>
        <div class="qt-event-card__photo">
          <div class="qt-event-card__photo-img" role="img"
               aria-label="<?php echo esc_attr(get_the_title()); ?>"
               <?php if ($ev_thumb) : ?>style="background-image: url('<?php echo esc_url($ev_thumb); ?>'); background-size: cover; background-position: center;"<?php endif; ?>>
          </div>
          <?php if ($ev_badge) : ?><span class="qt-event-card__badge"><?php echo esc_html($ev_badge); ?></span><?php endif; ?>
          <?php if ($ev_seats) : ?><span class="qt-event-card__seats"><?php echo esc_html($ev_seats); ?> seats left</span><?php endif; ?>
        </div>

        <div class="qt-event-card__body">
          <div class="qt-event-card__date"><?php echo esc_html($ev_date_d); ?></div>
          <h3 class="qt-event-card__name"><?php the_title(); ?></h3>
          <?php if ($ev_venue) : ?>
          <p class="qt-event-card__venue"><?php echo esc_html($ev_venue); ?></p>
          <?php endif; ?>
        </div>

        <div class="qt-event-card__footer">
          <div class="qt-event-card__price">
            <?php if ($ev_price && $ev_status !== 'coming-soon') : ?>
              KES <?php echo esc_html(number_format((float)$ev_price)); ?> <span>per person</span>
            <?php elseif ($ev_status === 'sold-out') : ?>
              <span style="color:var(--qt-muted)">Sold Out</span>
            <?php else : ?>
              Coming Soon
            <?php endif; ?>
          </div>
          <?php if ($ev_status === 'sold-out') : ?>
            <span class="qt-event-card__btn" style="opacity:.5">Sold Out</span>
          <?php elseif ($ev_status === 'coming-soon') : ?>
            <span class="qt-event-card__btn">Notify Me</span>
          <?php else : ?>
            <span class="qt-event-card__btn">Book Now</span>
          <?php endif; ?>
        </div>
      </a>

      <?php $card_index++; endwhile; wp_reset_postdata(); ?>

    </div>
    <?php else : ?>
    <p style="color:var(--qt-muted);font-size:var(--qt-text-md);padding-block:var(--qt-3xl)">
      New dates dropping soon. <a href="#newsletter" style="color:var(--qt-accent)">Get notified →</a>
    </p>
    <?php endif; ?>

  </div>
</section>

<hr class="qt-rule">

<!-- ╔══ THE EXPERIENCE ═════════════════════════════════════════════╗ -->
<?php
$exp_line1   = qt_opt('qt_exp_line1', 'We said trivia.');
$exp_line2   = qt_opt('qt_exp_line2', 'We meant theatre.');
$exp_photo   = qt_opt('qt_exp_photo', '');
$exp_01_t    = qt_opt('qt_exp_01_title', 'Your team. Your strategy.');
$exp_01_d    = qt_opt('qt_exp_01_desc', 'Four to six people. Pick your specialists — the sports nerd, the music obsessive, the one who watched every documentary.');
$exp_02_t    = qt_opt('qt_exp_02_title', 'Ten rounds of controlled chaos.');
$exp_02_d    = qt_opt('qt_exp_02_desc', 'Pop culture. History. Music. Sport. Science. Each round a new chance to come back from last place.');
$exp_03_t    = qt_opt('qt_exp_03_title', 'Prizes that are actually worth winning.');
$exp_03_d    = qt_opt('qt_exp_03_desc', 'Cash. Vouchers. Bragging rights that follow you to the office on Monday.');
?>
<section class="qt-experience" id="experience" aria-labelledby="qt-exp-headline">
  <div class="qt-experience__inner qt-container" style="max-width:100%;padding:0">

    <div class="qt-experience__visual" role="img" aria-label="People competing at a lively trivia night"
         style="margin-left:max(var(--qt-xl),calc((100vw - 1360px)/2));<?php if ($exp_photo) : ?>background-image:linear-gradient(to right,transparent 60%,var(--qt-paper-2) 100%),url('<?php echo esc_url($exp_photo); ?>');<?php endif; ?>">
    </div>

    <div class="qt-experience__text">
      <h2 id="qt-exp-headline" class="qt-experience__headline" data-animate>
        <?php echo esc_html($exp_line1); ?><br>
        <em><?php echo esc_html($exp_line2); ?></em>
      </h2>

      <div class="qt-experience__items" data-stagger>
        <?php foreach ([
          ['01', $exp_01_t, $exp_01_d],
          ['02', $exp_02_t, $exp_02_d],
          ['03', $exp_03_t, $exp_03_d],
        ] as [$num, $title, $desc]) : ?>
        <div class="qt-experience__item" data-animate>
          <span class="qt-experience__item-num" aria-hidden="true"><?php echo esc_html($num); ?></span>
          <div class="qt-experience__item-text">
            <strong><?php echo esc_html($title); ?></strong>
            <p><?php echo esc_html($desc); ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</section>

<hr class="qt-rule">

<!-- ╔══ HOW IT WORKS ═══════════════════════════════════════════════╗ -->
<?php
$how_headline = qt_opt('qt_how_headline', 'How the night runs');
$how_sub      = qt_opt('qt_how_sub', 'Four phases. Each one louder than the last.');
$how_steps = [
  ['Form your team',    '4–6 players per table. Book early — seats fill fast. Come with your crew or make new ones at the door.'],
  ['Pick your poison',  'Each event has a theme. Pop Culture. Sports. Music. Mixed Bag. You\'ll know before you book — show up strong.'],
  ['Compete live',      'Ten rounds. A host who keeps it moving. Score updates after each round. The comeback is always on the cards.'],
  ['Take the trophy',   'Winners get prizes. The rest get a great night out. Either way, you\'re coming back next month.'],
];
?>
<section class="qt-how" id="how-it-works" aria-labelledby="qt-how-title">
  <div class="qt-container">

    <div class="qt-how__head" data-animate>
      <h2 id="qt-how-title" class="qt-how__title"><?php echo esc_html($how_headline); ?></h2>
      <p class="qt-how__sub"><?php echo esc_html($how_sub); ?></p>
    </div>

    <div class="qt-how__steps" role="list" data-stagger>
      <?php foreach ($how_steps as $i => [$title, $desc]) : $num = str_pad($i + 1, 2, '0', STR_PAD_LEFT); ?>
      <div class="qt-how__step" role="listitem" data-animate>
        <div class="qt-how__step-num" aria-hidden="true"><?php echo esc_html($num); ?></div>
        <h3 class="qt-how__step-title"><?php echo esc_html($title); ?></h3>
        <p class="qt-how__step-desc"><?php echo esc_html($desc); ?></p>
        <?php if ($i < count($how_steps) - 1) : ?>
        <div class="qt-how__step-arrow" aria-hidden="true"></div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>

<hr class="qt-rule">

<!-- ╔══ LEADERBOARD ════════════════════════════════════════════════╗ -->
<?php
$lb_query = new WP_Query([
  'post_type'      => 'qt_leaderboard',
  'posts_per_page' => 10,
  'orderby'        => 'date',
  'order'          => 'DESC',
]);
?>
<section class="qt-leaderboard" id="leaderboard" aria-labelledby="qt-lb-title">
  <div class="qt-container">
    <div class="qt-leaderboard__inner">

      <div class="qt-leaderboard__intro" data-animate>
        <div class="qt-leaderboard__kicker">
          <span class="qt-leaderboard__kicker-bar" aria-hidden="true"></span>
          <span class="qt-label">Hall of Fame</span>
        </div>
        <h2 id="qt-lb-title" class="qt-leaderboard__title">
          Top<br><em>Teams</em>
        </h2>
        <p class="qt-leaderboard__desc">
          The names that keep coming back. The rivals who've gone head-to-head three sessions running. The new team nobody expected to win.
        </p>
      </div>

      <div data-animate data-animate="fade-right">
        <?php if ($lb_query->have_posts()) : ?>
        <div class="qt-lb-scroll">
        <table class="qt-lb-table" aria-label="Trivia night winners">
          <thead>
            <tr>
              <th scope="col">Trivia Night</th>
              <th scope="col">Team</th>
              <th scope="col">Venue</th>
              <th scope="col">Status</th>
            </tr>
          </thead>
          <tbody>
          <?php while ($lb_query->have_posts()) : $lb_query->the_post();
            $lb_team   = get_post_meta(get_the_ID(), 'qt_lb_team',   true);
            $lb_venue  = get_post_meta(get_the_ID(), 'qt_lb_venue',  true);
            $lb_status = get_post_meta(get_the_ID(), 'qt_lb_status', true);
          ?>
            <tr>
              <td><div class="qt-lb-night"><?php the_title(); ?></div></td>
              <td><div class="qt-lb-team"><?php echo esc_html($lb_team); ?></div></td>
              <td class="qt-lb-venue"><?php echo esc_html($lb_venue); ?></td>
              <td><?php if ($lb_status) : ?><span class="qt-lb-badge"><?php echo esc_html($lb_status); ?></span><?php endif; ?></td>
            </tr>
          <?php endwhile; wp_reset_postdata(); ?>
          </tbody>
        </table>
        </div>
        <?php else : ?>
        <p style="color:var(--qt-muted);padding-block:var(--qt-2xl)">Hall of Fame coming after the first event.</p>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>

<hr class="qt-rule">

<!-- ╔══ GALLERY ════════════════════════════════════════════════════╗ -->
<?php
$gallery_caption = qt_opt('qt_gallery_caption', 'The Grand Trivia Night · April 2026 · Wooden Barrels, APIC Center · Nairobi');
$gallery_imgs = [];
for ($i = 1; $i <= 5; $i++) {
  $gallery_imgs[] = [
    'src' => qt_opt("qt_gallery_img_{$i}", ''),
    'alt' => qt_opt("qt_gallery_alt_{$i}", "Gallery photo {$i}"),
  ];
}
$gallery_modifiers = ['--main', '--2', '--3', '--4', '--5'];
?>
<section class="qt-gallery" aria-labelledby="qt-gallery-title">
  <div class="qt-gallery__head" data-animate>
    <h2 id="qt-gallery-title" class="qt-gallery__title">From the night</h2>
  </div>

  <div class="qt-gallery__layout" aria-label="Photo gallery from past events">
    <?php foreach ($gallery_imgs as $gi => $img) :
      $mod = $gallery_modifiers[$gi] ?? '';
      $style = $img['src'] ? "background-image:url('" . esc_url($img['src']) . "');" : '';
    ?>
    <div class="qt-gallery__img qt-gallery__img<?php echo esc_attr($mod); ?>"
         role="img"
         aria-label="<?php echo esc_attr($img['alt']); ?>"
         style="<?php echo $style; ?>"
         data-animate="fade-scale">
    </div>
    <?php endforeach; ?>
  </div>

  <div class="qt-gallery__caption">
    <p><?php echo esc_html($gallery_caption); ?></p>
  </div>
</section>

<hr class="qt-rule">

<!-- ╔══ TESTIMONIALS ════════════════════════════════════════════════╗ -->
<?php
$testimonials_query = new WP_Query([
  'post_type'      => 'qt_testimonial',
  'posts_per_page' => 3,
  'orderby'        => 'menu_order',
  'order'          => 'ASC',
]);
?>
<?php if ($testimonials_query->have_posts()) : ?>
<section class="qt-testimonials" aria-labelledby="qt-testimonials-title">
  <div class="qt-container">
    <div class="qt-testimonials__head" data-animate>
      <h2 id="qt-testimonials-title" class="qt-testimonials__title">What people say</h2>
    </div>

    <div class="qt-testimonials__grid" data-stagger>
      <?php while ($testimonials_query->have_posts()) : $testimonials_query->the_post();
        $t_quote  = get_post_meta(get_the_ID(), 'qt_testimonial_quote', true);
        $t_name   = get_post_meta(get_the_ID(), 'qt_testimonial_name', true);
        $t_detail = get_post_meta(get_the_ID(), 'qt_testimonial_detail', true);
      ?>
      <blockquote class="qt-testimonial" data-animate>
        <p class="qt-testimonial__quote"><?php echo wp_kses_post($t_quote); ?></p>
        <footer class="qt-testimonial__attr">
          <cite class="qt-testimonial__name"><?php echo esc_html($t_name); ?></cite>
          <?php if ($t_detail) : ?>
          <span class="qt-testimonial__detail"><?php echo esc_html($t_detail); ?></span>
          <?php endif; ?>
        </footer>
      </blockquote>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>
  </div>
</section>

<hr class="qt-rule">
<?php endif; ?>

<!-- ╔══ FAQ ════════════════════════════════════════════════════════╗ -->
<?php
$faq_query = new WP_Query([
  'post_type'      => 'qt_faq',
  'posts_per_page' => 10,
  'meta_key'       => 'qt_faq_order',
  'orderby'        => 'meta_value_num',
  'order'          => 'ASC',
]);
/* Fallback FAQ if none in DB */
$default_faqs = [
  ['Do I need to pre-book?', 'Yes — seats are limited to keep the event intimate. We sell out quickly, especially for the featured events. Book online; tickets at the door are rare and not guaranteed.'],
  ['How many people per team?', 'Between 4 and 6 people per team. You can register a solo entry and we\'ll match you with a group on the night, but teams of 4–6 perform best.'],
  ['What is the ticket price?', 'KES 500 per person. This covers your entry and a reserved seat for the full evening. Drinks and food are ordered separately at the venue.'],
  ['What kind of questions are asked?', 'Each event has a theme — Pop Culture, Sports, Music, History, or Mixed Bag. The theme is announced in advance so you can build your dream team around it.'],
  ['Is there a prize?', 'Yes. Cash prizes for the top teams, plus vouchers and the bragging rights that come with being the table everyone watched all night.'],
  ['Can I bring kids?', 'QUIZTOPIA_KE events are 18+ by default, as they are held at licensed bar venues. Check the specific event listing for any exceptions.'],
];
?>
<section class="qt-faq" id="faq" aria-labelledby="qt-faq-title">
  <div class="qt-container">
    <div class="qt-faq__inner">

      <div class="qt-faq__label" id="qt-faq-title" data-animate>
        Frequently<br><em>Asked</em>
      </div>

      <div class="qt-faq__list" role="list">
        <?php if ($faq_query->have_posts()) :
          while ($faq_query->have_posts()) : $faq_query->the_post();
            $faq_answer = get_post_meta(get_the_ID(), 'qt_faq_answer', true);
        ?>
        <div class="qt-faq__item" role="listitem" data-animate>
          <button class="qt-faq__question" aria-expanded="false">
            <span class="qt-faq__q-text"><?php the_title(); ?></span>
            <span class="qt-faq__q-icon" aria-hidden="true">+</span>
          </button>
          <div class="qt-faq__answer"><?php echo wp_kses_post($faq_answer); ?></div>
        </div>
        <?php endwhile; wp_reset_postdata();
        else :
          foreach ($default_faqs as [$q, $a]) :
        ?>
        <div class="qt-faq__item" role="listitem" data-animate>
          <button class="qt-faq__question" aria-expanded="false">
            <span class="qt-faq__q-text"><?php echo esc_html($q); ?></span>
            <span class="qt-faq__q-icon" aria-hidden="true">+</span>
          </button>
          <div class="qt-faq__answer"><?php echo esc_html($a); ?></div>
        </div>
        <?php endforeach; endif; ?>
      </div>

    </div>
  </div>
</section>

<!-- ╔══ FINAL CTA ══════════════════════════════════════════════════╗ -->
<?php
$cta_line1 = qt_opt('qt_cta_line1', "Don't just watch");
$cta_line2 = qt_opt('qt_cta_line2', 'the leaderboard.');
$cta_line3 = qt_opt('qt_cta_line3', 'Get on it.');
$cta_sub   = qt_opt('qt_cta_sub', 'Next event filling fast.');
$cta_bg    = qt_opt('qt_cta_bg', '');
$instagram_url    = qt_opt('qt_instagram_url', 'https://instagram.com/quiztopia_ke');
$instagram_handle = qt_opt('qt_instagram_handle', '@quiztopia_ke');
?>
<section class="qt-final-cta<?php echo $cta_bg ? ' has-bg-image' : ''; ?>" aria-labelledby="qt-cta-headline"
         <?php if ($cta_bg) : ?>style="--cta-bg-url: url('<?php echo esc_url($cta_bg); ?>')"<?php endif; ?>>
  <div class="qt-container">
    <div class="qt-final-cta__inner">

      <h2 id="qt-cta-headline" class="qt-final-cta__headline" data-animate>
        <?php echo esc_html($cta_line1); ?><br>
        <em><?php echo esc_html($cta_line2); ?></em><br>
        <?php echo esc_html($cta_line3); ?>
      </h2>

      <div class="qt-final-cta__actions" data-animate="fade-left">
        <a href="<?php echo esc_url($shop_url); ?>" class="qt-btn-primary">Get Tickets</a>
        <p class="qt-final-cta__sub"><?php echo esc_html($cta_sub); ?></p>
        <?php if ($instagram_url) : ?>
        <a href="<?php echo esc_url($instagram_url); ?>" class="qt-final-cta__insta" target="_blank" rel="noopener noreferrer">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.5" fill="currentColor" stroke="none"/></svg>
          <?php echo esc_html($instagram_handle); ?>
        </a>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>

<?php get_footer(); ?>
