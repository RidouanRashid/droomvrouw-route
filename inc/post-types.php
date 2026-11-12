<?php

function dv_register_content_types(): void {
	register_post_type( 'dv_location', [
		'labels'        => [
			'name'               => __( 'Haltes', 'droomvrouw' ),
			'singular_name'      => __( 'Halte', 'droomvrouw' ),
			'add_new'            => __( 'Nieuwe halte', 'droomvrouw' ),
			'add_new_item'       => __( 'Nieuwe halte toevoegen', 'droomvrouw' ),
			'edit_item'          => __( 'Halte bewerken', 'droomvrouw' ),
			'new_item'           => __( 'Nieuwe halte', 'droomvrouw' ),
			'view_item'          => __( 'Halte bekijken', 'droomvrouw' ),
			'search_items'       => __( 'Haltes zoeken', 'droomvrouw' ),
			'not_found'          => __( 'Geen haltes gevonden', 'droomvrouw' ),
			'not_found_in_trash' => __( 'Geen haltes in de prullenbak', 'droomvrouw' ),
			'all_items'          => __( 'Alle haltes', 'droomvrouw' ),
			'featured_image'     => __( 'Hoofdafbeelding', 'droomvrouw' ),
			'set_featured_image' => __( 'Hoofdafbeelding instellen', 'droomvrouw' ),
			'item_excerpt'       => __( 'Korte tekst', 'droomvrouw' ),
		],
		'public'        => true,
		'show_in_rest'  => true,
		'menu_position' => 5,
		'menu_icon'     => 'dashicons-location-alt',
		'supports'      => [ 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ],
		'has_archive'   => false,
		'rewrite'       => [ 'slug' => 'halte' ],
	] );

	register_taxonomy( 'dv_line', [ 'dv_location' ], [
		'labels'            => [
			'name'          => __( 'Lijnen', 'droomvrouw' ),
			'singular_name' => __( 'Lijn', 'droomvrouw' ),
			'add_new_item'  => __( 'Nieuwe lijn toevoegen', 'droomvrouw' ),
			'edit_item'     => __( 'Lijn bewerken', 'droomvrouw' ),
			'search_items'  => __( 'Lijnen zoeken', 'droomvrouw' ),
			'all_items'     => __( 'Alle lijnen', 'droomvrouw' ),
			'not_found'     => __( 'Geen lijnen gevonden', 'droomvrouw' ),
		],
		'hierarchical'      => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => [ 'slug' => 'lijn' ],
	] );
}

add_action( 'init', 'dv_register_content_types' );
