<?php
$dv_term     = $args['term'];
$dv_count    = count( $args['stops'] ?? [] );
$dv_is_title = 'h1' === ( $args['heading'] ?? 'h1' );
$dv_subtitle = get_term_meta( $dv_term->term_id, 'dv_subtitle', true );
$dv_duration = get_term_meta( $dv_term->term_id, 'dv_duration', true );
$dv_intro    = ! empty( $args['intro'] ) ? get_term_meta( $dv_term->term_id, 'dv_intro', true ) : '';
$dv_facts    = array_filter( [ sprintf( _n( '%d halte', '%d haltes', $dv_count, 'droomvrouw' ), $dv_count ), $dv_duration ] );
$dv_tag      = $dv_is_title ? 'h1' : 'p';
?>
<div class="line-head">
	<div class="flex flex-wrap items-baseline justify-between gap-x-6 gap-y-1">
		<<?php echo $dv_tag; ?> class="font-sans text-lg font-bold tracking-[0.2em] text-ink lg:text-xl">
			<?php if ( $dv_is_title ) : ?>
				<?php echo esc_html( dv_line_title( $dv_term ) ); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( get_term_link( $dv_term ) ); ?>" class="underline-offset-[6px] hover:underline"><?php echo esc_html( dv_line_title( $dv_term ) ); ?></a>
			<?php endif; ?>
		</<?php echo $dv_tag; ?>>
		<p class="italic text-muted"><?php echo esc_html( $dv_subtitle ?: $dv_facts[0] ); ?></p>
	</div>

	<?php if ( $dv_intro ) : ?>
		<p class="mt-3 max-w-xl leading-relaxed"><?php echo esc_html( $dv_intro ); ?></p>
		<p class="mt-2 font-sans text-xs tracking-[0.18em] text-muted"><?php echo esc_html( implode( ' · ', $dv_facts ) ); ?></p>
	<?php endif; ?>
</div>
