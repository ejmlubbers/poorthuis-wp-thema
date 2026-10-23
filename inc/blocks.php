<?php
/**
 * Eigen blokken (blocks/*): wereldbol, contactformulier, bedrijfsfilm.
 * Geen bouwstap: elk blok heeft een block.json, een PHP-render en kleine
 * scripts die direct de wp.*-globals gebruiken.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
	foreach ( glob( get_theme_file_path( 'blocks/*/block.json' ) ) as $file ) {
		register_block_type( dirname( $file ) );
	}
} );

add_filter( 'block_categories_all', function ( $cats ) {
	array_unshift( $cats, [ 'slug' => 'poorthuis', 'title' => 'Poorthuis' ] );
	return $cats;
} );

/** Projecten voor de wereldbol, in volgorde van de beheerder. */
function poorthuis_projects(): array {
	$posts = get_posts( [
		'post_type'      => 'project',
		'posts_per_page' => 200,
		'orderby'        => [ 'menu_order' => 'ASC', 'title' => 'ASC' ],
		'post_status'    => 'publish',
	] );
	return array_map( fn( $p ) => [
		'id'      => $p->ID,
		'name'    => get_the_title( $p ),
		'city'    => (string) get_post_meta( $p->ID, 'plaats', true ),
		'country' => (string) get_post_meta( $p->ID, 'land', true ),
		'sector'  => (string) get_post_meta( $p->ID, 'sector', true ),
		'lat'     => (float) get_post_meta( $p->ID, 'lat', true ),
		'lng'     => (float) get_post_meta( $p->ID, 'lng', true ),
		'img'     => get_the_post_thumbnail_url( $p, 'medium' ) ?: '',
	], $posts );
}
