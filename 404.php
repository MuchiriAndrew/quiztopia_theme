<?php get_header(); ?>

<div class="qt-404">
  <div class="qt-container" style="position:relative">
    <div class="qt-404__num" aria-hidden="true">404</div>
    <h1 class="qt-404__title" data-animate>Wrong night.</h1>
    <p class="qt-404__sub" data-animate>This page doesn't exist. But there's a trivia night you probably shouldn't miss.</p>
    <div style="display:flex;gap:var(--qt-md);flex-wrap:wrap" data-animate>
      <a href="<?php echo esc_url(home_url('/')); ?>" class="qt-btn-primary">Back home</a>
      <a href="<?php echo esc_url(home_url('/#events')); ?>" class="qt-btn-ghost">See events</a>
    </div>
  </div>
</div>

<?php get_footer(); ?>
