<?php

function dv_marker_types(): array {
	return [
		'circle'          => __( 'Cirkel', 'droomvrouw' ),
		'triangle'        => __( 'Driehoek', 'droomvrouw' ),
		'circle-triangle' => __( 'Cirkel + driehoek', 'droomvrouw' ),
	];
}

function dv_sanitize_marker( $value ): string {
	return array_key_exists( $value, dv_marker_types() ) ? $value : 'circle';
}

function dv_sanitize_id_list( $value ): array {
	if ( is_string( $value ) ) {
		$value = explode( ',', $value );
	}
	return array_values( array_unique( array_filter( array_map( 'absint', (array) $value ) ) ) );
}

add_action( 'init', function () {
	$can_edit = fn( $allowed, $meta_key, $post_id ) => current_user_can( 'edit_post', $post_id );

	register_post_meta( 'dv_location', 'dv_stop_number', [
		'type'              => 'integer',
		'single'            => true,
		'default'           => 0,
		'show_in_rest'      => true,
		'sanitize_callback' => 'absint',
		'auth_callback'     => $can_edit,
	] );

	register_post_meta( 'dv_location', 'dv_marker', [
		'type'              => 'string',
		'single'            => true,
		'default'           => 'circle',
		'show_in_rest'      => [ 'schema' => [ 'enum' => array_keys( dv_marker_types() ) ] ],
		'sanitize_callback' => 'dv_sanitize_marker',
		'auth_callback'     => $can_edit,
	] );

	register_post_meta( 'dv_location', 'dv_book_chapter', [
		'type'              => 'string',
		'single'            => true,
		'default'           => '',
		'show_in_rest'      => true,
		'sanitize_callback' => 'sanitize_text_field',
		'auth_callback'     => $can_edit,
	] );

	register_post_meta( 'dv_location', 'dv_gallery', [
		'type'              => 'array',
		'single'            => true,
		'default'           => [],
		'show_in_rest'      => [ 'schema' => [ 'type' => 'array', 'items' => [ 'type' => 'integer' ] ] ],
		'sanitize_callback' => 'dv_sanitize_id_list',
		'auth_callback'     => $can_edit,
	] );

	$term_fields = [
		'dv_color'    => [ 'string', 'sanitize_hex_color' ],
		'dv_subtitle' => [ 'string', 'sanitize_text_field' ],
		'dv_intro'    => [ 'string', 'sanitize_textarea_field' ],
		'dv_duration' => [ 'string', 'sanitize_text_field' ],
		'dv_order'    => [ 'integer', 'absint' ],
	];

	foreach ( $term_fields as $key => [ $type, $sanitize ] ) {
		register_term_meta( 'dv_line', $key, [
			'type'              => $type,
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => $sanitize,
		] );
	}
} );
