<?php

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ] );

	register_nav_menus( [ 'primary' => __( 'Menu', 'droomvrouw' ) ] );

	add_image_size( 'dv-card', 720, 720, true );
	add_image_size( 'dv-slide', 1200, 900, true );
} );

add_action( 'wp_head', function () {
	echo "<script>document.documentElement.classList.add('js')</script>\n";
}, 0 );

add_action( 'wp_enqueue_scripts', function () {
	$dir = get_template_directory();
	$uri = get_template_directory_uri();

	wp_enqueue_style( 'droomvrouw', "$uri/assets/dist/main.css", [], filemtime( "$dir/assets/dist/main.css" ) );

	wp_enqueue_script_module( 'droomvrouw-menu', "$uri/assets/js/menu.js", [], filemtime( "$dir/assets/js/menu.js" ) );

	if ( is_tax( 'dv_line' ) || is_singular( 'dv_location' ) ) {
		wp_enqueue_script_module( 'droomvrouw-carousel', "$uri/assets/js/carousel.js", [], filemtime( "$dir/assets/js/carousel.js" ) );
	}
} );

add_action( 'wp_head', function () {
	$uri = get_template_directory_uri();
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( "$uri/assets/fonts/literata-latin-wght-normal.woff2" )
	);
}, 1 );

add_action( 'pre_get_posts', function ( WP_Query $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_tax( 'dv_line' ) ) {
		return;
	}
	foreach ( dv_stop_query_args() as $key => $value ) {
		$query->set( $key, $value );
	}
} );

add_action( 'after_switch_theme', function () {
	dv_register_content_types();
	flush_rewrite_rules();
} );
