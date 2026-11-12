<?php
$dv_ids   = $args['ids'];
$dv_total = count( $dv_ids );
if ( ! $dv_total ) {
	return;
}

$dv_widths = [
	1 => 'w-[100cqw] px-12 sm:px-0',
	2 => 'w-[100cqw] px-12 sm:w-[calc((100cqw-2rem)/2)] sm:px-0',
	3 => 'w-[100cqw] px-12 sm:w-[calc((100cqw-2rem)/2)] sm:px-0 lg:w-[calc((100cqw-4rem)/3)]',
];
$dv_width = $dv_widths[ min( 3, $dv_total ) ];
$dv_label = sprintf( _n( 'Galerij, %d afbeelding', 'Galerij, %d afbeeldingen', $dv_total, 'droomvrouw' ), $dv_total );
?>
<section class="mt-8 lg:mt-10" aria-label="<?php echo esc_attr( $dv_label ); ?>" data-carousel>
	<div class="relative @container [--arrow-top:calc((100cqw-6rem)*0.375)]">
		<div class="carousel-track" <?php echo $dv_total > 1 ? 'role="region" tabindex="0" aria-label="' . esc_attr( $dv_label ) . '"' : ''; ?> data-carousel-track>
			<ul class="flex w-max min-w-full sm:gap-8">
				<?php foreach ( $dv_ids as $dv_index => $dv_id ) : ?>
					<li class="<?php echo esc_attr( $dv_width ); ?> shrink-0 snap-start" data-carousel-slide>
						<figure>
							<div class="relative">
								<?php
								echo wp_get_attachment_image( $dv_id, 'dv-slide', false, [
									'class'         => 'aspect-[4/3] w-full object-cover',
									'loading'       => 0 === $dv_index ? 'eager' : 'lazy',
									'fetchpriority' => 0 === $dv_index ? 'high' : 'auto',
									'sizes'         => 1 === $dv_total ? '(min-width: 1024px) 70vw, 85vw' : '(min-width: 1024px) 28vw, (min-width: 640px) 45vw, 75vw',
								] );
								?>
								<?php if ( $dv_total > 1 ) : ?>
									<span class="counter" aria-hidden="true"><?php echo esc_html( ( $dv_index + 1 ) . " / $dv_total" ); ?></span>
								<?php endif; ?>
							</div>
							<?php if ( $dv_caption = wp_get_attachment_caption( $dv_id ) ) : ?>
								<figcaption class="mt-2 font-sans text-xs tracking-[0.08em] text-muted"><?php echo esc_html( $dv_caption ); ?></figcaption>
							<?php endif; ?>
						</figure>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<?php
		if ( $dv_total > 1 ) {
			get_template_part( 'template-parts/journey/carousel-buttons', null, [ 'prev' => __( 'Vorige afbeelding', 'droomvrouw' ), 'next' => __( 'Volgende afbeelding', 'droomvrouw' ) ] );
		}
		?>
	</div>
</section>
