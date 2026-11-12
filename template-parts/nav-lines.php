<?php
$dv_lines = dv_lines();
if ( ! $dv_lines ) {
	return;
}

$dv_current = 0;
if ( is_tax( 'dv_line' ) ) {
	$dv_current = get_queried_object_id();
} elseif ( is_singular( 'dv_location' ) && ( $dv_line = dv_location_line( get_the_ID() ) ) ) {
	$dv_current = $dv_line->term_id;
}
?>
<div>
	<p id="nav-lines-title" class="nav-heading text-ink"><?php esc_html_e( 'De lijnen', 'droomvrouw' ); ?></p>
	<ul class="nav-list nav-list--lines" aria-labelledby="nav-lines-title">
		<?php foreach ( $dv_lines as $dv_term ) : ?>
			<li>
				<a href="<?php echo esc_url( get_term_link( $dv_term ) ); ?>"<?php echo $dv_term->term_id === $dv_current ? ' aria-current="page"' : ''; ?>>
					<span><?php echo esc_html( $dv_term->name ); ?></span>
					<span class="nav-dot" style="--dot: <?php echo esc_attr( dv_line_color( $dv_term ) ); ?>" aria-hidden="true"></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
