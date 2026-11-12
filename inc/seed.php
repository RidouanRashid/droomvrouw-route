<?php

function dv_demo_images(): array {
	return [
		'uitzichtberg'      => __( 'Een man bevestigt een blauw papieren werk op een raam, met de straat erachter weerspiegeld', 'droomvrouw' ),
		'bord-in-het-park'  => __( 'Een gescheurd bord met tekst in een groen park', 'droomvrouw' ),
		'cyanotypie-weg'    => __( 'Blauwe cyanotypie van een weg met een witte middenstreep', 'droomvrouw' ),
		'blauwe-cirkel'     => __( 'Een blauwe geschilderde cirkel over een foto van een straat met een vrouw', 'droomvrouw' ),
		'trap-in-het-groen' => __( 'Een oude stenen trap tussen bomen en klimop', 'droomvrouw' ),
		'cyanotypie-kaart'  => __( 'Blauwe cyanotypie op gelinieerd papier met kleine symbolen', 'droomvrouw' ),
	];
}

function dv_demo_paragraphs( string ...$paragraphs ): string {
	return implode( "\n\n", array_map( fn( $p ) => "<!-- wp:paragraph -->\n<p>" . esc_html( $p ) . "</p>\n<!-- /wp:paragraph -->", $paragraphs ) );
}

