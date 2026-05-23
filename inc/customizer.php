<?php
defined( 'ABSPATH' ) || exit;

function qt_customizer( $wp_customize ) {

    /* ─── Panel ─────────────────────────────────────────────────── */
    $wp_customize->add_panel( 'qt_panel', [
        'title'    => 'QUIZTOPIA Site Content',
        'priority' => 10,
    ] );

    /* ─── Helper: add text setting + control ─────────────────────── */
    $add = function( $id, $label, $section, $default = '', $type = 'text', $description = '' ) use ( $wp_customize ) {
        $wp_customize->add_setting( $id, [
            'default'           => $default,
            'sanitize_callback' => $type === 'url' ? 'esc_url_raw' : 'wp_kses_post',
            'transport'         => 'postMessage',
        ] );
        $ctrl_args = [
            'label'       => $label,
            'section'     => $section,
            'settings'    => $id,
            'description' => $description,
        ];
        if ( $type === 'textarea' ) {
            $wp_customize->add_control( new WP_Customize_Control( $wp_customize, $id,
                array_merge( $ctrl_args, [ 'type' => 'textarea' ] ) ) );
        } elseif ( $type === 'image' ) {
            $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $id, $ctrl_args ) );
        } else {
            $wp_customize->add_control( $id, array_merge( $ctrl_args, [ 'type' => $type ] ) );
        }
    };

    /* ═══════════════════════════════════════════════════════════════
       HERO
       ══════════════════════════════════════════════════════════════ */
    $wp_customize->add_section( 'qt_hero', [
        'title'    => 'Hero',
        'panel'    => 'qt_panel',
        'priority' => 10,
    ] );
    $add( 'qt_hero_kicker',    'Kicker text',       'qt_hero', "Nairobi's loudest trivia night" );
    $add( 'qt_hero_line1',     'Headline line 1',   'qt_hero', 'Bring your' );
    $add( 'qt_hero_line2',     'Headline line 2 (amber)',   'qt_hero', 'sharpest mind.' );
    $add( 'qt_hero_line3',     'Headline line 3',   'qt_hero', 'Or your funniest friend.' );
    $add( 'qt_hero_sub',       'Subheadline',       'qt_hero', 'Live trivia. Real prizes. Ice cold drinks. Every round hits different when you\'re competing with the whole room.', 'textarea' );
    $add( 'qt_hero_cta1',      'Primary CTA label', 'qt_hero', 'Get Tickets' );
    $add( 'qt_hero_cta2',      'Secondary CTA label','qt_hero', 'See what happens' );
    $add( 'qt_hero_photo',     'Hero right-side photo', 'qt_hero', '', 'image', 'Bar / venue interior photo. Right half of hero (used when no video is set).' );
    $add( 'qt_hero_video',    'Hero video (MP4 URL)',  'qt_hero', '', 'url',   'Self-hosted .mp4 — overrides the photo. Portrait / phone-shot videos work great on this layout.' );

    /* ═══════════════════════════════════════════════════════════════
       EXPERIENCE SECTION
       ══════════════════════════════════════════════════════════════ */
    $wp_customize->add_section( 'qt_experience', [
        'title'    => 'The Experience',
        'panel'    => 'qt_panel',
        'priority' => 20,
    ] );
    $add( 'qt_exp_line1',   'Headline line 1',        'qt_experience', 'We said trivia.' );
    $add( 'qt_exp_line2',   'Headline line 2 (amber)', 'qt_experience', 'We meant theatre.' );
    $add( 'qt_exp_photo',   'Experience photo',        'qt_experience', '', 'image', 'Crowd / trivia night photo for the left panel.' );
    $add( 'qt_exp_01_title','Item 01 title', 'qt_experience', 'Your team. Your strategy.' );
    $add( 'qt_exp_01_desc', 'Item 01 description', 'qt_experience', 'Four to six people. Pick your specialists — the sports nerd, the music obsessive, the one who watched every documentary.', 'textarea' );
    $add( 'qt_exp_02_title','Item 02 title', 'qt_experience', 'Ten rounds of controlled chaos.' );
    $add( 'qt_exp_02_desc', 'Item 02 description', 'qt_experience', 'Pop culture. History. Music. Sport. Science. Each round a new chance to come back from last place.', 'textarea' );
    $add( 'qt_exp_03_title','Item 03 title', 'qt_experience', 'Prizes that are actually worth winning.' );
    $add( 'qt_exp_03_desc', 'Item 03 description', 'qt_experience', 'Cash. Vouchers. Bragging rights that follow you to the office on Monday.', 'textarea' );

    /* ═══════════════════════════════════════════════════════════════
       HOW IT WORKS
       ══════════════════════════════════════════════════════════════ */
    $wp_customize->add_section( 'qt_how', [
        'title'    => 'How It Works',
        'panel'    => 'qt_panel',
        'priority' => 30,
    ] );
    $add( 'qt_how_headline', 'Section headline', 'qt_how', 'How the night runs' );
    $add( 'qt_how_sub',      'Section subheadline', 'qt_how', 'Four phases. Each one louder than the last.' );

    /* ═══════════════════════════════════════════════════════════════
       GALLERY
       ══════════════════════════════════════════════════════════════ */
    $wp_customize->add_section( 'qt_gallery', [
        'title'    => 'Gallery',
        'panel'    => 'qt_panel',
        'priority' => 40,
    ] );
    $add( 'qt_gallery_caption', 'Gallery caption', 'qt_gallery', 'The Grand Trivia Night · April 2026 · Wooden Barrels, APIC Center · Nairobi' );
    for ( $i = 1; $i <= 5; $i++ ) {
        $add( "qt_gallery_img_{$i}", "Gallery photo {$i}", 'qt_gallery', '', 'image' );
        $add( "qt_gallery_alt_{$i}", "Gallery photo {$i} alt text", 'qt_gallery', '' );
    }

    /* ═══════════════════════════════════════════════════════════════
       FINAL CTA
       ══════════════════════════════════════════════════════════════ */
    $wp_customize->add_section( 'qt_final_cta', [
        'title'    => 'Final CTA',
        'panel'    => 'qt_panel',
        'priority' => 50,
    ] );
    $add( 'qt_cta_line1', 'Headline line 1', 'qt_final_cta', 'Don\'t just watch' );
    $add( 'qt_cta_line2', 'Headline line 2 (amber)', 'qt_final_cta', 'the leaderboard.' );
    $add( 'qt_cta_line3', 'Headline line 3', 'qt_final_cta', 'Get on it.' );
    $add( 'qt_cta_sub',   'Sub text below CTA button', 'qt_final_cta', 'Next event filling fast.' );

    /* ═══════════════════════════════════════════════════════════════
       FOOTER / NEWSLETTER
       ══════════════════════════════════════════════════════════════ */
    $wp_customize->add_section( 'qt_footer', [
        'title'    => 'Footer & Newsletter',
        'panel'    => 'qt_panel',
        'priority' => 60,
    ] );
    $add( 'qt_nl_headline', 'Newsletter headline line 1', 'qt_footer', 'Stay in the' );
    $add( 'qt_nl_accent',   'Newsletter headline line 2 (amber)', 'qt_footer', 'loop.' );
    $add( 'qt_nl_sub',      'Newsletter sub text', 'qt_footer', 'New events, new venues, early-bird tickets — straight to your inbox.' );
    $add( 'qt_footer_copy', 'Footer copyright line', 'qt_footer', '© ' . date('Y') . ' QUIZTOPIA_KE. All rights reserved.' );

    /* ═══════════════════════════════════════════════════════════════
       SOCIAL / BRAND
       ══════════════════════════════════════════════════════════════ */
    $wp_customize->add_section( 'qt_social', [
        'title'    => 'Social & Brand',
        'panel'    => 'qt_panel',
        'priority' => 70,
    ] );
    $add( 'qt_instagram_url',    'Instagram URL',    'qt_social', 'https://instagram.com/quiztopia_ke', 'url' );
    $add( 'qt_instagram_handle', 'Instagram handle', 'qt_social', '@quiztopia_ke' );
    $add( 'qt_tiktok_url',       'TikTok URL',       'qt_social', '', 'url' );
    $add( 'qt_whatsapp_url',     'WhatsApp URL',     'qt_social', '', 'url' );
}
add_action( 'customize_register', 'qt_customizer' );
