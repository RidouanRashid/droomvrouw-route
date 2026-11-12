<?php
$dv_stops = $args['stops'];
$dv_total = count( $dv_stops );
$dv_label = sprintf( __( 'Haltes op %s', 'droomvrouw' ), dv_line_title( $args['term'] ) );
?>
<div class="mt-6 lg:mt-8" data-carousel>
	<?php
	get_template_part( 'template-parts/journey/overview', null, [
		'stops' => $dv_stops,
		'mode'  => 'anchor',
		'label' => $dv_label,
		'class' => 'mb-8 lg:hidden',
	] );
	?>

	<div class="relative @container [--arrow-top:calc((100cqw-6rem)/2)]">
		<div class="carousel-track" role="region" aria-label="<?php echo esc_attr( $dv_label ); ?>" tabindex="0" data-carousel-track>
			<div class="relative w-max min-w-full">
				<span class="journey-line absolute left-7 right-0 top-7 hidden -translate-y-1/2 lg:block" aria-hidden="true"></span>
				<ol class="flex sm:gap-8">
					<?php foreach ( $dv_stops as $dv_index => $dv_stop ) : ?>
						<li id="halte-<?php echo esc_attr( $dv_index + 1 ); ?>" class="w-[100cqw] shrink-0 snap-start px-12 sm:w-[calc((100cqw-2rem)/2)] sm:px-0 lg:w-[calc((100cqw-4rem)/3)]" data-carousel-slide>
							<?php get_template_part( 'template-parts/journey/stop-card', null, [ 'stop' => $dv_stop, 'index' => $dv_index, 'total' => $dv_total ] ); ?>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		</div>
		<span class="journey-line absolute left-full top-7 hidden w-[50vw] -translate-y-1/2 lg:block" aria-hidden="true"></span>

		<?php get_template_part( 'template-parts/journey/carousel-buttons', null, [ 'prev' => __( 'Vorige halte', 'droomvrouw' ), 'next' => __( 'Volgende halte', 'droomvrouw' ) ] ); ?>
	</div>
</div>
