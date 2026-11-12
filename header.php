<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'bg-paper font-serif text-body antialiased' ); ?>>
<?php wp_body_open(); ?>
<a href="#main" class="skip-link sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:bg-ink focus:px-4 focus:py-3 focus:font-sans focus:text-paper"><?php esc_html_e( 'Naar de inhoud', 'droomvrouw' ); ?></a>

<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/wash.webp' ); ?>" alt="" width="820" height="594" decoding="async" class="pointer-events-none absolute left-0 top-0 -z-10 w-[calc(var(--logo-size)*10.5)] select-none">

<header class="site-header relative z-30">
	<div class="mx-auto flex min-h-[calc(var(--logo-size)*6.2)] max-w-7xl flex-wrap items-start justify-end gap-8 px-5 pt-8 sm:px-8 lg:flex-nowrap lg:px-16 lg:pt-16">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="absolute left-[0.75em] top-[0.8em] z-50 font-sans text-[length:var(--logo-size)] font-bold leading-[1.15] tracking-[0.04em] text-logo" rel="home">
			<span aria-hidden="true">De<br>Droom<br>vrouw<br>route</span>
			<span class="sr-only"><?php echo esc_html( get_bloginfo( 'name' ) ?: __( 'De Droomvrouwroute', 'droomvrouw' ) ); ?></span>
		</a>

		<button type="button" class="group relative z-50 hidden size-12 place-items-center text-ink js:max-lg:grid" aria-expanded="false" aria-controls="site-nav" data-menu-toggle data-label-open="<?php esc_attr_e( 'Menu openen', 'droomvrouw' ); ?>" data-label-close="<?php esc_attr_e( 'Menu sluiten', 'droomvrouw' ); ?>">
			<svg viewBox="0 0 32 32" class="size-9 group-aria-expanded:hidden" aria-hidden="true" focusable="false"><path d="M4 8h24M4 16h24M4 24h24" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
			<svg viewBox="0 0 32 32" class="hidden size-8 group-aria-expanded:block" aria-hidden="true" focusable="false"><path d="M7 7l18 18M25 7L7 25" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
			<span class="sr-only" data-menu-label><?php esc_html_e( 'Menu openen', 'droomvrouw' ); ?></span>
		</button>

		<nav id="site-nav" class="nav-panel" aria-label="<?php esc_attr_e( 'Hoofdmenu', 'droomvrouw' ); ?>">
			<div class="flex flex-col gap-10 lg:flex-row lg:gap-20 lg:pt-2">
				<?php get_template_part( 'template-parts/nav-lines' ); ?>

				<div>
					<p id="nav-menu-title" class="nav-heading text-muted"><?php esc_html_e( 'Menu', 'droomvrouw' ); ?></p>
					<?php
					wp_nav_menu( [
						'theme_location'  => 'primary',
						'container'       => false,
						'menu_class'      => 'nav-list',
						'depth'           => 1,
						'fallback_cb'     => false,
						'items_wrap'      => '<ul id="%1$s" class="%2$s" aria-labelledby="nav-menu-title">%3$s</ul>',
					] );
					?>
				</div>
			</div>
		</nav>
	</div>
</header>
