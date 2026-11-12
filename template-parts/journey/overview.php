<?php
$dv_stops   = $args['stops'];
$dv_current = (int) ( $args['current'] ?? 0 );
$dv_anchors = 'anchor' === ( $args['mode'] ?? 'page' );
$dv_total   = count( $dv_stops );
?>
<nav class="relative max-sm:px-12 <?php echo esc_attr( $args['class'] ?? '' ); ?>" aria-label="<?php echo esc_attr( $args['label'] ); ?>">
	<span class="journey-line absolute left-6 top-1/2 -translate-y-1/2 max-sm:left-18 <?php echo $dv_total > 1 ? 'right-6 max-sm:right-18' : 'right-0'; ?> <?php echo ! empty( $args['tail'] ) ? 'lg:right-[-50vw]' : ''; ?>" aria-hidden="true"></span>
	<ol class="relative flex justify-between gap-1">
		<?php foreach ( $dv_stops as $dv_index => $dv_stop ) : ?>
			<?php
			$dv_number = $dv_index + 1;
			$dv_active = $dv_anchors ? 0 === $dv_index : $dv_stop->ID === $dv_current;
			$dv_attr   = $dv_anchors
				? sprintf( 'href="#halte-%d" data-carousel-goto="%d"', $dv_number, $dv_index ) . ( $dv_active ? ' aria-current="step"' : '' )
				: sprintf( 'href="%s"', esc_url( get_permalink( $dv_stop ) ) ) . ( $dv_active ? ' aria-current="page"' : '' );
			?>
			<li>
				<a <?php echo $dv_attr; ?> class="block rounded-full">
					<?php echo dv_marker( $dv_number, $dv_active, 'size-11 lg:size-14' ); ?>
					<span class="sr-only"><?php printf( esc_html__( 'Halte %1$d van %2$d: %3$s', 'droomvrouw' ), $dv_number, $dv_total, esc_html( get_the_title( $dv_stop ) ) ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ol>
</nav>