function dv_demo_content(): array {
	$story = __( 'Hier komt het langere verhaal: wat de Droomvrouw hier deed of maakte en wat je nu nog kunt zien.', 'droomvrouw' );
	$walk  = __( 'Neem de tijd om rond te kijken. Wat valt je op als je stil blijft staan?', 'droomvrouw' );

	return [
		'lines'     => [
			'witte'   => [ 'dv_color' => '#ffffff', 'dv_order' => 1, 'dv_subtitle' => __( 'drie plekken op de route', 'droomvrouw' ), 'dv_duration' => __( '± 45 minuten lopen', 'droomvrouw' ), 'dv_intro' => __( 'De stilste lijn: drie plekken waar bijna niets te zien is, en juist daarom veel.', 'droomvrouw' ) ],
			'rode'    => [ 'dv_color' => '#b5473a', 'dv_order' => 2, 'dv_subtitle' => __( 'drie plekken op de route', 'droomvrouw' ), 'dv_duration' => __( '± 1 uur lopen', 'droomvrouw' ), 'dv_intro' => __( 'Een korte lijn door de stad, langs de plekken waar het begon.', 'droomvrouw' ) ],
			'blauwe'  => [ 'dv_color' => '#2f6e95', 'dv_order' => 3, 'dv_subtitle' => __( 'vijf plekken op de route', 'droomvrouw' ), 'dv_duration' => __( '± 2 uur lopen', 'droomvrouw' ), 'dv_intro' => __( 'Volg de lijn, halte voor halte. Elke plek is een stukje van het verhaal van de Droomvrouw.', 'droomvrouw' ) ],
			'groene'  => [ 'dv_color' => '#5e8a4f', 'dv_order' => 4, 'dv_subtitle' => __( 'vier plekken op de route', 'droomvrouw' ), 'dv_duration' => __( '± 1,5 uur lopen', 'droomvrouw' ), 'dv_intro' => __( 'Een lijn door het groen, van het park tot aan de rand van het bos.', 'droomvrouw' ) ],
		],
		'locations' => [
			[ 'blauwe', 1, 'Stuttgart (uitzichtberg) / Waiblingen', 'circle', 'hoofdstuk 3', [ 'uitzichtberg', 'blauwe-cirkel', 'cyanotypie-weg' ],
				__( 'Wat er te vertellen valt, met een beeld van deze locatie. Dit is de plek op de route waar je uitkijkt over de stad.', 'droomvrouw' ) ],
			[ 'blauwe', 2, 'Tweede halte', 'triangle', 'hoofdstuk 5', [ 'blauwe-cirkel' ],
				__( 'Een korte beschrijving van de plek en wat de Droomvrouw hier deed of maakte.', 'droomvrouw' ) ],
			[ 'blauwe', 3, 'De trap in het groen', 'circle-triangle', 'hoofdstuk 7', [ 'trap-in-het-groen', 'bord-in-het-park', 'cyanotypie-weg', 'uitzichtberg', 'cyanotypie-kaart' ],
				__( 'Wat je hier kunt ervaren, plus een verwijzing naar het boek waarin deze plek staat.', 'droomvrouw' ) ],
			[ 'blauwe', 4, 'De weg verder', 'circle', 'hoofdstuk 8', [ 'cyanotypie-weg', 'bord-in-het-park' ],
				__( 'Het pad loopt door. Wat je hier ziet, verandert met de seizoenen.', 'droomvrouw' ) ],
			[ 'blauwe', 5, 'Eindpunt', 'triangle', 'hoofdstuk 9', [ 'cyanotypie-kaart', 'uitzichtberg', 'blauwe-cirkel', 'cyanotypie-weg', 'trap-in-het-groen', 'bord-in-het-park', 'uitzichtberg', 'cyanotypie-kaart' ],
				__( 'Het laatste stuk van de lijn, en waar je vandaag nog sporen van de Droomvrouw vindt.', 'droomvrouw' ) ],
			[ 'rode', 1, 'Het atelier', 'circle-triangle', 'hoofdstuk 1', [ 'blauwe-cirkel', 'cyanotypie-kaart' ],
				__( 'De plek waar de eerste werken ontstonden.', 'droomvrouw' ) ],
			[ 'rode', 2, 'De markt', 'circle', 'hoofdstuk 2', [ 'bord-in-het-park', 'uitzichtberg', 'trap-in-het-groen', 'cyanotypie-weg' ],
				__( 'Hier ontmoette de Droomvrouw de mensen van de stad.', 'droomvrouw' ) ],
			[ 'rode', 3, 'Aan het water', 'triangle', '', [ 'cyanotypie-weg' ],
				__( 'Een rustige plek om even stil te staan.', 'droomvrouw' ) ],
			[ 'witte', 1, 'Het lege blad', 'circle', 'hoofdstuk 4', [ 'cyanotypie-kaart', 'cyanotypie-weg' ],
				__( 'Waar de Droomvrouw begon met niets dan papier en licht.', 'droomvrouw' ) ],
			[ 'witte', 2, 'Het raam', 'triangle', 'hoofdstuk 6', [ 'uitzichtberg', 'blauwe-cirkel', 'cyanotypie-kaart' ],
				__( 'Een raam waar een werk hing, met de straat erin weerspiegeld.', 'droomvrouw' ) ],
			[ 'witte', 3, 'De stilte', 'circle-triangle', '', [ 'cyanotypie-weg' ],
				__( 'Het einde van de lijn. Hier hoef je niets te doen.', 'droomvrouw' ) ],
			[ 'groene', 1, 'Het park', 'circle', 'hoofdstuk 10', [ 'bord-in-het-park', 'trap-in-het-groen' ],
				__( 'Tussen de bomen staat een bord met een tekst van de Droomvrouw.', 'droomvrouw' ) ],
			[ 'groene', 2, 'De oude trap', 'circle-triangle', 'hoofdstuk 11', [ 'trap-in-het-groen', 'bord-in-het-park', 'uitzichtberg' ],
				__( 'Een stenen trap onder de klimop, die nergens meer heen lijkt te gaan.', 'droomvrouw' ) ],
			[ 'groene', 3, 'De open plek', 'triangle', 'hoofdstuk 12', [ 'cyanotypie-weg', 'trap-in-het-groen' ],
				__( 'Hier maakte de Droomvrouw haar afdrukken in de zon.', 'droomvrouw' ) ],
			[ 'groene', 4, 'De rand van het bos', 'circle', '', [ 'bord-in-het-park', 'cyanotypie-kaart', 'trap-in-het-groen', 'cyanotypie-weg' ],
				__( 'Waar het pad ophoudt en de route verder gaat in je hoofd.', 'droomvrouw' ) ],
		],
		'menu'      => [
			__( 'De Droomvrouw', 'droomvrouw' )         => '/de-droomvrouw/',
			__( 'Zelf de route volgen', 'droomvrouw' )  => '/zelf-de-route-volgen/',
			__( 'Creatieve begeleiding', 'droomvrouw' ) => '/creatieve-begeleiding/',
			__( 'Kunst', 'droomvrouw' )                 => '/kunst/',
			__( 'Projecten', 'droomvrouw' )             => '/projecten/',
			__( 'Nieuwtjes', 'droomvrouw' )             => '/nieuwtjes/',
			__( 'Contact', 'droomvrouw' )               => '/contact/',
		],
		'story'     => [ $story, $walk ],
	];
}

