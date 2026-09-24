<?php
/**
 * Shortcode [droomvrouwroute_kaart]: de volledige interactieve plattegrond.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function droomvrouwroute_kaart_assets() {
    wp_enqueue_style( 'dvr-kaart', DVR_KAART_URL . 'assets/css/plattegrond.css', array(), '1.0' );
    wp_enqueue_script( 'dvr-kaart', DVR_KAART_URL . 'assets/js/plattegrond.js', array(), '1.0', true );
}

function droomvrouwroute_kaart_shortcode() {
    droomvrouwroute_kaart_assets();

    $basis_kaart_url = droomvrouwroute_afbeelding_url( get_option( 'options_basis_kaart' ) );

    $lagen = get_terms( array(
        'taxonomy'   => 'laag',
        'hide_empty' => false,
        'meta_key'   => 'volgorde',
        'orderby'    => 'meta_value_num',
        'order'      => 'ASC',
    ) );
    if ( is_wp_error( $lagen ) ) {
        $lagen = array();
    }

    $infopunten = new WP_Query( array(
        'post_type'      => 'infopunt',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ) );

    ob_start();
    ?>
    <div class="dvr-kaart-wrap">

        <img class="dvr-ondertitel" src="<?php echo esc_url( DVR_KAART_URL . 'assets/img/ondertitel.png' ); ?>" alt="Maak nu je droom waar">

        <div class="dvr-paneel-groep">
            <?php if ( ! empty( $lagen ) ) : ?>
            <details class="dvr-paneel dvr-paneel-lijnen" open>
                <summary>De lijnen</summary>
                <ul>
                    <?php foreach ( $lagen as $index => $laag ) :
                        $kleur = get_term_meta( $laag->term_id, 'kleur', true );
                        ?>
                        <li>
                            <label>
                                <input
                                    type="checkbox"
                                    class="laag-toggle"
                                    data-laag="<?php echo esc_attr( $laag->slug ); ?>"
                                    <?php checked( 0 === $index ); ?>
                                >
                                <span class="dvr-laag-swatch" style="background-color: <?php echo esc_attr( $kleur ? $kleur : '#999' ); ?>"></span>
                                <?php echo esc_html( $laag->name ); ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </details>
            <?php endif; ?>
        </div>

        <div class="dvr-zoek">
            <input type="search" id="dvr-zoekveld" placeholder="Zoek een plek…" autocomplete="off">
            <div class="dvr-zoek-resultaten" id="dvr-zoek-resultaten" hidden></div>
        </div>

        <div class="dvr-kaart-venster" id="dvr-kaart-venster">
            <div class="dvr-kaart-schaal" id="dvr-kaart-schaal">

                <?php if ( $basis_kaart_url ) : ?>
                    <img class="basis-kaart" src="<?php echo esc_url( $basis_kaart_url ); ?>" alt="Plattegrond van De Droomvrouwroute">
                <?php endif; ?>

                <?php foreach ( $lagen as $index => $laag ) :
                    $overlay_url = droomvrouwroute_afbeelding_url( get_term_meta( $laag->term_id, 'lijn_overlay', true ) );
                    if ( ! $overlay_url ) {
                        continue;
                    }
                    ?>
                    <img
                        class="laag-overlay <?php echo 0 === $index ? 'is-actief' : ''; ?>"
                        data-laag="<?php echo esc_attr( $laag->slug ); ?>"
                        src="<?php echo esc_url( $overlay_url ); ?>"
                        alt="Route-lijn: <?php echo esc_attr( $laag->name ); ?>"
                    >
                <?php endforeach; ?>

                <?php if ( $infopunten->have_posts() ) : while ( $infopunten->have_posts() ) : $infopunten->the_post();
                    $id          = get_the_ID();
                    $x           = get_post_meta( $id, 'positie_x', true );
                    $y           = get_post_meta( $id, 'positie_y', true );
                    $x           = ( '' === $x ) ? 50 : $x;
                    $y           = ( '' === $y ) ? 50 : $y;
                    $punt_lagen  = wp_get_post_terms( $id, 'laag', array( 'fields' => 'slugs' ) );
                    $eerste_laag = ! empty( $lagen ) ? $lagen[0]->slug : '';
                    $is_actief   = in_array( $eerste_laag, $punt_lagen, true );
                    ?>
                    <button
                        type="button"
                        class="infopunt-marker <?php echo $is_actief ? 'is-actief' : ''; ?>"
                        data-laag="<?php echo esc_attr( implode( ' ', $punt_lagen ) ); ?>"
                        data-target="infopunt-<?php echo esc_attr( $id ); ?>"
                        style="left: <?php echo esc_attr( $x ); ?>%; top: <?php echo esc_attr( $y ); ?>%;"
                        aria-label="<?php the_title_attribute(); ?>"
                    ></button>
                <?php endwhile; wp_reset_postdata(); endif; ?>

            </div>
        </div>

        <div id="dvr-tooltip" class="dvr-tooltip" hidden></div>

        <?php
        if ( $infopunten->have_posts() ) : while ( $infopunten->have_posts() ) : $infopunten->the_post();
            $id           = get_the_ID();
            $foto_url     = droomvrouwroute_afbeelding_url( get_post_meta( $id, 'foto', true ) );
            $subthema     = get_post_meta( $id, 'subthema', true );
            $beschrijving = get_post_meta( $id, 'beschrijving', true );
            $social_link  = get_post_meta( $id, 'social_link', true );
            ?>
            <div class="infopunt-data" id="infopunt-<?php echo esc_attr( $id ); ?>" hidden>
                <h3><?php the_title(); ?></h3>
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
                    <a class="social-knop" href="<?php echo esc_url( $social_link ); ?>" target="_blank" rel="noopener">Bekijk op Instagram/Facebook</a>
                <?php endif; ?>
            </div>
        <?php endwhile; wp_reset_postdata(); endif; ?>

        <div class="infopunt-modal" hidden>
            <div class="infopunt-modal-overlay" data-modal-sluiten></div>
            <div class="infopunt-modal-inhoud" role="dialog" aria-modal="true">
                <button type="button" class="infopunt-modal-sluiten" data-modal-sluiten aria-label="Sluiten">&times;</button>
                <div class="infopunt-modal-tekst"></div>
            </div>
        </div>

    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'droomvrouwroute_kaart', 'droomvrouwroute_kaart_shortcode' );
