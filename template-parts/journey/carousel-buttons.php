<div class="carousel-buttons sm:mt-6 sm:flex sm:justify-end sm:gap-3">
	<button type="button" class="carousel-button left-0" data-carousel-prev hidden>
		<svg viewBox="0 0 24 24" class="size-7" aria-hidden="true" focusable="false"><path d="M16 4 6 12l10 8z" fill="currentColor"/></svg>
		<span class="sr-only"><?php echo esc_html( $args['prev'] ); ?></span>
	</button>
	<button type="button" class="carousel-button right-0" data-carousel-next hidden>
		<svg viewBox="0 0 24 24" class="size-7" aria-hidden="true" focusable="false"><path d="m8 4 10 8-10 8z" fill="currentColor"/></svg>
		<span class="sr-only"><?php echo esc_html( $args['next'] ); ?></span>
	</button>
</div>