function dv_demo_import_image( string $key, string $alt ): int {
	$existing = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_dv_demo_key', 'meta_value' => $key, 'fields' => 'ids', 'numberposts' => 1 ] );
	if ( $existing ) {
		return (int) $existing[0];
	}

	$source = get_template_directory() . "/assets/img/seed/$key.jpg";
	$tmp    = wp_tempnam( "$key.jpg" );
	copy( $source, $tmp );

	$id = media_handle_sideload( [ 'name' => "$key.jpg", 'tmp_name' => $tmp ], 0, null, [ 'post_title' => ucfirst( str_replace( '-', ' ', $key ) ) ] );
	if ( is_wp_error( $id ) ) {
		wp_delete_file( $tmp );
		return 0;
	}

	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	update_post_meta( $id, '_dv_demo_key', $key );
	return $id;
}

function dv_seed_demo(): void {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$content = dv_demo_content();

	$images = [];
	foreach ( dv_demo_images() as $key => $alt ) {
		$images[ $key ] = dv_demo_import_image( $key, $alt );
	}

	$lines = [];
	foreach ( $content['lines'] as $name => $meta ) {
		$term = term_exists( $name, 'dv_line' );
		if ( ! $term ) {
			$term = wp_insert_term( $name, 'dv_line' );
			if ( is_wp_error( $term ) ) {
				continue;
			}
			update_term_meta( $term['term_id'], '_dv_demo', 1 );
		}
		if ( get_term_meta( $term['term_id'], '_dv_demo', true ) ) {
			foreach ( $meta as $key => $value ) {
				if ( '' === get_term_meta( $term['term_id'], $key, true ) ) {
					update_term_meta( $term['term_id'], $key, $value );
				}
			}
		}
		$lines[ $name ] = (int) $term['term_id'];
	}

	foreach ( $content['locations'] as [ $line, $number, $title, $marker, $chapter, $gallery, $excerpt ] ) {
		$key = sanitize_title( "$line-$title" );
		if ( get_posts( [ 'post_type' => 'dv_location', 'post_status' => 'any', 'meta_key' => '_dv_demo_key', 'meta_value' => $key, 'fields' => 'ids', 'numberposts' => 1 ] ) ) {
			continue;
		}

		$ids     = array_values( array_filter( array_map( fn( $image ) => $images[ $image ] ?? 0, $gallery ) ) );
		$post_id = wp_insert_post( [
			'post_type'    => 'dv_location',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_excerpt' => $excerpt,
			'post_content' => dv_demo_paragraphs( ...$content['story'] ),
			'tax_input'    => [ 'dv_line' => [ $lines[ $line ] ?? 0 ] ],
			'meta_input'   => [
				'_dv_demo_key'    => $key,
				'dv_stop_number'  => $number,
				'dv_marker'       => $marker,
				'dv_book_chapter' => $chapter,
				'dv_gallery'      => array_values( array_unique( $ids ) ),
			],
		] );

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			wp_set_object_terms( $post_id, [ $lines[ $line ] ?? 0 ], 'dv_line' );
			if ( $ids ) {
				set_post_thumbnail( $post_id, $ids[0] );
			}
		}
	}

	$locations = get_nav_menu_locations();
	if ( empty( $locations['primary'] ) && ! wp_get_nav_menu_object( 'Menu' ) ) {
		$menu_id = wp_create_nav_menu( 'Menu' );
		if ( ! is_wp_error( $menu_id ) ) {
			foreach ( $content['menu'] as $title => $path ) {
				wp_update_nav_menu_item( $menu_id, 0, [
					'menu-item-title'  => $title,
					'menu-item-url'    => home_url( $path ),
					'menu-item-type'   => 'custom',
					'menu-item-status' => 'publish',
				] );
			}
			$locations['primary'] = $menu_id;
			set_theme_mod( 'nav_menu_locations', $locations );
			update_option( 'dv_demo_menu', $menu_id );
		}
	}

	flush_rewrite_rules();
}

