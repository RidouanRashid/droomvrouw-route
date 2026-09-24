<?php
/**
 * SCF-instellingenpagina en veldgroepen voor infopunt, laag en de opties.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function droomvrouwroute_registreer_opties_pagina() {
    if ( function_exists( 'acf_add_options_page' ) ) {
        acf_add_options_page( array(
            'page_title' => __( 'Plattegrond instellingen', 'droomvrouwroute' ),
            'menu_title' => __( 'Plattegrond', 'droomvrouwroute' ),
            'menu_slug'  => 'plattegrond-instellingen',
            'capability' => 'manage_options',
            'icon_url'   => 'dashicons-location-alt',
        ) );
    }
}
add_action( 'acf/init', 'droomvrouwroute_registreer_opties_pagina' );

function droomvrouwroute_registreer_velden() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( array(
        'key'    => 'group_infopunt',
        'title'  => 'Informatiepunt gegevens',
        'fields' => array(
            array(
                'key'   => 'field_infopunt_beschrijving',
                'label' => 'Beschrijving',
                'name'  => 'beschrijving',
                'type'  => 'wysiwyg',
            ),
            array(
                'key'           => 'field_infopunt_foto',
                'label'         => 'Foto',
                'name'          => 'foto',
                'type'          => 'image',
                'return_format' => 'id',
            ),
            array(
                'key'   => 'field_infopunt_subthema',
                'label' => 'Subthema (bv. "In verhouding tot Kunst")',
                'name'  => 'subthema',
                'type'  => 'text',
            ),
            array(
                'key'           => 'field_infopunt_positie_x',
                'label'         => 'Positie X (% van links)',
                'name'          => 'positie_x',
                'type'          => 'number',
                'min'           => 0,
                'max'           => 100,
                'step'          => 0.1,
                'default_value' => 50,
                'instructions'  => 'Horizontale positie van dit punt op de plattegrond, als percentage (0 = links, 100 = rechts).',
            ),
            array(
                'key'           => 'field_infopunt_positie_y',
                'label'         => 'Positie Y (% van boven)',
                'name'          => 'positie_y',
                'type'          => 'number',
                'min'           => 0,
                'max'           => 100,
                'step'          => 0.1,
                'default_value' => 50,
                'instructions'  => 'Verticale positie van dit punt op de plattegrond, als percentage (0 = boven, 100 = onder).',
            ),
            array(
                'key'          => 'field_infopunt_social_link',
                'label'        => 'Instagram/Facebook-link (optioneel)',
                'name'         => 'social_link',
                'type'         => 'url',
                'instructions' => 'Link naar een specifieke Instagram- of Facebook-post. Verschijnt als knop bij dit informatiepunt.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'infopunt',
                ),
            ),
        ),
    ) );

    acf_add_local_field_group( array(
        'key'    => 'group_laag',
        'title'  => 'Laag gegevens',
        'fields' => array(
            array(
                'key'           => 'field_laag_overlay',
                'label'         => 'Lijn-overlay (transparante afbeelding)',
                'name'          => 'lijn_overlay',
                'type'          => 'image',
                'return_format' => 'id',
                'instructions'  => 'De afbeelding met alleen de gekleurde lijn van deze laag, met transparante achtergrond.',
            ),
            array(
                'key'   => 'field_laag_kleur',
                'label' => 'Kleur',
                'name'  => 'kleur',
                'type'  => 'color_picker',
            ),
            array(
                'key'           => 'field_laag_volgorde',
                'label'         => 'Volgorde',
                'name'          => 'volgorde',
                'type'          => 'number',
                'default_value' => 0,
                'instructions'  => 'Bepaalt de volgorde in het lagen-menu (laag getal = bovenaan). De laag met de laagste waarde staat standaard aan.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'taxonomy',
                    'operator' => '==',
                    'value'    => 'laag',
                ),
            ),
        ),
    ) );

    acf_add_local_field_group( array(
        'key'    => 'group_opties',
        'title'  => 'Plattegrond instellingen',
        'fields' => array(
            array(
                'key'           => 'field_opties_basiskaart',
                'label'         => 'Basiskaart',
                'name'          => 'basis_kaart',
                'type'          => 'image',
                'return_format' => 'id',
                'instructions'  => 'De onderliggende plattegrond die altijd zichtbaar blijft, ongeacht welke lagen actief zijn.',
            ),
            array(
                'key'   => 'field_opties_oude_site',
                'label' => 'URL oude website',
                'name'  => 'oude_website_url',
                'type'  => 'url',
            ),
            array(
                'key'   => 'field_opties_instagram',
                'label' => 'Instagram-URL',
                'name'  => 'instagram_url',
                'type'  => 'url',
            ),
            array(
                'key'   => 'field_opties_facebook',
                'label' => 'Facebook-URL',
                'name'  => 'facebook_url',
                'type'  => 'url',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'options_page',
                    'operator' => '==',
                    'value'    => 'plattegrond-instellingen',
                ),
            ),
        ),
    ) );
}
add_action( 'acf/init', 'droomvrouwroute_registreer_velden' );
