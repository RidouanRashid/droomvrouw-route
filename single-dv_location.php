<?php
get_header();
the_post();

$dv_post    = get_post();
$dv_term    = dv_location_line( $dv_post->ID );
$dv_stops   = $dv_term ? dv_line_stops( $dv_term ) : [ $dv_post ];
$dv_ids     = wp_list_pluck( $dv_stops, 'ID' );
$dv_index   = (int) array_search( $dv_post->ID, $dv_ids, true );
$dv_total   = count( $dv_stops );
$dv_prev    = $dv_stops[ $dv_index - 1 ] ?? null;
$dv_next    = $dv_stops[ $dv_index + 1 ] ?? null;
$dv_chapter = dv_book_chapter( $dv_post->ID );
?>

<main id="main" <?php echo dv_line_attributes( $dv_term, 'relative isolate overflow-x-clip pt-10 lg:pt-12' ); ?>>
	<?php get_template_part( 'template-parts/route-wash' ); ?>

	<div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-16">
		<div class="lg:ml-[24%]">
			<?php if ( $dv_term ) : ?>
				<?php get_template_part( 'template-parts/journey/line-head', null, [ 'term' => $dv_term, 'stops' => $dv_stops, 'heading' => 'p' ] ); ?>
				<?php
				get_template_part( 'template-parts/journey/overview', null, [
					'stops'   => $dv_stops,
					'current' => $dv_post->ID,
					'mode'    => 'page',
					'tail'    => true,
					'label'   => sprintf( __( 'Haltes op %s', 'droomvrouw' ), dv_line_title( $dv_term ) ),
					'class'   => 'mt-6 lg:mt-8',
				] );
				?>
			<?php endif; ?>

			<?php get_template_part( 'template-parts/journey/gallery', null, [ 'ids' => dv_gallery_ids( $dv_post->ID ) ] ); ?>

			<article <?php post_class( 'mt-10 max-w-2xl lg:mt-12' ); ?>>
				<h1 class="font-sans text-xl font-bold leading-snug tracking-[0.1em] text-ink lg:text-2xl">
					<?php if ( $dv_term ) : ?>
						<span class="sr-only"><?php printf( esc_html__( 'Halte %1$d van %2$d:', 'droomvrouw' ), $dv_index + 1, $dv_total ); ?></span>
					<?php endif; ?>
					<?php the_title(); ?>
				</h1>

				<?php if ( has_excerpt() ) : ?>
					<p class="mt-5 text-xl leading-relaxed"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>

				<div class="entry-content mt-5 text-lg leading-relaxed">
					<?php the_content(); ?>
				</div>

				<?php if ( $dv_chapter ) : ?>
					<p class="mt-6 font-sans text-sm tracking-[0.1em] text-muted"><?php echo esc_html( $dv_chapter ); ?></p>
				<?php endif; ?>
			</article>

			<?php if ( $dv_term ) : ?>
				<nav class="mt-16 grid gap-6 border-t border-rule pt-6 font-sans text-sm tracking-[0.1em] sm:grid-cols-3" aria-label="<?php esc_attr_e( 'Andere haltes', 'droomvrouw' ); ?>">
					<div>
						<?php if ( $dv_prev ) : ?>
							<a href="<?php echo esc_url( get_permalink( $dv_prev ) ); ?>" rel="prev" class="group block">
								<span class="text-muted">&larr; <?php esc_html_e( 'vorige halte', 'droomvrouw' ); ?></span>
								<span class="mt-1 block font-bold text-ink group-hover:underline"><?php echo esc_html( get_the_title( $dv_prev ) ); ?></span>
							</a>
						<?php endif; ?>
					</div>
					<div class="sm:text-center">
						<a href="<?php echo esc_url( get_term_link( $dv_term ) ); ?>" class="read-more"><?php printf( esc_html__( 'terug naar %s', 'droomvrouw' ), esc_html( dv_line_title( $dv_term ) ) ); ?></a>
					</div>
					<div class="sm:text-right">
						<?php if ( $dv_next ) : ?>
							<a href="<?php echo esc_url( get_permalink( $dv_next ) ); ?>" rel="next" class="group block">
								<span class="text-muted"><?php esc_html_e( 'volgende halte', 'droomvrouw' ); ?> &rarr;</span>
								<span class="mt-1 block font-bold text-ink group-hover:underline"><?php echo esc_html( get_the_title( $dv_next ) ); ?></span>
							</a>
						<?php endif; ?>
					</div>
				</nav>
			<?php endif; ?>
		</div>
	</div>
</main>

<?php get_footer(); ?>