function dv_remove_demo(): void {
	$posts = get_posts( [ 'post_type' => [ 'dv_location', 'attachment' ], 'post_status' => 'any', 'meta_key' => '_dv_demo_key', 'fields' => 'ids', 'numberposts' => -1 ] );
	foreach ( $posts as $id ) {
		'attachment' === get_post_type( $id ) ? wp_delete_attachment( $id, true ) : wp_delete_post( $id, true );
	}

	$terms = get_terms( [ 'taxonomy' => 'dv_line', 'hide_empty' => false, 'meta_key' => '_dv_demo', 'fields' => 'ids' ] );
	foreach ( is_wp_error( $terms ) ? [] : $terms as $term_id ) {
		wp_delete_term( $term_id, 'dv_line' );
	}

	if ( $menu_id = (int) get_option( 'dv_demo_menu' ) ) {
		wp_delete_nav_menu( $menu_id );
		delete_option( 'dv_demo_menu' );
	}
}

add_action( 'admin_menu', function () {
	add_management_page( __( 'Droomvrouw demo-inhoud', 'droomvrouw' ), __( 'Droomvrouw demo', 'droomvrouw' ), 'manage_options', 'droomvrouw-demo', function () {
		$done = sanitize_key( $_GET['dv-demo'] ?? '' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Droomvrouw demo-inhoud', 'droomvrouw' ); ?></h1>
			<?php if ( 'seeded' === $done ) : ?>
				<div class="notice notice-success"><p><?php esc_html_e( 'De demo-inhoud is aangemaakt.', 'droomvrouw' ); ?> <a href="<?php echo esc_url( (string) get_term_link( 'blauwe', 'dv_line' ) ); ?>"><?php esc_html_e( 'Bekijk de blauwe lijn', 'droomvrouw' ); ?></a></p></div>
			<?php elseif ( 'removed' === $done ) : ?>
				<div class="notice notice-success"><p><?php esc_html_e( 'De demo-inhoud is verwijderd.', 'droomvrouw' ); ?></p></div>
			<?php endif; ?>
			<p><?php esc_html_e( 'Maakt de vier lijnen aan met haltes op elke lijn, voorbeeldfoto’s en een menu. Bestaande inhoud blijft staan.', 'droomvrouw' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block;margin-right:1em">
				<?php wp_nonce_field( 'dv_demo' ); ?>
				<input type="hidden" name="action" value="dv_seed_demo">
				<?php submit_button( __( 'Demo-inhoud aanmaken', 'droomvrouw' ), 'primary', 'submit', false ); ?>
			</form>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block">
				<?php wp_nonce_field( 'dv_demo' ); ?>
				<input type="hidden" name="action" value="dv_remove_demo">
				<?php submit_button( __( 'Demo-inhoud verwijderen', 'droomvrouw' ), 'secondary', 'submit', false ); ?>
			</form>
		</div>
		<?php
	} );
} );

foreach ( [ 'dv_seed_demo' => 'seeded', 'dv_remove_demo' => 'removed' ] as $dv_action => $dv_result ) {
	add_action( "admin_post_$dv_action", function () use ( $dv_action, $dv_result ) {
		check_admin_referer( 'dv_demo' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Je hebt geen toestemming hiervoor.', 'droomvrouw' ) );
		}
		$dv_action();
		wp_safe_redirect( admin_url( "tools.php?page=droomvrouw-demo&dv-demo=$dv_result" ) );
		exit;
	} );
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'droomvrouw seed', function () {
		dv_seed_demo();
		WP_CLI::success( 'Demo-inhoud aangemaakt.' );
	} );
	WP_CLI::add_command( 'droomvrouw unseed', function () {
		dv_remove_demo();
		WP_CLI::success( 'Demo-inhoud verwijderd.' );
	} );
}
