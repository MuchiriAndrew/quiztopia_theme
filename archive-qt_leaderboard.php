<?php get_header(); ?>

<div class="qt-leaderboard-archive">
  <div class="qt-container">

    <div class="qt-page-header" style="padding-top:0;padding-bottom:var(--qt-3xl);background:transparent">
      <span class="qt-label" data-animate>Hall of Fame</span>
      <h1 class="qt-display qt-display--lg" data-animate style="margin-top:var(--qt-md)">
        The <em style="font-style:normal;color:var(--qt-accent)">Leaderboard</em>
      </h1>
      <p data-animate style="margin-top:var(--qt-md);color:var(--qt-ink-2);max-width:48ch;font-size:var(--qt-text-md)">
        Every trivia night, every team that made the board. Newest events first.
      </p>
    </div>

    <?php if ( have_posts() ) : ?>
    <div class="qt-lb-groups qt-lb-groups--archive" data-stagger>
      <?php while ( have_posts() ) : the_post();
        qt_render_leaderboard_group( get_the_ID() );
      endwhile; ?>
    </div>

    <?php the_posts_pagination([
      'mid_size'  => 2,
      'prev_text' => '← Newer',
      'next_text' => 'Older →',
    ]); ?>

    <?php else : ?>
    <p style="color:var(--qt-muted);font-size:var(--qt-text-md);padding:var(--qt-4xl) 0">
      No leaderboards published yet. Check back after the next trivia night.
    </p>
    <?php endif; ?>

  </div>
</div>

<?php get_footer(); ?>
