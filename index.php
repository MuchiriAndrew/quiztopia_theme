<?php get_header(); ?>

<div class="qt-page-header">
  <div class="qt-container">
    <span class="qt-label qt-page-header__kicker">QUIZTOPIA_KE</span>
    <h1 class="qt-page-header__title" data-animate><?php bloginfo('name'); ?></h1>
  </div>
</div>

<div class="qt-page-content">
  <div class="qt-container">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
      <article>
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <?php the_excerpt(); ?>
      </article>
    <?php endwhile; else : ?>
      <p style="color:var(--qt-ink-2)">Nothing here yet.</p>
    <?php endif; ?>
  </div>
</div>

<?php get_footer(); ?>
