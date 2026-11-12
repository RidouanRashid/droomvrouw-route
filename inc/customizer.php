<?php

function dv_defaults(): array {
	return [
		'marker_style'                 => 'css',
		'marker_image_circle'          => 0,
		'marker_image_triangle'        => 0,
		'marker_image_circle-triangle' => 0,
		'line_layout'                  => 'horizontal',
		'line_style'                   => 'drawn',
		'cta_heading'                  => __( 'Maak nu je droom waar', 'droomvrouw' ),
		'cta_text'                     => __( 'Zelf de route volgen', 'droomvrouw' ),
		'cta_url'                      => '',
		'old_site_label'               => __( 'Geschiedenis:', 'droomvrouw' ),
		'old_site_url'                 => 'https://barbaravanbeers.nl',
	];
}

function dv_mod( string $key ) {
	return get_theme_mod( "dv_$key", dv_defaults()[ $key ] ?? '' );
}

add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ) {
	$defaults = dv_defaults();

	$wp_customize->add_section( 'droomvrouw_route', [
		'title'    => __( 'Droomvrouw route', 'droomvrouw' ),
		'priority' => 30,
	] );

	$add = function ( string $key, string $sanitize, array $control ) use ( $wp_customize, $defaults ) {
		$wp_customize->add_setting( "dv_$key", [
			'default'           => $defaults[ $key ],
			'sanitize_callback' => $sanitize,
		] );
		$wp_customize->add_control( "dv_$key", array_merge( [ 'section' => 'droomvrouw_route' ], $control ) );
	};

	$choice = fn( array $choices ) => fn( $value ) => array_key_exists( $value, $choices ) ? $value : array_key_first( $choices );

	$marker_styles = [
		'css'   => __( 'Vormen (CSS/SVG)', 'droomvrouw' ),
		'image' => __( 'Eigen afbeeldingen', 'droomvrouw' ),
	];
	$wp_customize->add_setting( 'dv_marker_style', [ 'default' => 'css', 'sanitize_callback' => $choice( $marker_styles ) ] );
	$wp_customize->add_control( 'dv_marker_style', [
		'section'     => 'droomvrouw_route',
		'type'        => 'radio',
		'label'       => __( 'Markeringen', 'droomvrouw' ),
		'description' => __( 'Kies tussen getekende vormen of de afbeeldingen hieronder.', 'droomvrouw' ),
		'choices'     => $marker_styles,
	] );

	foreach ( dv_marker_types() as $type => $label ) {
		$wp_customize->add_setting( "dv_marker_image_$type", [ 'default' => 0, 'sanitize_callback' => 'absint' ] );
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, "dv_marker_image_$type", [
			'section'   => 'droomvrouw_route',
			'label'     => sprintf( __( 'Afbeelding: %s', 'droomvrouw' ), strtolower( $label ) ),
			'mime_type' => 'image',
		] ) );
	}

	$layouts = [
		'horizontal' => __( 'Naast elkaar (standaard)', 'droomvrouw' ),
		'vertical'   => __( 'Verticaal, de lijn naar beneden', 'droomvrouw' ),
	];
	$wp_customize->add_setting( 'dv_line_layout', [ 'default' => 'horizontal', 'sanitize_callback' => $choice( $layouts ) ] );
	$wp_customize->add_control( 'dv_line_layout', [
		'section' => 'droomvrouw_route',
		'type'    => 'radio',
		'label'   => __( 'Weergave van een lijn', 'droomvrouw' ),
		'choices' => $layouts,
	] );

	$styles = [
		'drawn'    => __( 'Getekend', 'droomvrouw' ),
		'straight' => __( 'Strak', 'droomvrouw' ),
	];
	$wp_customize->add_setting( 'dv_line_style', [ 'default' => 'drawn', 'sanitize_callback' => $choice( $styles ) ] );
	$wp_customize->add_control( 'dv_line_style', [
		'section' => 'droomvrouw_route',
		'type'    => 'radio',
		'label'   => __( 'Stijl van de lijn', 'droomvrouw' ),
		'choices' => $styles,
	] );

	$add( 'cta_heading', 'sanitize_text_field', [ 'label' => __( 'Tekst aan het eind van de lijn', 'droomvrouw' ), 'description' => __( 'Alleen zichtbaar in de verticale weergave.', 'droomvrouw' ) ] );
	$add( 'cta_text', 'sanitize_text_field', [ 'label' => __( 'Knoptekst', 'droomvrouw' ) ] );
	$add( 'cta_url', 'esc_url_raw', [ 'label' => __( 'Knoplink', 'droomvrouw' ), 'type' => 'url' ] );
	$add( 'old_site_label', 'sanitize_text_field', [ 'label' => __( 'Label oude website', 'droomvrouw' ) ] );
	$add( 'old_site_url', 'esc_url_raw', [ 'label' => __( 'Link naar de oude website', 'droomvrouw' ), 'type' => 'url' ] );
} );
