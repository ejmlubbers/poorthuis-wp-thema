<?php
/**
 * SEO: de Yoast-velden voor SEO-titel en metabeschrijving ook via de REST-API bereikbaar maken
 * (voor beheerders/redacteuren), zodat ze in één keer voor alle talen ingevuld kunnen worden.
 * In de editor blijven ze gewoon te bewerken in het Yoast-paneel.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
	foreach ( [ 'page', 'vacature' ] as $type ) {
		foreach ( [ '_yoast_wpseo_title', '_yoast_wpseo_metadesc' ] as $key ) {
			register_post_meta( $type, $key, [
				'type'          => 'string',
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => fn( $allowed, $meta_key, $post_id ) => current_user_can( 'edit_post', $post_id ),
			] );
		}
	}
}, 20 );
