<?php
$dv_stops = $args['stops'];
$dv_total = count( $dv_stops );
?>
<div class="relative mt-12 lg:mt-16">
	<span class="journey-line-v absolute bottom-0 left-6 top-0 -translate-x-1/2 lg:left-1/2" aria-hidden="true"></span>

	<ol class="relative">
		<?php foreach ( $dv_stops as $dv_index => $dv_stop ) : ?>
			<?php
			$dv_number  = $dv_index + 1;
			$dv_flip    = 1 === $dv_index % 2;
			$dv_image   = dv_card_image_id( $dv_stop->ID );
			$dv_chapter = dv_book_chapter( $dv_stop->ID );
			?>
			<li id="halte-<?php echo esc_attr( $dv_number ); ?>" class="relative pb-16 pl-16 lg:grid lg:grid-cols-[1fr_8rem_1fr] lg:items-center lg:pb-24 lg:pl-0">
				<div class="absolute left-0 top-0 z-10 lg:static lg:col-start-2 lg:row-start-1 lg:justify-self-center">
					<?php echo dv_marker( $dv_number, false, 'size-12 lg:size-14' ); ?>
				</div>

				<?php if ( $dv_image ) : ?>
					<figure class="photo-frame <?php echo $dv_flip ? 'rotate-[1.5deg] lg:col-start-3' : '-rotate-[1.5deg] lg:col-start-1'; ?> lg:row-start-1">
						<?php echo wp_get_attachment_image( $dv_image, 'dv-slide', false, [ 'class' => 'aspect-[4/3] w-full object-cover', 'loading' => 'lazy', 'sizes' => '(min-width: 1024px) 40vw, 85vw' ] ); ?>
					</figure>
				<?php endif; ?>

				<article class="group relative mt-6 lg:row-start-1 lg:mt-0 <?php echo $dv_flip ? 'lg:col-start-1 lg:text-right' : 'lg:col-start-3'; ?>">
					<p class="font-sans text-xs tracking-[0.18em] text-muted"><?php printf( esc_html__( 'halte %d', 'droomvrouw' ), $dv_number ); ?></p>
					<h2 class="mt-2 font-sans text-lg font-bold leading-snug tracking-[0.1em] text-ink">
						<a href="<?php echo esc_url( get_permalink( $dv_stop ) ); ?>" class="stretched-link">
							<span class="sr-only"><?php printf( esc_html__( 'Halte %1$d van %2$d:', 'droomvrouw' ), $dv_number, $dv_total ); ?></span>
							<?php echo esc_html( get_the_title( $dv_stop ) ); ?>
						</a>
					</h2>
					<p class="mt-3 leading-relaxed"><?php echo esc_html( dv_short_text( $dv_stop ) ); ?></p>
					<?php if ( $dv_chapter ) : ?>
						<p class="mt-2 font-sans text-sm tracking-[0.1em] text-muted"><?php echo esc_html( $dv_chapter ); ?></p>
					<?php endif; ?>
					<span class="read-more mt-3 inline-block" aria-hidden="true"><?php esc_html_e( 'lees verder', 'droomvrouw' ); ?></span>
				</article>
			</li>
		<?php endforeach; ?>
	</ol>
</div>

<?php get_template_part( 'template-parts/journey/route-end' ); ?>
