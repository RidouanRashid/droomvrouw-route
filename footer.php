<?php
$dv_old_site = dv_mod( 'old_site_url' );
?>
<footer class="site-footer mx-auto mt-24 max-w-7xl px-5 pb-10 sm:px-8 lg:mt-32 lg:px-16">
	<?php get_template_part( 'template-parts/contact' ); ?>

	<?php if ( $dv_old_site ) : ?>
		<p class="mt-10 font-sans text-xs leading-relaxed tracking-[0.18em] text-ink">
			<?php echo esc_html( dv_mod( 'old_site_label' ) ); ?><br>
			<a href="<?php echo esc_url( $dv_old_site ); ?>" class="underline-offset-4 hover:underline"><?php echo esc_html( dv_old_site_label( $dv_old_site ) ); ?></a>
		</p>
	<?php endif; ?>
</footer>

<?php wp_footer(); ?>
</body>
</html>
