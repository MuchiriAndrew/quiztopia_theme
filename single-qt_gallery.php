<?php get_header(); ?>

<?php while ( have_posts() ) : the_post();
  $g_id       = get_the_ID();
  $g_subtitle = get_post_meta( $g_id, 'qt_gallery_subtitle', true );
  $g_ids      = qt_get_gallery_image_ids( $g_id );
?>

<div class="qt-gallery-single">
  <div class="qt-container">

    <div class="qt-page-header" style="padding-top:0;padding-bottom:var(--qt-2xl);background:transparent">
      <a href="<?php echo esc_url( get_post_type_archive_link( 'qt_gallery' ) ); ?>" class="qt-gallery-single__back" data-animate>
        <span aria-hidden="true">←</span> All galleries
      </a>
      <h1 class="qt-display qt-display--lg" data-animate style="margin-top:var(--qt-md)"><?php the_title(); ?></h1>
      <?php if ( $g_subtitle ) : ?>
      <p class="qt-gallery-single__subtitle" data-animate><?php echo esc_html( $g_subtitle ); ?></p>
      <?php endif; ?>
    </div>

    <?php if ( $g_ids ) : ?>
    <div class="qt-gallery-single__grid" data-stagger>
      <?php foreach ( $g_ids as $att_id ) :
        $thumb = wp_get_attachment_image_url( $att_id, 'large' );
        $full  = wp_get_attachment_image_url( $att_id, 'full' );
        if ( ! $thumb ) continue;
        $alt   = trim( strip_tags( get_post_meta( $att_id, '_wp_attachment_image_alt', true ) ) );
      ?>
      <a href="<?php echo esc_url( $full ?: $thumb ); ?>"
         class="qt-gallery-single__item"
         data-lightbox
         data-animate="fade-scale">
        <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $alt ?: get_the_title() ); ?>" loading="lazy">
      </a>
      <?php endforeach; ?>
    </div>
    <?php else : ?>
    <p style="color:var(--qt-muted);font-size:var(--qt-text-md);padding:var(--qt-3xl) 0">
      No photos in this gallery yet.
    </p>
    <?php endif; ?>

  </div>
</div>

<!-- Lightbox -->
<div class="qt-lightbox" id="qt-lightbox" role="dialog" aria-modal="true" aria-label="Photo viewer" hidden>
  <button class="qt-lightbox__close" type="button" aria-label="Close">&times;</button>
  <button class="qt-lightbox__nav qt-lightbox__nav--prev" type="button" aria-label="Previous photo">&#8249;</button>
  <img class="qt-lightbox__img" src="" alt="">
  <button class="qt-lightbox__nav qt-lightbox__nav--next" type="button" aria-label="Next photo">&#8250;</button>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
