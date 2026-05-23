<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>

<div class="qt-page-header">
  <div class="qt-container" style="position:relative">
    <span class="qt-label qt-page-header__kicker"><?php echo get_the_date(); ?></span>
    <h1 class="qt-page-header__title qt-display qt-display--lg" data-animate>
      <?php the_title(); ?>
    </h1>
  </div>
</div>

<div class="qt-page-content">
  <div class="qt-container">
    <?php the_content(); ?>
  </div>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
