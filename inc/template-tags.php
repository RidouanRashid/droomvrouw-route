<?php

function dv_stop_query_args(): array {
	return [
		'posts_per_page' => -1,
		'no_found_rows'  => true,
		'meta_query'     => [
			'relation'  => 'OR',
			'dv_number' => [ 'key' => 'dv_stop_number', 'type' => 'NUMERIC' ],
			'dv_none'   => [ 'key' => 'dv_stop_number', 'compare' => 'NOT EXISTS' ],
		],
		'orderby'        => [ 'dv_number' => 'ASC', 'date' => 'ASC' ],
	];
}

function dv_line_stops( WP_Term $line ): array {
	return get_posts( array_merge( dv_stop_query_args(), [
		'post_type' => 'dv_location',
		'tax_query' => [ [ 'taxonomy' => 'dv_line', 'terms' => $line->term_id ] ],
	] ) );
}

function dv_lines(): array {
	$lines = get_terms( [ 'taxonomy' => 'dv_line', 'hide_empty' => false ] );
	if ( is_wp_error( $lines ) ) {
		return [];
	}
	usort( $lines, fn( $a, $b ) => (int) get_term_meta( $a->term_id, 'dv_order', true ) <=> (int) get_term_meta( $b->term_id, 'dv_order', true ) );
	return $lines;
}

function dv_location_line( int $post_id ): ?WP_Term {
	$terms = get_the_terms( $post_id, 'dv_line' );
	return ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
}

function dv_line_title( WP_Term $line ): string {
	return sprintf( __( 'de %s lijn', 'droomvrouw' ), $line->name );
}

function dv_line_color( ?WP_Term $line ): string {
	$color = $line ? get_term_meta( $line->term_id, 'dv_color', true ) : '';
	return $color ?: '#2f6e95';
}

function dv_is_light_color( string $hex ): bool {
	$hex = ltrim( $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	[ $r, $g, $b ] = array_map( 'hexdec', str_split( $hex, 2 ) );
	return ( 0.2126 * $r + 0.7152 * $g + 0.0722 * $b ) / 255 > 0.8;
}

function dv_line_attributes( ?WP_Term $line, string $class = '' ): string {
	$color   = dv_line_color( $line );
	$classes = [ $class, 'straight' === dv_mod( 'line_style' ) ? 'line-straight' : 'line-drawn' ];
	if ( dv_is_light_color( $color ) ) {
		$classes[] = 'is-light-line';
	}
	return sprintf( 'class="%s" style="--line: %s"', esc_attr( trim( implode( ' ', $classes ) ) ), esc_attr( $color ) );
}

function dv_marker_shape( string $type, string $number ): string {
	$stroke = 'stroke="var(--marker-stroke, var(--line, #2f6e95))" stroke-width="4" stroke-linejoin="round"';
	$fill   = 'fill="var(--marker-fill, #f4ecc8)"';
	$text_y = 24;

	switch ( $type ) {
		case 'triangle':
			$shape  = "<path d=\"M24 4 L45 42 H3 Z\" $fill $stroke/>";
			$text_y = 30;
			break;
		case 'circle-triangle':
			$shape  = "<path d=\"M24 1 L46 39 H2 Z\" $fill $stroke/><circle cx=\"24\" cy=\"29\" r=\"13\" $fill $stroke/>";
			$text_y = 29;
			break;
		default:
			$shape = "<circle cx=\"24\" cy=\"24\" r=\"22\" $fill $stroke/>";
	}

	return sprintf(
		'<svg viewBox="0 0 48 48" class="size-full overflow-visible" focusable="false">%s<text x="24" y="%d" text-anchor="middle" dominant-baseline="central" font-family="Verdana, sans-serif" font-weight="700" font-size="15" fill="var(--marker-ink, #050333)">%s</text></svg>',
		$shape,
		$text_y,
		esc_html( $number )
	);
}

function dv_marker_image_url( string $type ): string {
	$id = (int) dv_mod( "marker_image_$type" );
	if ( $id && ( $url = wp_get_attachment_image_url( $id, 'thumbnail' ) ) ) {
		return $url;
	}
	return get_template_directory_uri() . "/assets/img/markers/$type.svg";
}

function dv_marker( int $number, bool $active = false, string $class = 'size-12' ): string {
	$type    = 'circle';
	$classes = "dv-marker dv-marker--$type relative inline-grid shrink-0 place-items-center $class" . ( $active ? ' is-active' : '' );

	if ( 'image' === dv_mod( 'marker_style' ) ) {
		return sprintf(
			'<span class="%s dv-marker--image" aria-hidden="true"><img src="%s" alt="" class="absolute inset-0 size-full object-contain" loading="lazy" decoding="async"><span class="dv-marker__number relative font-sans text-sm font-bold">%d</span></span>',
			esc_attr( $classes ),
			esc_url( dv_marker_image_url( $type ) ),
			$number
		);
	}

	return sprintf( '<span class="%s" aria-hidden="true">%s</span>', esc_attr( $classes ), dv_marker_shape( $type, (string) $number ) );
}

function dv_gallery_ids( int $post_id ): array {
	$ids = array_filter( dv_sanitize_id_list( get_post_meta( $post_id, 'dv_gallery', true ) ), fn( $id ) => wp_attachment_is_image( $id ) );
	if ( ! $ids && has_post_thumbnail( $post_id ) ) {
		$ids = [ get_post_thumbnail_id( $post_id ) ];
	}
	return array_values( $ids );
}

function dv_card_image_id( int $post_id ): int {
	return has_post_thumbnail( $post_id ) ? (int) get_post_thumbnail_id( $post_id ) : (int) ( dv_gallery_ids( $post_id )[0] ?? 0 );
}

function dv_short_text( WP_Post $post ): string {
	return has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 24 );
}

function dv_book_chapter( int $post_id ): string {
	$chapter = get_post_meta( $post_id, 'dv_book_chapter', true );
	return $chapter ? sprintf( __( 'In het boek: %s', 'droomvrouw' ), $chapter ) : '';
}

function dv_old_site_label( string $url ): string {
	return preg_replace( '/^www\./', '', (string) wp_parse_url( $url, PHP_URL_HOST ) );
}
