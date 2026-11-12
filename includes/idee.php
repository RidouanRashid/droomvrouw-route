<?php
/**
 * Ideeën-formulier: bezoekers sturen een idee + contactgegevens in
 * (shortcode [droomvrouwroute_idee_formulier]), het komt als "In afwachting"
 * binnen bij Ideeën in het admin panel, een beheerder keurt het daar goed of
 * af, en pas na goedkeuren verschijnt het idee automatisch op de site
 * (shortcode [droomvrouwroute_ideeen]). Naam en e-mailadres van de inzender
 * worden nergens in de publieke shortcodes uitgevoerd.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function droomvrouwroute_idee_assets() {
    wp_enqueue_style( 'dvr-idee', DVR_KAART_URL . 'assets/css/idee.css', array(), '1.0' );
}

/**
 * [droomvrouwroute_idee_formulier] — inzendformulier op de site.
 */
function droomvrouwroute_idee_formulier_shortcode() {
    droomvrouwroute_idee_assets();

    $melding = '';
    if ( isset( $_GET['dvr_idee'] ) ) {
        if ( 'verzonden' === $_GET['dvr_idee'] ) {
            $melding = '<p class="dvr-idee-melding dvr-idee-melding--ok">Bedankt voor je idee! We nemen het binnenkort door.</p>';
        } elseif ( 'fout' === $_GET['dvr_idee'] ) {
            $melding = '<p class="dvr-idee-melding dvr-idee-melding--fout">Vul alsjeblieft alle velden (en een geldig e-mailadres) in.</p>';
        }
    }

    ob_start();
    ?>
    <form class="dvr-idee-formulier" id="dvr-idee-formulier" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <?php echo $melding; // phpcs:ignore -- al als veilige HTML opgebouwd hierboven ?>
        <input type="hidden" name="action" value="droomvrouwroute_idee_versturen">
        <?php wp_nonce_field( 'droomvrouwroute_idee_versturen' ); ?>

        <p>
            <label for="dvr-idee-titel">Jouw idee</label>
            <input type="text" id="dvr-idee-titel" name="dvr_idee_titel" maxlength="150" required>
        </p>
        <p>
            <label for="dvr-idee-toelichting">Toelichting</label>
            <textarea id="dvr-idee-toelichting" name="dvr_idee_toelichting" rows="4" required></textarea>
        </p>
        <p>
            <label for="dvr-idee-naam">Jouw naam</label>
            <input type="text" id="dvr-idee-naam" name="dvr_idee_naam" maxlength="100" required>
        </p>
        <p>
            <label for="dvr-idee-email">E-mailadres</label>
            <input type="email" id="dvr-idee-email" name="dvr_idee_email" maxlength="150" required>
        </p>
        <p class="dvr-idee-privacy-note">Je naam en e-mailadres gebruiken we alleen om eventueel contact met je op te nemen — die worden nooit publiek getoond.</p>

        <button type="submit">Idee insturen</button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode( 'droomvrouwroute_idee_formulier', 'droomvrouwroute_idee_formulier_shortcode' );

/**
 * Verwerkt de inzending: nieuw "idee"-bericht met status "pending", zodat
 * het in het admin panel op goedkeuring wacht.
 */
function droomvrouwroute_idee_versturen_handler() {
    check_admin_referer( 'droomvrouwroute_idee_versturen' );

    $titel       = isset( $_POST['dvr_idee_titel'] ) ? sanitize_text_field( wp_unslash( $_POST['dvr_idee_titel'] ) ) : '';
    $toelichting = isset( $_POST['dvr_idee_toelichting'] ) ? sanitize_textarea_field( wp_unslash( $_POST['dvr_idee_toelichting'] ) ) : '';
    $naam        = isset( $_POST['dvr_idee_naam'] ) ? sanitize_text_field( wp_unslash( $_POST['dvr_idee_naam'] ) ) : '';
    $email       = isset( $_POST['dvr_idee_email'] ) ? sanitize_email( wp_unslash( $_POST['dvr_idee_email'] ) ) : '';

    $terug = wp_get_referer() ? wp_get_referer() : home_url( '/' );

    if ( '' === $titel || '' === $toelichting || '' === $naam || ! is_email( $email ) ) {
        wp_safe_redirect( add_query_arg( 'dvr_idee', 'fout', $terug ) . '#dvr-idee-formulier' );
        exit;
    }

    $post_id = wp_insert_post( array(
        'post_type'    => 'idee',
        'post_title'   => $titel,
        'post_content' => $toelichting,
        'post_status'  => 'pending',
    ), true );

    if ( ! is_wp_error( $post_id ) ) {
        update_post_meta( $post_id, 'inzender_naam', $naam );
        update_post_meta( $post_id, 'inzender_email', $email );
    }

    wp_safe_redirect( add_query_arg( 'dvr_idee', 'verzonden', $terug ) . '#dvr-idee-formulier' );
    exit;
}
add_action( 'admin_post_nopriv_droomvrouwroute_idee_versturen', 'droomvrouwroute_idee_versturen_handler' );
add_action( 'admin_post_droomvrouwroute_idee_versturen', 'droomvrouwroute_idee_versturen_handler' );

/**
 * [droomvrouwroute_ideeen] — goedgekeurde ideeën, zonder contactgegevens.
 */
function droomvrouwroute_ideeen_overzicht_shortcode() {
    droomvrouwroute_idee_assets();

    $ideeen = new WP_Query( array(
        'post_type'      => 'idee',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );

    if ( ! $ideeen->have_posts() ) {
        return '<p class="dvr-ideeen-leeg">Er zijn nog geen goedgekeurde ideeën.</p>';
    }

    ob_start();
    ?>
    <div class="dvr-ideeen-lijst">
        <?php while ( $ideeen->have_posts() ) : $ideeen->the_post(); ?>
            <article class="dvr-idee">
                <h3><?php the_title(); ?></h3>
                <div class="dvr-idee-tekst"><?php echo wpautop( esc_html( get_the_content() ) ); ?></div>
            </article>
        <?php endwhile; ?>
    </div>
    <?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode( 'droomvrouwroute_ideeen', 'droomvrouwroute_ideeen_overzicht_shortcode' );

/**
 * ✓ Goedkeuren / ✗ Afwijzen als rij-acties in het "Ideeën"-overzicht in
 * het admin panel, naast de standaard WordPress-acties.
 */
function droomvrouwroute_idee_rij_acties( $acties, $post ) {
    if ( 'idee' !== $post->post_type || ! current_user_can( 'edit_post', $post->ID ) ) {
        return $acties;
    }

    if ( 'publish' !== $post->post_status ) {
        $url = wp_nonce_url(
            admin_url( 'admin-post.php?action=droomvrouwroute_idee_goedkeuren&post=' . $post->ID ),
            'droomvrouwroute_idee_actie_' . $post->ID
        );
        $acties['dvr_goedkeuren'] = '<a href="' . esc_url( $url ) . '" style="color:#2a7a2a;">✓ Goedkeuren</a>';
    }

    if ( 'trash' !== $post->post_status ) {
        $url = wp_nonce_url(
            admin_url( 'admin-post.php?action=droomvrouwroute_idee_afwijzen&post=' . $post->ID ),
            'droomvrouwroute_idee_actie_' . $post->ID
        );
        $acties['dvr_afwijzen'] = '<a href="' . esc_url( $url ) . '" style="color:#a13d2b;">✗ Afwijzen</a>';
    }

    return $acties;
}
add_filter( 'post_row_actions', 'droomvrouwroute_idee_rij_acties', 10, 2 );

function droomvrouwroute_idee_goedkeuren_handler() {
    $id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
    check_admin_referer( 'droomvrouwroute_idee_actie_' . $id );

    if ( $id && current_user_can( 'edit_post', $id ) ) {
        wp_update_post( array( 'ID' => $id, 'post_status' => 'publish' ) );
    }

    wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=idee' ) );
    exit;
}
add_action( 'admin_post_droomvrouwroute_idee_goedkeuren', 'droomvrouwroute_idee_goedkeuren_handler' );

function droomvrouwroute_idee_afwijzen_handler() {
    $id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
    check_admin_referer( 'droomvrouwroute_idee_actie_' . $id );

    if ( $id && current_user_can( 'edit_post', $id ) ) {
        wp_trash_post( $id );
    }

    wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=idee' ) );
    exit;
}
add_action( 'admin_post_droomvrouwroute_idee_afwijzen', 'droomvrouwroute_idee_afwijzen_handler' );
