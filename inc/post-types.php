<?php
/**
 * Berichttypes: projecten (voor de wereldbol) en vacatures.
 * Zonder ACF: gewone post meta, bewerkbaar in een zijpaneel van de editor
 * (assets/js/meta-panel.js) en bruikbaar in blokken via Block Bindings.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
	register_post_type( 'project', [
		'labels'        => [
			'name'          => 'Projecten',
			'singular_name' => 'Project',
			'add_new_item'  => 'Nieuw project',
			'edit_item'     => 'Project bewerken',
			'menu_name'     => 'Projecten',
		],
		'public'        => false,      // alleen zichtbaar op de wereldbol, geen eigen pagina
		'show_ui'       => true,
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-location-alt',
		'menu_position' => 21,
		'supports'      => [ 'title', 'thumbnail', 'custom-fields', 'page-attributes' ],
	] );

	register_post_type( 'vacature', [
		'labels'        => [
			'name'          => 'Vacatures',
			'singular_name' => 'Vacature',
			'add_new_item'  => 'Nieuwe vacature',
			'edit_item'     => 'Vacature bewerken',
			'menu_name'     => 'Vacatures',
		],
		'public'        => true,
		'show_in_rest'  => true,
		'has_archive'   => false,     // de lijst staat op de pagina Werken bij
		'rewrite'       => [ 'slug' => 'vacatures', 'with_front' => false ],
		'menu_icon'     => 'dashicons-groups',
		'menu_position' => 22,
		'supports'      => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'page-attributes' ],
		'template'      => [
			[ 'core/paragraph', [ 'placeholder' => 'Beschrijf de vacature…' ] ],
		],
	] );

	$fields = [
		'project'  => [
			'plaats' => 'string',  // bijv. Megalopolis
			'land'   => 'string',  // landcode, bijv. GR
			'sector' => 'string',  // bijv. Energieproductie
			'lat'    => 'number',
			'lng'    => 'number',
		],
		'vacature' => [
			'dienstverband' => 'string', // bijv. Fulltime, Stage
			'locatie'       => 'string', // bijv. Oldenzaal + op locatie
		],
	];
	foreach ( $fields as $type => $keys ) {
		foreach ( $keys as $key => $kind ) {
			register_post_meta( $type, $key, [
				'type'              => $kind,
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'number' === $kind ? 'floatval' : 'sanitize_text_field',
				'auth_callback'     => fn() => current_user_can( 'edit_posts' ),
			] );
		}
	}
} );

// Zijpaneel in de editor voor bovenstaande velden.
add_action( 'enqueue_block_editor_assets', function () {
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->post_type, [ 'project', 'vacature' ], true ) ) {
		return;
	}
	wp_enqueue_script(
		'poorthuis-meta-panel',
		get_theme_file_uri( 'assets/js/meta-panel.js' ),
		[ 'wp-plugins', 'wp-editor', 'wp-components', 'wp-data', 'wp-core-data', 'wp-element' ],
		POORTHUIS_VERSION,
		true
	);
} );

// Kolommen in het overzicht, zodat je in één oogopslag ziet wat waar staat.
add_filter( 'manage_project_posts_columns', fn( $c ) => array_slice( $c, 0, 2 ) + [ 'plaats' => 'Plaats', 'land' => 'Land' ] + $c );
add_filter( 'manage_vacature_posts_columns', fn( $c ) => array_slice( $c, 0, 2 ) + [ 'dienstverband' => 'Dienstverband', 'locatie' => 'Locatie' ] + $c );
add_action( 'manage_project_posts_custom_column', fn( $col, $id ) => print esc_html( get_post_meta( $id, $col, true ) ), 10, 2 );
add_action( 'manage_vacature_posts_custom_column', fn( $col, $id ) => print esc_html( get_post_meta( $id, $col, true ) ), 10, 2 );
