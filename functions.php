<?php
/**
 * Poorthuis — lichtgewicht blokthema.
 *
 * Alles wat het thema doet staat in inc/: opschonen van WordPress-overhead,
 * de berichttypes (projecten, vacatures), de eigen blokken en het formulier.
 */

defined( 'ABSPATH' ) || exit;

define( 'POORTHUIS_VERSION', wp_get_theme()->get( 'Version' ) );

require __DIR__ . '/inc/lang.php';
require __DIR__ . '/inc/setup.php';
require __DIR__ . '/inc/post-types.php';
require __DIR__ . '/inc/blocks.php';
require __DIR__ . '/inc/contact.php';
require __DIR__ . '/inc/cookies.php';
require __DIR__ . '/inc/inrichten.php';
require __DIR__ . '/inc/redirects.php';
require __DIR__ . '/inc/updates.php';
