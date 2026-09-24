<?php
/**
 * Template Name: Plattegrond
 *
 * Volledig zelfstandige pagina: geen site-header/footer/menu, alleen de
 * kaart zelf, geleverd door de plugin "droomvrouwroute-kaart" via de
 * shortcode [droomvrouwroute_kaart]. Dit is ook de vaste voorpagina.
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php echo do_shortcode( '[droomvrouwroute_kaart]' ); ?>
<?php wp_footer(); ?>
</body>
</html>
