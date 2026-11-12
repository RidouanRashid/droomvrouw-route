<?php
$dv_stop    = $args['stop'];
$dv_number  = $args['index'] + 1;
$dv_total   = $args['total'];
$dv_image   = dv_card_image_id( $dv_stop->ID );
$dv_chapter = dv_book_chapter( $dv_stop->ID );
?>
<article class="stop-card group relative">
	<div class="relative z-10 mb-5 hidden lg:block">
		<?php echo dv_marker( $dv_number, false, 'size-14' ); ?>
	</div>

	<figure class="relative aspect-square overflow-hidden bg-rule/50">
		<?php
		if ( $dv_image ) {
			echo wp_get_attachment_image( $dv_image, 'dv-card', false, [
				'class'   => 'size-full object-cover transition-transform duration-500 motion-safe:group-hover:scale-[1.03]',
				'loading' => 'lazy',
				'sizes'   => '(min-width: 1024px) 28vw, (min-width: 640px) 45vw, 75vw',
			] );
		}
		?>
		<span class="counter lg:hidden" aria-hidden="true"><?php echo esc_html( "$dv_number / $dv_total" ); ?></span>
	</figure>

	<h2 class="mt-6 font-sans text-lg font-bold leading-snug tracking-[0.1em] text-ink">
		<a href="<?php echo esc_url( get_permalink( $dv_stop ) ); ?>" class="stretched-link">
			<span class="sr-only"><?php printf( esc_html__( 'Halte %1$d van %2$d:', 'droomvrouw' ), $dv_number, $dv_total ); ?></span>
			<?php echo esc_html( get_the_title( $dv_stop ) ); ?>
		</a>
	</h2>
	<p class="mt-3 text-lg leading-relaxed"><?php echo esc_html( dv_short_text( $dv_stop ) ); ?></p>
	<?php if ( $dv_chapter ) : ?>
		<p class="mt-3 font-sans text-sm tracking-[0.1em] text-muted"><?php echo esc_html( $dv_chapter ); ?></p>
	<?php endif; ?>
	<span class="read-more mt-3 inline-block" aria-hidden="true"><?php esc_html_e( 'lees verder', 'droomvrouw' ); ?></span>
</article>
