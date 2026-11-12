<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="site-header">
    <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">De Droomvrouwroute</a></p>

    <?php
    wp_nav_menu( array(
        'theme_location' => 'hoofdmenu',
        'container'      => false,
        'menu_class'     => 'hoofdmenu',
        'fallback_cb'    => false,
    ) );

    $oude_site_url = get_option( 'options_oude_website_url' );
    $instagram_url = get_option( 'options_instagram_url' );
    $facebook_url  = get_option( 'options_facebook_url' );
    ?>

    <?php if ( $oude_site_url || $instagram_url || $facebook_url ) : ?>
    <div class="header-extra">
        <?php if ( $oude_site_url ) : ?>
            <a href="<?php echo esc_url( $oude_site_url ); ?>" target="_blank" rel="noopener">Oude website</a>
        <?php endif; ?>
        <?php if ( $instagram_url ) : ?>
            <a href="<?php echo esc_url( $instagram_url ); ?>" target="_blank" rel="noopener">Instagram</a>
        <?php endif; ?>
        <?php if ( $facebook_url ) : ?>
            <a href="<?php echo esc_url( $facebook_url ); ?>" target="_blank" rel="noopener">Facebook</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</header>
