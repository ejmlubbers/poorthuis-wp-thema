<?php
/**
 * Basis: stijlen laden en overbodige WordPress-onderdelen uitzetten,
 * zodat de site zo licht mogelijk blijft.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', function () {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/site.css' );
	add_theme_support( 'responsive-embeds' );
} );

// Eén klein stylesheet voor de vormgeving die theme.json niet kan uitdrukken.
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'poorthuis', get_theme_file_uri( 'assets/css/site.css' ), [], POORTHUIS_VERSION );
	wp_enqueue_script( 'poorthuis-header', get_theme_file_uri( 'assets/js/header.js' ), [], POORTHUIS_VERSION, [ 'strategy' => 'defer', 'in_footer' => true ] );
} );

// Pagina's die met een donkere sectie beginnen: de kop ligt daar doorzichtig overheen (zoals in de demo).
add_filter( 'body_class', function ( $classes ) {
	if ( is_front_page() || is_singular( 'vacature' ) ) {
		$classes[] = 'ph-dark-top';
	} elseif ( is_singular() ) {
		$start = substr( (string) get_post_field( 'post_content', get_queried_object_id() ), 0, 600 );
		if ( preg_match( '/ph-(page-)?hero\b/', $start ) ) {
			$classes[] = 'ph-dark-top';
		}
	}
	return $classes;
} );

// Overhead die deze site niet gebruikt.
add_action( 'init', function () {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'print_emoji_detection_script' );
	add_filter( 'emoji_svg_url', '__return_false' );
} );

// Reacties staan nergens aan op deze site.
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_action( 'admin_menu', function () {
	remove_menu_page( 'edit-comments.php' );
} );

// Alleen de blokpatronen van dit thema tonen, niet die van wordpress.org.
add_action( 'after_setup_theme', function () {
	remove_theme_support( 'core-block-patterns' );
} );
add_filter( 'should_load_remote_block_patterns', '__return_false' );

// Patrooncategorieën voor het inserter-menu.
add_action( 'init', function () {
	register_block_pattern_category( 'poorthuis-secties', [ 'label' => 'Poorthuis · secties' ] );
	register_block_pattern_category( 'poorthuis-paginas', [ 'label' => 'Poorthuis · pagina\'s' ] );
} );

// Blokstijlen die Cindy in de editor kan aanklikken (vormgeving in site.css).
add_action( 'init', function () {
	$styles = [
		'core/paragraph' => [ 'eyebrow' => 'Bovenkop (met geel streepje)' ],
		'core/button'    => [ 'blauw' => 'Blauw', 'lijn' => 'Met lijn' ],
		'core/group'     => [
			'kaart'       => 'Kaart',
			'cijfer'      => 'Cijfer',
			'certificaat' => 'Certificaat',
			'tijdlijn'    => 'Tijdlijn',
			'streep'      => 'Waarschuwingsstreep onderaan',
		],
		'core/cover'     => [ 'streep' => 'Waarschuwingsstreep onderaan' ],
		'core/list'      => [ 'vinkjes' => 'Met vinkjes' ],
	];
	foreach ( $styles as $block => $list ) {
		foreach ( $list as $name => $label ) {
			register_block_style( $block, [ 'name' => $name, 'label' => $label ] );
		}
	}
} );

/** Pad naar een afbeelding in het thema, voor gebruik in patronen. */
function poorthuis_img( string $file ): string {
	return esc_url( get_theme_file_uri( 'assets/images/' . $file ) );
}
