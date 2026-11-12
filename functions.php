<?php

foreach ( [ 'setup', 'post-types', 'meta', 'meta-boxes', 'term-fields', 'customizer', 'template-tags', 'seed' ] as $droomvrouw_file ) {
	require get_template_directory() . "/inc/{$droomvrouw_file}.php";
}
