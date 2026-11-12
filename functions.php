<?php
/**
 * Functies voor het Droomvrouwroute-thema.
 *
 * De interactieve kaart zelf (infopunt/laag/velden/shortcode) zit in de
 * plugin "droomvrouwroute-kaart" — dit thema is alleen verantwoordelijk
 * voor de vormgeving eromheen (header, footer, kleuren, lettertype).
 */

/**
 * Thema-basisinstellingen: hoofdmenu-locatie en featured images.
 */
function droomvrouwroute_setup() {
    register_nav_menus( array(
        'hoofdmenu' => __( 'Hoofdmenu', 'droomvrouwroute' ),
    ) );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'title-tag' );
}
add_action( 'after_setup_theme', 'droomvrouwroute_setup' );

/**
 * Vervangt de sitenaam in de document-title door "De Droomvrouwroute",
 * los van de gedeelde WordPress-siteinstelling (die nog "Wereld Records"
 * heet van een andere opdracht op dezelfde installatie).
 */
function droomvrouwroute_document_title_parts( $title ) {
    if ( is_front_page() ) {
        // Op de voorpagina zet WP de sitenaam in 'title' (i.p.v. 'site').
        $title['title'] = 'De Droomvrouwroute';
        unset( $title['site'] );
    } else {
        $title['site'] = 'De Droomvrouwroute';
    }
    return $title;
}
add_filter( 'document_title_parts', 'droomvrouwroute_document_title_parts' );

/**
 * Verbergt de WP-adminbalk op de voorkant: die toont altijd de gedeelde
 * sitenaam ("Wereld Records") en past niet bij het schermvullende ontwerp.
 */
add_filter( 'show_admin_bar', '__return_false' );

/**
 * Laadt de algemene thema-stylesheet.
 */
function droomvrouwroute_enqueue_assets() {
    wp_enqueue_style( 'droomvrouwroute-style', get_stylesheet_uri(), array(), '1.0' );
}
add_action( 'wp_enqueue_scripts', 'droomvrouwroute_enqueue_assets' );

/**
 * Rendert de kaart (altijd dezelfde, echte, interactieve kaart — nooit een
 * kopie) als basis van de pagina, met daarboven een overlay-kader. Op de
 * voorpagina is dat kader leeg/verborgen; op een subpagina-URL (direct
 * bezocht, of zonder JS) staat het al open met de inhoud van die pagina.
 * JS (subpagina-overlay.js) neemt het daarna over: klikken op een
 * menu-item haalt de volgende subpagina via de REST API op zonder dat de
 * kaart ooit opnieuw geladen wordt.
 */
function droomvrouwroute_render_kaart_pagina( $open_pagina_id = 0 ) {
    if ( function_exists( 'droomvrouwroute_kaart_assets' ) ) {
        droomvrouwroute_kaart_assets();
    }

    wp_enqueue_style( 'dvr-overlay', get_template_directory_uri() . '/assets/css/subpagina-overlay.css', array( 'droomvrouwroute-style', 'dvr-kaart' ), '1.0' );
    wp_enqueue_script( 'dvr-overlay', get_template_directory_uri() . '/assets/js/subpagina-overlay.js', array(), '1.0', true );

    if ( function_exists( 'droomvrouwroute_idee_assets' ) ) {
        droomvrouwroute_idee_assets();
    }

    // Instagram's embed.js alleen laden als er echt een video-URL is
    // ingesteld — anders onnodige laadtijd voor niets (zie checklist
    // "Laadtijd checken" op de Instagram-kaart in Trello). De officiële
    // embed is zelf al lazy en start nooit met geluid aan.
    if ( trim( (string) get_option( 'options_instagram_video_url' ) ) ) {
        wp_enqueue_script( 'dvr-instagram-embed', 'https://www.instagram.com/embed.js', array(), null, true );
    }

    $menu = function_exists( 'droomvrouwroute_subpagina_menu' ) ? droomvrouwroute_subpagina_menu() : array();
    wp_localize_script( 'dvr-overlay', 'dvrOverlayData', array(
        'restBase'  => esc_url_raw( rest_url( 'dvr/v1/subpagina/' ) ),
        'homeUrl'   => esc_url_raw( home_url( '/' ) ),
        'homeTitel' => 'De Droomvrouwroute',
        'menu'      => $menu,
    ) );

    $fragment = null;
    if ( $open_pagina_id && function_exists( 'dvr_subpagina_fragment' ) ) {
        $fragment = dvr_subpagina_fragment( $open_pagina_id );
    }
    ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class( $fragment ? 'dvr-overlay-open' : '' ); ?>>

<?php echo function_exists( 'droomvrouwroute_kaart_shortcode' ) ? droomvrouwroute_kaart_shortcode() : ''; ?>

<div id="dvr-overlay" class="dvr-overlay<?php echo $fragment ? ' is-open' : ''; ?>">
    <div class="dvr-overlay-paneel" role="dialog" aria-modal="true">
        <button type="button" class="dvr-overlay-sluiten" data-dvr-overlay-sluiten aria-label="Sluiten">&times;</button>
        <div class="dvr-overlay-inhoud" id="dvr-overlay-inhoud"><?php if ( $fragment ) : ?><h1><?php echo esc_html( $fragment['title'] ); ?></h1><?php echo $fragment['html']; ?><?php endif; ?></div>
    </div>
</div>

<?php wp_footer(); ?>
</body>
</html>
    <?php
}
