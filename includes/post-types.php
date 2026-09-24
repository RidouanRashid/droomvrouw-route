<?php
/**
 * Custom Post Type "infopunt" en taxonomy "laag".
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function droomvrouwroute_registreer_infopunt() {
    $labels = array(
        'name'          => __( 'Informatiepunten', 'droomvrouwroute' ),
        'singular_name' => __( 'Informatiepunt', 'droomvrouwroute' ),
        'add_new_item'  => __( 'Nieuw informatiepunt toevoegen', 'droomvrouwroute' ),
        'edit_item'     => __( 'Informatiepunt bewerken', 'droomvrouwroute' ),
        'all_items'     => __( 'Alle informatiepunten', 'droomvrouwroute' ),
        'search_items'  => __( 'Informatiepunten zoeken', 'droomvrouwroute' ),
        'not_found'     => __( 'Geen informatiepunten gevonden', 'droomvrouwroute' ),
    );

    register_post_type( 'infopunt', array(
        'labels'       => $labels,
        'public'       => true,
        'show_in_rest' => true,
        'menu_icon'    => 'dashicons-location-alt',
        'supports'     => array( 'title', 'thumbnail' ),
        'has_archive'  => false,
        'rewrite'      => array( 'slug' => 'informatiepunt' ),
    ) );
}
add_action( 'init', 'droomvrouwroute_registreer_infopunt' );

function droomvrouwroute_registreer_laag() {
    $labels = array(
        'name'          => __( 'Lagen', 'droomvrouwroute' ),
        'singular_name' => __( 'Laag', 'droomvrouwroute' ),
        'add_new_item'  => __( 'Nieuwe laag toevoegen', 'droomvrouwroute' ),
        'edit_item'     => __( 'Laag bewerken', 'droomvrouwroute' ),
    );

    register_taxonomy( 'laag', array( 'infopunt' ), array(
        'labels'       => $labels,
        'public'       => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => array( 'slug' => 'laag' ),
    ) );
}
add_action( 'init', 'droomvrouwroute_registreer_laag' );
