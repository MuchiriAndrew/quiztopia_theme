<?php
defined( 'ABSPATH' ) || exit;

/* ═══════════════════════════════════════════════════════════════
   BRAND EMAIL TEMPLATES
   Brand colours (email-safe hex equivalents of CSS oklch values):
   BG dark   #100d0a   paper
   BG card   #1c1814   paper-2
   Accent    #e0890d   orange
   Text      #f0ebe4   ink
   Muted     #8a847e   muted
   Rule      #2c2822   border
   ══════════════════════════════════════════════════════════════ */

function qt_email_base( $body_html, $preheader = '' ) {
    $site_url  = home_url('/');
    $site_name = get_bloginfo('name') ?: 'QUIZTOPIA_KE';
    ob_start();
    ?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo esc_html($site_name); ?></title>
  <!--[if mso]>
  <noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
  <![endif]-->
  <style>
    body,table,td,a{-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}
    body{margin:0;padding:0;background:#100d0a;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif}
    table{border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt}
    img{border:0;height:auto;line-height:100%;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic}
    @media only screen and (max-width:620px){
      .email-container{width:100%!important}
      .content-pad{padding:32px 24px!important}
      .headline{font-size:32px!important;line-height:1.1!important}
    }
  </style>
</head>
<body style="margin:0;padding:0;background:#100d0a">

<?php if ($preheader) : ?>
<div style="display:none;max-height:0;overflow:hidden;mso-hide:all;font-size:1px;line-height:1px;color:#100d0a"><?php echo esc_html($preheader); ?></div>
<?php endif; ?>

<table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="background:#100d0a">
<tr><td align="center" style="padding:40px 16px">

  <!-- Email container -->
  <table class="email-container" width="580" cellpadding="0" cellspacing="0" border="0" role="presentation" style="max-width:580px;width:100%">

    <!-- ─ Logo ─ -->
    <tr>
      <td align="center" style="padding:0 0 28px">
        <a href="<?php echo esc_url($site_url); ?>" style="text-decoration:none">
          <span style="font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:20px;font-weight:900;letter-spacing:0.14em;color:#e0890d;text-transform:uppercase">QUIZTOPIA_KE</span>
        </a>
      </td>
    </tr>

    <!-- ─ Orange top bar ─ -->
    <tr>
      <td height="4" style="background:#e0890d;border-radius:3px 3px 0 0;font-size:0;line-height:0">&nbsp;</td>
    </tr>

    <!-- ─ Card body ─ -->
    <tr>
      <td class="content-pad" style="background:#1c1814;padding:52px 52px 44px;border-radius:0 0 8px 8px">
        <?php echo $body_html; ?>
      </td>
    </tr>

    <!-- ─ Spacer ─ -->
    <tr><td height="32" style="font-size:0;line-height:0">&nbsp;</td></tr>

    <!-- ─ Footer ─ -->
    <tr>
      <td align="center" style="padding:0 24px">
        <p style="margin:0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:11px;color:#4a4540;letter-spacing:0.10em;text-transform:uppercase">
          <?php echo esc_html($site_name); ?> &middot; Nairobi, Kenya
        </p>
        <p style="margin:10px 0 0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:11px;color:#4a4540;line-height:1.6">
          You're receiving this because you signed up for QUIZTOPIA_KE event updates.
        </p>
      </td>
    </tr>

  </table>

</td></tr>
</table>
</body>
</html>
    <?php
    return ob_get_clean();
}

/* ─── Confirmation email sent to subscriber on sign-up ──────── */
function qt_email_confirmation( $email ) {
    $site_url   = home_url('/');
    $next_event = qt_get_next_event();
    $next_date  = '';
    if ( $next_event ) {
        $raw = get_post_meta( $next_event->ID, 'qt_event_date', true );
        if ( $raw ) $next_date = date( 'j F Y', strtotime( $raw ) );
    }

    ob_start();
    ?>
<!-- Headline -->
<h1 class="headline" style="margin:0 0 8px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:40px;font-weight:900;line-height:1.05;letter-spacing:-0.02em;color:#f0ebe4;text-transform:uppercase">You're<br><span style="color:#e0890d">in the loop.</span></h1>

<!-- Sub -->
<p style="margin:20px 0 0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:16px;line-height:1.7;color:#a09890">
  When the next QUIZTOPIA_KE night drops, you'll hear it first — early-bird tickets, new venues, themed rounds, all of it.
</p>

<?php if ($next_date) : ?>
<!-- Next event teaser -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="margin:32px 0">
  <tr>
    <td style="background:#100d0a;border-left:3px solid #e0890d;border-radius:4px;padding:16px 20px">
      <p style="margin:0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:12px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;color:#e0890d">Next Event</p>
      <p style="margin:4px 0 0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:18px;font-weight:700;color:#f0ebe4"><?php echo esc_html($next_date); ?></p>
      <p style="margin:4px 0 0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:13px;color:#8a847e">Details &amp; tickets coming soon</p>
    </td>
  </tr>
</table>
<?php else : ?>
<div style="height:32px">&nbsp;</div>
<?php endif; ?>

<!-- Divider -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="margin:<?php echo $next_date ? '0' : '0'; ?> 0 32px">
  <tr><td height="1" style="background:#2c2822;font-size:0;line-height:0">&nbsp;</td></tr>
</table>

<!-- CTA -->
<table cellpadding="0" cellspacing="0" border="0" role="presentation">
  <tr>
    <td style="border-radius:6px;background:#e0890d">
      <a href="<?php echo esc_url($site_url); ?>" style="display:inline-block;padding:14px 32px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#100d0a;text-decoration:none">
        Visit QUIZTOPIA_KE &rarr;
      </a>
    </td>
  </tr>
</table>

<!-- Closing note -->
<p style="margin:28px 0 0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14px;line-height:1.7;color:#6a6460">
  Keep your team on standby.
</p>
    <?php
    $body = ob_get_clean();
    return qt_email_base( $body, 'You\'ll be first to know when the next trivia night drops.' );
}

/* ─── Announcement email sent to all leads about an event ─────── */
function qt_email_announcement( $event_id, $custom_subject = '' ) {
    $ev_date_raw = get_post_meta( $event_id, 'qt_event_date', true );
    $ev_date_f   = $ev_date_raw ? date( 'j F Y', strtotime( $ev_date_raw ) ) : '';
    $ev_time     = get_post_meta( $event_id, 'qt_event_time', true );
    $ev_venue    = get_post_meta( $event_id, 'qt_event_venue', true );
    $ev_price    = get_post_meta( $event_id, 'qt_event_price', true );
    $ev_theme    = get_post_meta( $event_id, 'qt_event_theme', true );
    $ev_title    = get_the_title( $event_id );
    $ev_wc_id    = get_post_meta( $event_id, 'qt_event_wc_product_id', true );
    $ev_url      = $ev_wc_id ? get_permalink( $ev_wc_id ) : get_permalink( $event_id );
    $shop_url    = function_exists('WC') ? wc_get_page_permalink('shop') : home_url('/shop');
    $buy_url     = $ev_url ?: $shop_url;

    $subject = $custom_subject ?: ( 'New event: ' . $ev_title . ( $ev_date_f ? ' — ' . $ev_date_f : '' ) );

    ob_start();
    ?>
<!-- Label -->
<p style="margin:0 0 16px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:11px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;color:#e0890d">New Event Announced</p>

<!-- Headline -->
<h1 class="headline" style="margin:0 0 8px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:40px;font-weight:900;line-height:1.05;letter-spacing:-0.02em;color:#f0ebe4;text-transform:uppercase"><?php echo esc_html($ev_title); ?></h1>

<?php if ($ev_date_f) : ?>
<!-- Date chip -->
<p style="margin:12px 0 0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:16px;font-weight:600;color:#f0ebe4">
  <?php echo esc_html($ev_date_f); ?>
  <?php if ($ev_time) : ?><span style="color:#6a6460"> &middot; <?php echo esc_html($ev_time); ?></span><?php endif; ?>
</p>
<?php endif; ?>

<!-- Details block -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="margin:28px 0">
  <tr>
    <td style="background:#100d0a;border-radius:6px;padding:24px 24px 20px">

      <?php if ($ev_venue) : ?>
      <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="margin-bottom:14px">
        <tr>
          <td width="90" style="font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:11px;font-weight:600;letter-spacing:0.10em;text-transform:uppercase;color:#4a4540;vertical-align:top;padding-top:2px">Venue</td>
          <td style="font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:15px;color:#f0ebe4;font-weight:500"><?php echo esc_html($ev_venue); ?></td>
        </tr>
      </table>
      <?php endif; ?>

      <?php if ($ev_theme) : ?>
      <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="margin-bottom:14px">
        <tr>
          <td width="90" style="font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:11px;font-weight:600;letter-spacing:0.10em;text-transform:uppercase;color:#4a4540;vertical-align:top;padding-top:2px">Theme</td>
          <td style="font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:15px;color:#e0890d;font-weight:700"><?php echo esc_html($ev_theme); ?></td>
        </tr>
      </table>
      <?php endif; ?>

      <?php if ($ev_price) : ?>
      <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation">
        <tr>
          <td width="90" style="font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:11px;font-weight:600;letter-spacing:0.10em;text-transform:uppercase;color:#4a4540;vertical-align:top;padding-top:2px">Tickets</td>
          <td style="font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:15px;color:#f0ebe4;font-weight:500">KES <?php echo esc_html(number_format((float)$ev_price)); ?> <span style="color:#4a4540;font-size:13px">per person</span></td>
        </tr>
      </table>
      <?php endif; ?>

    </td>
  </tr>
</table>

<!-- CTA -->
<table cellpadding="0" cellspacing="0" border="0" role="presentation" style="margin-bottom:20px">
  <tr>
    <td style="border-radius:6px;background:#e0890d">
      <a href="<?php echo esc_url($buy_url); ?>" style="display:inline-block;padding:16px 36px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#100d0a;text-decoration:none">
        Book Your Spot &rarr;
      </a>
    </td>
  </tr>
</table>

<!-- Urgency note -->
<p style="margin:0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:13px;color:#6a6460">
  Seats are limited. First in, first seated.
</p>
    <?php
    $body = ob_get_clean();
    return [
        'subject' => $subject,
        'html'    => qt_email_base( $body, 'A new QUIZTOPIA_KE night just dropped. Grab your spot.' ),
    ];
}
