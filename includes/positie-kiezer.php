<?php
/**
 * Visuele positiekiezer: klik op de kaart in het admin-scherm van een
 * infopunt om positie_x / positie_y in te stellen, in plaats van blind
 * percentages te gokken. Toont de route-lijn(en) van de gekoppelde laag/lagen
 * als referentie, zodat het punt precies op de route geplaatst kan worden.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function droomvrouwroute_positie_kiezer_metabox() {
    add_meta_box(
        'dvr_positie_kiezer',
        __( 'Positie op de kaart', 'droomvrouwroute' ),
        'droomvrouwroute_positie_kiezer_render',
        'infopunt',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'droomvrouwroute_positie_kiezer_metabox' );

function droomvrouwroute_positie_kiezer_render( $post ) {
    $basis_kaart_id = get_option( 'options_basis_kaart' );
    $kaart_url      = droomvrouwroute_afbeelding_url( $basis_kaart_id );

    if ( ! $kaart_url ) {
        echo '<p>' . esc_html__( 'Stel eerst een basiskaart in bij Plattegrond → instellingen.', 'droomvrouwroute' ) . '</p>';
        return;
    }

    // Zelfde beeldverhouding als de echte kaart aanhouden (net als op de
    // live plattegrondpagina), anders klopt een percentage dat je hier
    // aanklikt/sleept niet met waar het punt op de echte kaart terechtkomt.
    $kaart_breedte = 1056;
    $kaart_hoogte  = 790;
    if ( $basis_kaart_id && is_numeric( $basis_kaart_id ) ) {
        $afmetingen = wp_get_attachment_image_src( $basis_kaart_id, 'full' );
        if ( $afmetingen ) {
            $kaart_breedte = $afmetingen[1];
            $kaart_hoogte  = $afmetingen[2];
        }
    }

    $x = get_post_meta( $post->ID, 'positie_x', true );
    $y = get_post_meta( $post->ID, 'positie_y', true );
    $x = ( '' === $x ) ? 50 : $x;
    $y = ( '' === $y ) ? 50 : $y;

    $overlays = array();
    $termen   = wp_get_post_terms( $post->ID, 'laag' );
    if ( ! is_wp_error( $termen ) ) {
        foreach ( $termen as $term ) {
            $overlay_url = droomvrouwroute_afbeelding_url( get_term_meta( $term->term_id, 'lijn_overlay', true ) );
            if ( $overlay_url ) {
                $overlays[] = $overlay_url;
            }
        }
    }
    ?>
    <p class="description">
        <?php esc_html_e( 'Klik op de kaart om dit informatiepunt te plaatsen. De route-lijn van de gekoppelde laag wordt hieronder getoond zodat je precies op de route kunt klikken. Sla "Laag" (hiernaast/onder) eerst op en herlaad deze pagina als de lijn nog niet zichtbaar is.', 'droomvrouwroute' ); ?>
    </p>
    <div
        id="dvr-positie-kiezer"
        style="position: relative; width: 100%; max-width: 40rem; aspect-ratio: <?php echo esc_attr( $kaart_breedte ); ?> / <?php echo esc_attr( $kaart_hoogte ); ?>; background: #f4ecc9; overflow: hidden; cursor: crosshair; border: 1px solid #ddd;"
    >
        <img src="<?php echo esc_url( $kaart_url ); ?>" alt="" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain; pointer-events: none;">
        <?php foreach ( $overlays as $overlay_url ) : ?>
            <img src="<?php echo esc_url( $overlay_url ); ?>" alt="" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain; pointer-events: none;">
        <?php endforeach; ?>
        <div
            id="dvr-positie-marker"
            style="position: absolute; left: <?php echo esc_attr( $x ); ?>%; top: <?php echo esc_attr( $y ); ?>%; width: 22px; height: 22px; margin: -11px 0 0 -11px; border-radius: 50%; background: #c0392b; border: 2px solid #fff; box-shadow: 0 0 0 1px rgba(0,0,0,0.4); cursor: grab; touch-action: none;"
        ></div>
    </div>
    <p class="description">
        <?php esc_html_e( 'Sleep het rode punt naar de juiste plek, of klik ergens anders op de kaart om het punt daarheen te springen.', 'droomvrouwroute' ); ?>
    </p>
    <p>
        <?php esc_html_e( 'Positie:', 'droomvrouwroute' ); ?>
        X <span id="dvr-positie-x-weergave"><?php echo esc_html( $x ); ?></span>%
        &middot;
        Y <span id="dvr-positie-y-weergave"><?php echo esc_html( $y ); ?></span>%
    </p>
    <script>
    ( function () {
        var vlak = document.getElementById( 'dvr-positie-kiezer' );
        var marker = document.getElementById( 'dvr-positie-marker' );
        var weergaveX = document.getElementById( 'dvr-positie-x-weergave' );
        var weergaveY = document.getElementById( 'dvr-positie-y-weergave' );

        if ( ! vlak || ! marker ) {
            return;
        }

        function zetPositie( x, y ) {
            x = Math.min( 100, Math.max( 0, x ) );
            y = Math.min( 100, Math.max( 0, y ) );
            x = Math.round( x * 10 ) / 10;
            y = Math.round( y * 10 ) / 10;

            marker.style.left = x + '%';
            marker.style.top = y + '%';
            weergaveX.textContent = x;
            weergaveY.textContent = y;

            var veldX = document.querySelector( '.acf-field[data-key="field_infopunt_positie_x"] input' );
            var veldY = document.querySelector( '.acf-field[data-key="field_infopunt_positie_y"] input' );
            if ( veldX ) { veldX.value = x; veldX.dispatchEvent( new Event( 'change' ) ); }
            if ( veldY ) { veldY.value = y; veldY.dispatchEvent( new Event( 'change' ) ); }
        }

        function positieVanEvent( event ) {
            var rect = vlak.getBoundingClientRect();
            return {
                x: ( event.clientX - rect.left ) / rect.width * 100,
                y: ( event.clientY - rect.top ) / rect.height * 100
            };
        }

        /* Klik ergens op de kaart: punt springt daarheen. */
        vlak.addEventListener( 'click', function ( event ) {
            if ( event.target === marker ) {
                return;
            }
            var p = positieVanEvent( event );
            zetPositie( p.x, p.y );
        } );

        /* Sleep het bestaande punt: preciezer bijstellen. */
        var slepen = false;
        marker.addEventListener( 'pointerdown', function ( event ) {
            slepen = true;
            marker.style.cursor = 'grabbing';
            marker.setPointerCapture( event.pointerId );
            event.stopPropagation();
        } );
        marker.addEventListener( 'pointermove', function ( event ) {
            if ( ! slepen ) {
                return;
            }
            var p = positieVanEvent( event );
            zetPositie( p.x, p.y );
        } );
        function slependStoppen( event ) {
            if ( ! slepen ) {
                return;
            }
            slepen = false;
            marker.style.cursor = 'grab';
            try { marker.releasePointerCapture( event.pointerId ); } catch ( e ) {}
        }
        marker.addEventListener( 'pointerup', slependStoppen );
        marker.addEventListener( 'pointercancel', slependStoppen );
    } )();
    </script>
    <?php
}
