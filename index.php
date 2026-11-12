<?php get_header(); ?>

<main id="main" class="mx-auto max-w-7xl px-5 pt-12 sm:px-8 lg:px-16">
	<div class="lg:ml-[24%] lg:max-w-2xl">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class( 'mb-16' ); ?>>
					<?php if ( is_singular() ) : ?>
						<h1 class="font-sans text-2xl font-bold tracking-[0.12em] text-ink"><?php the_title(); ?></h1>
					<?php else : ?>
						<h2 class="font-sans text-xl font-bold tracking-[0.12em] text-ink"><a href="<?php the_permalink(); ?>" class="hover:underline"><?php the_title(); ?></a></h2>
					<?php endif; ?>
					<div class="entry-content mt-6"><?php is_singular() ? the_content() : the_excerpt(); ?></div>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Er is hier nog niets te zien.', 'droomvrouw' ); ?></p>
		<?php endif; ?>
	</div>
</main>

<?php get_footer(); ?>
