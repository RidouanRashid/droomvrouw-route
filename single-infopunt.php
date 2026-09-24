<?php
/**
 * Detailweergave van een los informatiepunt (fallback voor directe links;
 * de normale interactie verloopt via de modal op de plattegrondpagina).
 */
get_header();
?>

<main class="pagina-inhoud">
    <?php while ( have_posts() ) : the_post();
        $foto_url   = droomvrouwroute_afbeelding_url( get_post_meta( get_the_ID(), 'foto', true ) );
        $subthema   = get_post_meta( get_the_ID(), 'subthema', true );
        $beschrijving = get_post_meta( get_the_ID(), 'beschrijving', true );
        $social_link  = get_post_meta( get_the_ID(), 'social_link', true );
        ?>
        <article <?php post_class(); ?>>
            <h1><?php the_title(); ?></h1>

            <?php if ( $subthema ) : ?>
                <p class="subthema"><?php echo esc_html( $subthema ); ?></p>
            <?php endif; ?>

            <?php if ( $foto_url ) : ?>
                <img src="<?php echo esc_url( $foto_url ); ?>" alt="<?php the_title_attribute(); ?>">
            <?php endif; ?>

            <?php if ( $beschrijving ) : ?>
                <div class="beschrijving"><?php echo wp_kses_post( $beschrijving ); ?></div>
            <?php endif; ?>

            <?php if ( $social_link ) : ?>
                <p><a class="social-knop" href="<?php echo esc_url( $social_link ); ?>" target="_blank" rel="noopener">Bekijk op Instagram/Facebook</a></p>
            <?php endif; ?>
        </article>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
