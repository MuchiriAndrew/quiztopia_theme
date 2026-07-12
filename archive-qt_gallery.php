<?php get_header(); ?>

<div class="qt-galleries-archive">
  <div class="qt-container">

    <div class="qt-page-header" style="padding-top:0;padding-bottom:var(--qt-3xl);background:transparent">
      <span class="qt-label" data-animate>QUIZTOPIA_KE</span>
      <h1 class="qt-display qt-display--lg" data-animate style="margin-top:var(--qt-md)">
        The <em style="font-style:normal;color:var(--qt-accent)">Gallery</em>
      </h1>
      <p data-animate style="margin-top:var(--qt-md);color:var(--qt-ink-2);max-width:46ch;font-size:var(--qt-text-md)">
        Snapshots from every trivia night. Pick an event to see the full set.
      </p>
    </div>

    <?php if ( have_posts() ) : ?>

    <?php while ( have_posts() ) : the_post();
      $g_id       = get_the_ID();
      $g_subtitle = get_post_meta( $g_id, 'qt_gallery_subtitle', true );
      $g_ids      = qt_get_gallery_image_ids( $g_id );
      $g_total    = count( $g_ids );
      $g_preview  = array_slice( $g_ids, 0, 5 );
      $g_url      = get_permalink();
    ?>
    <section class="qt-gallery-set" data-animate>
      <div class="qt-gallery-set__head">
        <div>
          <h2 class="qt-gallery-set__title"><a href="<?php echo esc_url( $g_url ); ?>"><?php the_title(); ?></a></h2>
          <?php if ( $g_subtitle ) : ?>
          <p class="qt-gallery-set__subtitle"><?php echo esc_html( $g_subtitle ); ?></p>
          <?php endif; ?>
        </div>
        <?php if ( $g_total ) : ?>
        <a href="<?php echo esc_url( $g_url ); ?>" class="qt-gallery-set__view">
          View full gallery
          <span aria-hidden="true">→</span>
        </a>
        <?php endif; ?>
      </div>

      <?php if ( $g_preview ) : ?>
      <a href="<?php echo esc_url( $g_url ); ?>" class="qt-gallery-set__grid" aria-label="View <?php echo esc_attr( get_the_title() ); ?> gallery">
        <?php foreach ( $g_preview as $pi => $att_id ) :
          $img = wp_get_attachment_image_url( $att_id, 'large' );
          if ( ! $img ) continue;
          $is_last  = ( $pi === count( $g_preview ) - 1 );
          $overflow = $is_last && $g_total > count( $g_preview );
        ?>
        <div class="qt-gallery-set__cell<?php echo $pi === 0 ? ' qt-gallery-set__cell--main' : ''; ?>"
             style="background-image:url('<?php echo esc_url( $img ); ?>')">
          <?php if ( $overflow ) : ?>
          <span class="qt-gallery-set__more">+<?php echo esc_html( $g_total - count( $g_preview ) ); ?></span>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </a>
      <?php else : ?>
      <p class="qt-gallery-set__empty">No photos in this gallery yet.</p>
      <?php endif; ?>
    </section>
    <?php endwhile; ?>

    <?php the_posts_pagination([
      'mid_size'  => 2,
      'prev_text' => '← Newer',
      'next_text' => 'Older →',
    ]); ?>

    <?php else : ?>
    <p style="color:var(--qt-muted);font-size:var(--qt-text-md);padding:var(--qt-4xl) 0">
      No galleries published yet. Check back after the next trivia night.
    </p>
    <?php endif; ?>

  </div>
</div>

<?php get_footer(); ?>
