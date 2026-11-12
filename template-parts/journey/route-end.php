<?php
$dv_heading = dv_mod( 'cta_heading' );
$dv_text    = dv_mod( 'cta_text' );
$dv_url     = dv_mod( 'cta_url' );
?>
<div class="route-end flex flex-col items-start lg:items-center">
	<span class="route-end__dot -ml-1 lg:ml-0" aria-hidden="true"></span>

	<?php if ( $dv_heading ) : ?>
		<p class="mt-10 -rotate-3 text-3xl italic text-[var(--line)] lg:text-5xl"><?php echo esc_html( $dv_heading ); ?></p>
	<?php endif; ?>

	<?php if ( $dv_text && $dv_url ) : ?>
		<a href="<?php echo esc_url( $dv_url ); ?>" class="btn mt-8"><?php echo esc_html( $dv_text ); ?></a>
	<?php endif; ?>
</div>
