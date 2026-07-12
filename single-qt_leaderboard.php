<?php get_header(); ?>

<?php while ( have_posts() ) : the_post();
  $lb_id       = get_the_ID();
  $lb_subtitle = qt_get_leaderboard_subtitle( $lb_id );
  $lb_teams    = qt_get_leaderboard_teams( $lb_id );
?>

<div class="qt-leaderboard-single">
  <div class="qt-container">

    <div class="qt-page-header" style="padding-top:0;padding-bottom:var(--qt-2xl);background:transparent">
      <a href="<?php echo esc_url( get_post_type_archive_link( 'qt_leaderboard' ) ); ?>" class="qt-lb-back" data-animate>
        <span aria-hidden="true">←</span> Full leaderboard
      </a>
      <span class="qt-label" data-animate style="display:block;margin-top:var(--qt-lg)">Hall of Fame</span>
      <h1 class="qt-display qt-display--lg" data-animate style="margin-top:var(--qt-sm)"><?php the_title(); ?></h1>
      <?php if ( $lb_subtitle ) : ?>
      <p class="qt-lb-single__sub" data-animate><?php echo esc_html( $lb_subtitle ); ?></p>
      <?php endif; ?>
    </div>

    <?php if ( $lb_teams ) : ?>
    <ol class="qt-lb-list qt-lb-list--single" data-stagger>
      <?php foreach ( $lb_teams as $rank => $team ) : ?>
      <li class="qt-lb-row<?php echo $rank === 0 ? ' qt-lb-row--lead' : ''; ?>" data-animate>
        <span class="qt-lb-rank"><?php echo esc_html( $rank + 1 ); ?></span>
        <span class="qt-lb-name"><?php echo esc_html( $team['name'] ); ?></span>
        <?php if ( ! empty( $team['note'] ) ) : ?><span class="qt-lb-note"><?php echo esc_html( $team['note'] ); ?></span><?php endif; ?>
      </li>
      <?php endforeach; ?>
    </ol>
    <?php else : ?>
    <p style="color:var(--qt-muted);font-size:var(--qt-text-md);padding:var(--qt-3xl) 0">
      No teams added to this leaderboard yet.
    </p>
    <?php endif; ?>

  </div>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
