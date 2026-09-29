<?php
/**
 * Plugin Name: Droomvrouwroute - Interactieve Kaart
 * Description: Registreert de informatiepunten, lagen en de interactieve plattegrond (shortcode [droomvrouwroute_kaart]) voor De Droomvrouwroute.
 * Version: 1.0
 * Author: Ridouan Rashid
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'DVR_KAART_URL', plugin_dir_url( __FILE__ ) );
define( 'DVR_KAART_PATH', plugin_dir_path( __FILE__ ) );

require_once DVR_KAART_PATH . 'includes/post-types.php';
require_once DVR_KAART_PATH . 'includes/velden.php';
require_once DVR_KAART_PATH . 'includes/shortcode.php';
require_once DVR_KAART_PATH . 'includes/positie-kiezer.php';

/**
 * Haalt een afbeeldingsveld (attachment-ID of losse URL) op als bruikbare URL.
 */
function droomvrouwroute_afbeelding_url( $waarde ) {
    if ( empty( $waarde ) ) {
        return '';
    }
    if ( is_numeric( $waarde ) ) {
        $url = wp_get_attachment_url( $waarde );
        return $url ? esc_url( $url ) : '';
    }
    return esc_url( $waarde );
}
