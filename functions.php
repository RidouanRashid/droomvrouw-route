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
