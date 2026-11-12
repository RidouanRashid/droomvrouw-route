<?php

function dv_line_fields(): array {
	return [
		'dv_color'    => [ 'type' => 'color', 'label' => __( 'Kleur van de lijn', 'droomvrouw' ) ],
		'dv_subtitle' => [ 'type' => 'text', 'label' => __( 'Ondertitel', 'droomvrouw' ), 'help' => __( 'Bijvoorbeeld: drie plekken op de route', 'droomvrouw' ) ],
		'dv_intro'    => [ 'type' => 'textarea', 'label' => __( 'Introductie', 'droomvrouw' ) ],
		'dv_duration' => [ 'type' => 'text', 'label' => __( 'Duur', 'droomvrouw' ), 'help' => __( 'Bijvoorbeeld: ± 2 uur lopen', 'droomvrouw' ) ],
		'dv_order'    => [ 'type' => 'number', 'label' => __( 'Volgorde in het menu', 'droomvrouw' ) ],
	];
}

function dv_line_field_input( string $key, array $field, $value ): void {
	$id = esc_attr( $key );
	if ( 'textarea' === $field['type'] ) {
		printf( '<textarea id="%1$s" name="%1$s" rows="4" class="large-text">%2$s</textarea>', $id, esc_textarea( (string) $value ) );
	} else {
		$value = 'color' === $field['type'] ? ( $value ?: '#2f6e95' ) : $value;
		printf( '<input type="%1$s" id="%2$s" name="%2$s" value="%3$s"%4$s>', esc_attr( $field['type'] ), $id, esc_attr( (string) $value ), 'number' === $field['type'] ? ' min="0" class="small-text"' : ( 'text' === $field['type'] ? ' class="regular-text"' : '' ) );
	}
	if ( ! empty( $field['help'] ) ) {
		printf( '<p class="description">%s</p>', esc_html( $field['help'] ) );
	}
}

add_action( 'dv_line_add_form_fields', function () {
	wp_nonce_field( 'dv_save_line', 'dv_line_nonce' );
	foreach ( dv_line_fields() as $key => $field ) {
		echo '<div class="form-field">';
		printf( '<label for="%s">%s</label>', esc_attr( $key ), esc_html( $field['label'] ) );
		dv_line_field_input( $key, $field, '' );
		echo '</div>';
	}
} );

add_action( 'dv_line_edit_form_fields', function ( WP_Term $term ) {
	wp_nonce_field( 'dv_save_line', 'dv_line_nonce' );
	foreach ( dv_line_fields() as $key => $field ) {
		echo '<tr class="form-field">';
		printf( '<th scope="row"><label for="%s">%s</label></th><td>', esc_attr( $key ), esc_html( $field['label'] ) );
		dv_line_field_input( $key, $field, get_term_meta( $term->term_id, $key, true ) );
		echo '</td></tr>';
	}
} );

$dv_save_line = function ( int $term_id ) {
	if ( ! isset( $_POST['dv_line_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['dv_line_nonce'] ), 'dv_save_line' ) || ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	foreach ( array_keys( dv_line_fields() ) as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_term_meta( $term_id, $key, wp_unslash( $_POST[ $key ] ) );
		}
	}
};
add_action( 'created_dv_line', $dv_save_line );
add_action( 'edited_dv_line', $dv_save_line );
