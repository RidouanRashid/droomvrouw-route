<?php

add_action( 'add_meta_boxes_dv_location', function () {
	add_meta_box( 'dv_location_details', __( 'Halte', 'droomvrouw' ), 'dv_render_details_box', 'dv_location', 'normal', 'high' );
	add_meta_box( 'dv_location_gallery', __( 'Galerij', 'droomvrouw' ), 'dv_render_gallery_box', 'dv_location', 'normal', 'high' );
} );

function dv_render_details_box( WP_Post $post ): void {
	$number  = (int) get_post_meta( $post->ID, 'dv_stop_number', true );
	$chapter = get_post_meta( $post->ID, 'dv_book_chapter', true );
	$marker  = dv_sanitize_marker( get_post_meta( $post->ID, 'dv_marker', true ) );
	wp_nonce_field( 'dv_save_location', 'dv_location_nonce' );
	?>
	<div class="dv-fields">
		<p>
			<label for="dv_stop_number"><strong><?php esc_html_e( 'Haltenummer', 'droomvrouw' ); ?></strong></label><br>
			<input type="number" min="0" step="1" id="dv_stop_number" name="dv_stop_number" value="<?php echo esc_attr( $number ); ?>" class="small-text" aria-describedby="dv_stop_number_help">
			<span id="dv_stop_number_help" class="description"><?php esc_html_e( 'Bepaalt de volgorde op de lijn. Op de website worden de haltes doorgenummerd vanaf 1.', 'droomvrouw' ); ?></span>
		</p>
		<p>
			<label for="dv_book_chapter"><strong><?php esc_html_e( 'In het boek', 'droomvrouw' ); ?></strong></label><br>
			<input type="text" id="dv_book_chapter" name="dv_book_chapter" value="<?php echo esc_attr( $chapter ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'hoofdstuk 3', 'droomvrouw' ); ?>">
		</p>
		<fieldset>
			<legend><strong><?php esc_html_e( 'Markering op de lijn', 'droomvrouw' ); ?></strong></legend>
			<div class="dv-marker-choices">
				<?php foreach ( dv_marker_types() as $type => $label ) : ?>
					<label class="dv-marker-choice">
						<input type="radio" name="dv_marker" value="<?php echo esc_attr( $type ); ?>" <?php checked( $marker, $type ); ?>>
						<span class="dv-marker-choice__shape" aria-hidden="true"><?php echo dv_marker_shape( $type, '1' ); ?></span>
						<span><?php echo esc_html( $label ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</fieldset>
	</div>
	<?php
}

function dv_gallery_admin_item( int $id = 0 ): string {
	$thumb   = $id ? wp_get_attachment_image_url( $id, 'thumbnail' ) : '';
	$missing = $id && '' === trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
	ob_start();
	?>
	<li class="dv-gallery__item" data-id="<?php echo esc_attr( $id ); ?>">
		<img src="<?php echo esc_url( $thumb ); ?>" alt="">
		<span class="dv-gallery__alert" <?php echo $missing ? '' : 'hidden'; ?>><?php esc_html_e( 'Geen alt-tekst', 'droomvrouw' ); ?></span>
		<span class="dv-gallery__actions">
			<button type="button" class="button-link" data-action="left" aria-label="<?php esc_attr_e( 'Naar links', 'droomvrouw' ); ?>">&larr;</button>
			<button type="button" class="button-link" data-action="right" aria-label="<?php esc_attr_e( 'Naar rechts', 'droomvrouw' ); ?>">&rarr;</button>
			<button type="button" class="button-link dv-gallery__remove" data-action="remove" aria-label="<?php esc_attr_e( 'Verwijderen', 'droomvrouw' ); ?>">&times;</button>
		</span>
	</li>
	<?php
	return ob_get_clean();
}

function dv_render_gallery_box( WP_Post $post ): void {
	$ids = dv_sanitize_id_list( get_post_meta( $post->ID, 'dv_gallery', true ) );
	?>
	<div class="dv-gallery" data-dv-gallery>
		<p class="description"><?php esc_html_e( 'Deze afbeeldingen staan onder de lijn op de pagina van de halte. Vul in de mediabibliotheek bij elke afbeelding een alt-tekst in: een korte beschrijving voor bezoekers die de afbeelding niet zien.', 'droomvrouw' ); ?></p>
		<input type="hidden" name="dv_gallery" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>">
		<ul class="dv-gallery__list" data-dv-gallery-list>
			<?php
			foreach ( $ids as $id ) {
				echo dv_gallery_admin_item( $id );
			}
			?>
		</ul>
		<template data-dv-gallery-template><?php echo dv_gallery_admin_item(); ?></template>
		<p><button type="button" class="button" data-dv-gallery-add><?php esc_html_e( 'Afbeeldingen toevoegen', 'droomvrouw' ); ?></button></p>
	</div>
	<?php
}

add_action( 'save_post_dv_location', function ( int $post_id ) {
	if ( ! isset( $_POST['dv_location_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['dv_location_nonce'] ), 'dv_save_location' ) ) {
		return;
	}
	if ( wp_is_post_autosave( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, 'dv_stop_number', absint( $_POST['dv_stop_number'] ?? 0 ) );
	update_post_meta( $post_id, 'dv_book_chapter', sanitize_text_field( wp_unslash( $_POST['dv_book_chapter'] ?? '' ) ) );
	update_post_meta( $post_id, 'dv_marker', dv_sanitize_marker( sanitize_key( $_POST['dv_marker'] ?? '' ) ) );
	update_post_meta( $post_id, 'dv_gallery', dv_sanitize_id_list( sanitize_text_field( wp_unslash( $_POST['dv_gallery'] ?? '' ) ) ) );
} );

add_action( 'admin_enqueue_scripts', function ( string $hook ) {
	if ( ! in_array( $hook, [ 'post.php', 'post-new.php', 'edit.php' ], true ) || 'dv_location' !== get_current_screen()->post_type ) {
		return;
	}
	$dir = get_template_directory();
	$uri = get_template_directory_uri();
	wp_enqueue_style( 'droomvrouw-admin', "$uri/assets/admin/admin.css", [], filemtime( "$dir/assets/admin/admin.css" ) );
	if ( 'edit.php' !== $hook ) {
		wp_enqueue_media();
		wp_enqueue_script( 'droomvrouw-gallery', "$uri/assets/admin/gallery.js", [], filemtime( "$dir/assets/admin/gallery.js" ), true );
	}
} );

add_filter( 'manage_dv_location_posts_columns', function ( array $columns ) {
	unset( $columns['taxonomy-dv_line'] );
	return [ 'cb' => $columns['cb'], 'dv_photo' => __( 'Foto', 'droomvrouw' ) ]
		+ array_slice( $columns, 1, 1, true )
		+ [
			'dv_line'        => __( 'Lijn', 'droomvrouw' ),
			'dv_stop_number' => __( 'Nr.', 'droomvrouw' ),
			'dv_text'        => __( 'Tekst', 'droomvrouw' ),
			'dv_gallery'     => __( "Foto's", 'droomvrouw' ),
			'dv_marker'      => __( 'Markering', 'droomvrouw' ),
		]
		+ array_slice( $columns, 2, null, true );
} );

add_action( 'manage_dv_location_posts_custom_column', function ( string $column, int $post_id ) {
	if ( 'dv_photo' === $column ) {
		$ids   = dv_sanitize_id_list( get_post_meta( $post_id, 'dv_gallery', true ) );
		$image = get_post_thumbnail_id( $post_id ) ?: ( $ids[0] ?? 0 );
		if ( $image ) {
			printf( '<a href="%s">%s</a>', esc_url( get_edit_post_link( $post_id ) ), wp_get_attachment_image( $image, 'thumbnail', false, [ 'class' => 'dv-list-photo', 'alt' => '' ] ) );
		} else {
			echo '<span class="dv-list-photo dv-list-photo--empty">' . esc_html__( 'geen foto', 'droomvrouw' ) . '</span>';
		}
	}
	if ( 'dv_line' === $column ) {
		foreach ( get_the_terms( $post_id, 'dv_line' ) ?: [] as $term ) {
			$color = get_term_meta( $term->term_id, 'dv_color', true ) ?: '#999999';
			printf(
				'<a class="dv-line-badge" href="%s"><span class="dv-line-badge__dot" style="background:%s"></span>%s</a>',
				esc_url( add_query_arg( [ 'post_type' => 'dv_location', 'dv_line' => $term->slug ], admin_url( 'edit.php' ) ) ),
				esc_attr( sanitize_hex_color( $color ) ?: '#999999' ),
				esc_html( $term->name )
			);
		}
	}
	if ( 'dv_text' === $column ) {
		$text = wp_trim_words( get_the_excerpt( $post_id ), 18 );
		echo $text ? esc_html( $text ) : '<em>' . esc_html__( 'nog geen tekst', 'droomvrouw' ) . '</em>';
	}
	if ( 'dv_gallery' === $column ) {
		echo esc_html( count( dv_sanitize_id_list( get_post_meta( $post_id, 'dv_gallery', true ) ) ) );
	}
	if ( 'dv_stop_number' === $column ) {
		echo esc_html( (int) get_post_meta( $post_id, 'dv_stop_number', true ) );
	}
	if ( 'dv_marker' === $column ) {
		$type = dv_sanitize_marker( get_post_meta( $post_id, 'dv_marker', true ) );
		printf( '<span class="dv-marker-column" title="%1$s">%2$s<span class="screen-reader-text">%1$s</span></span>', esc_attr( dv_marker_types()[ $type ] ), dv_marker_shape( $type, '' ) );
	}
}, 10, 2 );
