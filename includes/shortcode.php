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

/**
 * Subpagina's uit het menu naast "De lijnen". Titels volgen het ontwerp;
 * pagina's die (nog) niet bestaan worden overgeslagen.
 */
function droomvrouwroute_subpagina_menu() {
    $subpagina = array(
        'de-droomvrouw'         => 'De Droomvrouw',
        'zelf-de-route-volgen'  => 'Zelf de route volgen',
        'creatieve-begeleiding' => 'Creatieve begeleiding',
        'kunst'                 => 'Kunst',
        'projecten'             => 'Projecten',
        'contact'               => 'Contact',
        'nieuwtjes'             => 'Nieuwtjes',
    );

    $items = array();
    foreach ( $subpagina as $slug => $titel ) {
        if ( 'zelf-de-route-volgen' === $slug ) {
            $items[] = array( 'slug' => $slug, 'titel' => $titel, 'url' => home_url( '/' ) );
            continue;
        }
        $pagina = get_page_by_path( $slug, OBJECT, 'page' );
        if ( ! $pagina || 'publish' !== $pagina->post_status ) {
            continue;
        }
        $items[] = array( 'slug' => $slug, 'titel' => $titel, 'url' => get_permalink( $pagina ) );
    }
    return $items;
}

function droomvrouwroute_kaart_shortcode() {
    droomvrouwroute_kaart_assets();

    $basis_kaart_id  = get_option( 'options_basis_kaart' );
    $basis_kaart_url = droomvrouwroute_afbeelding_url( $basis_kaart_id );

    $kaart_breedte = 1056;
    $kaart_hoogte  = 790;
    if ( $basis_kaart_id && is_numeric( $basis_kaart_id ) ) {
        $afmetingen = wp_get_attachment_image_src( $basis_kaart_id, 'full' );
        if ( $afmetingen ) {
            $kaart_breedte = $afmetingen[1];
            $kaart_hoogte  = $afmetingen[2];
        }
    }

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

    $subpagina_per_titel = array();
    foreach ( droomvrouwroute_subpagina_menu() as $subpagina_item ) {
        $subpagina_per_titel[ mb_strtolower( $subpagina_item['titel'] ) ] = $subpagina_item['url'];
    }

    ob_start();
    ?>
    <div class="dvr-kaart-wrap">

        <img class="dvr-achtergrond-vlek" src="<?php echo esc_url( DVR_KAART_URL . 'assets/img/achtergrond-schoon.png' ); ?>" alt="" aria-hidden="true">

        <div class="dvr-titel" aria-hidden="true">
            <span>De</span>
            <span>Droom</span>
            <span>vrouw</span>
            <span>route</span>
        </div>

        <img class="dvr-ondertitel" src="<?php echo esc_url( DVR_KAART_URL . 'assets/img/ondertitel.png' ); ?>" alt="Maak nu je droom waar">

        <div class="dvr-zoek">
            <input type="search" id="dvr-zoekveld" placeholder="Zoek een plek…" autocomplete="off">
            <div class="dvr-zoek-resultaten" id="dvr-zoek-resultaten" hidden></div>
        </div>

        <div class="dvr-paneel-groep">
            <?php if ( ! empty( $lagen ) ) : ?>
            <details class="dvr-paneel dvr-paneel-lijnen" open>
                <summary>De lijnen</summary>
                <ul>
                    <?php foreach ( $lagen as $index => $laag ) : ?>
                        <li>
                            <label>
                                <input
                                    type="checkbox"
                                    class="laag-toggle"
                                    data-laag="<?php echo esc_attr( $laag->slug ); ?>"
                                    <?php checked( 0 === $index ); ?>
                                >
                                <?php echo esc_html( $laag->name ); ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </details>
            <?php endif; ?>

            <details class="dvr-paneel dvr-paneel-menu" open>
                <summary>Menu</summary>
                <ul>
                    <?php foreach ( droomvrouwroute_subpagina_menu() as $subpagina ) : ?>
                        <li><a href="<?php echo esc_url( $subpagina['url'] ); ?>"><?php echo esc_html( $subpagina['titel'] ); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </details>
        </div>

        <div
            class="dvr-kaart-venster"
            id="dvr-kaart-venster"
            style="--dvr-kaart-w: <?php echo esc_attr( $kaart_breedte ); ?>; --dvr-kaart-h: <?php echo esc_attr( $kaart_hoogte ); ?>;"
        >
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
                    $id            = get_the_ID();
                    $x             = get_post_meta( $id, 'positie_x', true );
                    $y             = get_post_meta( $id, 'positie_y', true );
                    $x             = ( '' === $x ) ? 50 : $x;
                    $y             = ( '' === $y ) ? 50 : $y;
                    $punt_lagen    = wp_get_post_terms( $id, 'laag', array( 'fields' => 'slugs' ) );
                    $eerste_laag   = ! empty( $lagen ) ? $lagen[0]->slug : '';
                    $is_actief     = in_array( $eerste_laag, $punt_lagen, true );
                    $is_basisroute = in_array( 'basisroute', $punt_lagen, true );

                    if ( ! $is_basisroute ) :
                        // Tekst van een lijn (groen/rood/blauw) staat al in de
                        // lijn-afbeelding zelf getekend — daar hoort geen los
                        // infopuntje bij. Komt de titel overeen met een
                        // subpagina (bv. "PROJECTEN"), dan wordt het een
                        // onzichtbare link daarnaartoe.
                        $subpagina_url = isset( $subpagina_per_titel[ mb_strtolower( trim( get_the_title() ) ) ] )
                            ? $subpagina_per_titel[ mb_strtolower( trim( get_the_title() ) ) ]
                            : '';
                        if ( $subpagina_url ) :
                            ?>
                            <a
                                href="<?php echo esc_url( $subpagina_url ); ?>"
                                class="infopunt-link <?php echo $is_actief ? 'is-actief' : ''; ?>"
                                data-laag="<?php echo esc_attr( implode( ' ', $punt_lagen ) ); ?>"
                                style="left: <?php echo esc_attr( $x ); ?>%; top: <?php echo esc_attr( $y ); ?>%;"
                                aria-label="<?php the_title_attribute(); ?>"
                            ></a>
                            <?php
                        endif;
                        continue;
                    endif;
                    ?>
                    <a
                        href="<?php echo esc_url( get_permalink( $id ) ); ?>"
                        class="infopunt-marker <?php echo $is_actief ? 'is-actief' : ''; ?>"
                        data-laag="<?php echo esc_attr( implode( ' ', $punt_lagen ) ); ?>"
                        style="left: <?php echo esc_attr( $x ); ?>%; top: <?php echo esc_attr( $y ); ?>%;"
                        aria-label="<?php the_title_attribute(); ?>"
                    ></a>
                <?php endwhile; wp_reset_postdata(); endif; ?>

            </div>
        </div>

        <div id="dvr-tooltip" class="dvr-tooltip" hidden></div>

    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'droomvrouwroute_kaart', 'droomvrouwroute_kaart_shortcode' );
