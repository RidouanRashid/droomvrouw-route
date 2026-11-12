<?php
$dv_term     = get_queried_object();
$dv_stops    = $GLOBALS['wp_query']->posts;
$dv_vertical = 'vertical' === dv_mod( 'line_layout' );

get_header();
?>

<main id="main" <?php echo dv_line_attributes( $dv_term, 'relative isolate overflow-x-clip pt-10 lg:pt-12' ); ?>>
	<?php get_template_part( 'template-parts/route-wash' ); ?>

	<div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-16">
		<div class="<?php echo $dv_vertical ? 'lg:ml-[24%] lg:max-w-2xl' : 'lg:ml-[24%]'; ?>">
			<?php get_template_part( 'template-parts/journey/line-head', null, [ 'term' => $dv_term, 'stops' => $dv_stops, 'heading' => 'h1', 'intro' => $dv_vertical ] ); ?>

			<?php if ( ! $dv_stops ) : ?>
				<p class="mt-8"><?php esc_html_e( 'Op deze lijn staan nog geen haltes.', 'droomvrouw' ); ?></p>
			<?php elseif ( ! $dv_vertical ) : ?>
				<?php get_template_part( 'template-parts/journey/track', null, [ 'term' => $dv_term, 'stops' => $dv_stops ] ); ?>
			<?php endif; ?>
		</div>

		<?php
		if ( $dv_stops && $dv_vertical ) {
			get_template_part( 'template-parts/journey/vertical', null, [ 'term' => $dv_term, 'stops' => $dv_stops ] );
		}
		?>
	</div>
</main>

<?php get_footer(); ?>
