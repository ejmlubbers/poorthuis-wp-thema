<?php
/**
 * SEO: de Yoast-velden voor SEO-titel en metabeschrijving ook via de REST-API bereikbaar maken
 * (voor beheerders/redacteuren), zodat ze in één keer voor alle talen ingevuld kunnen worden.
 * In de editor blijven ze gewoon te bewerken in het Yoast-paneel.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
	foreach ( [ 'page', 'vacature' ] as $type ) {
		foreach ( [ '_yoast_wpseo_title', '_yoast_wpseo_metadesc', '_yoast_wpseo_opengraph-image', '_yoast_wpseo_opengraph-image-id', '_yoast_wpseo_twitter-image', '_yoast_wpseo_twitter-image-id' ] as $key ) {
			register_post_meta( $type, $key, [
				'type'          => 'string',
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => fn( $allowed, $meta_key, $post_id ) => current_user_can( 'edit_post', $post_id ),
			] );
		}
	}
}, 20 );

/*
 * Deelafbeelding (WhatsApp, LinkedIn, Facebook): het Poorthuis-logo op blauw in plaats van
 * de eerste afbeelding op de pagina (het menneke). De mediabibliotheek-id staat in de optie
 * poorthuis_deelafbeelding; pagina's en vacatures zonder eigen deelafbeelding krijgen hem
 * bij het opslaan automatisch. In Yoast (tabblad Sociale media) kun je per pagina een andere kiezen.
 */
add_action( 'init', function () {
	register_setting( 'general', 'poorthuis_deelafbeelding', [ 'type' => 'integer', 'show_in_rest' => true, 'default' => 0 ] );
} );

function poorthuis_zet_deelafbeelding( int $post_id ): void {
	$id = (int) get_option( 'poorthuis_deelafbeelding', 0 );
	if ( ! $id || wp_is_post_revision( $post_id ) || get_post_meta( $post_id, '_yoast_wpseo_opengraph-image-id', true ) ) {
		return;
	}
	$url = wp_get_attachment_url( $id );
	if ( ! $url ) {
		return;
	}
	update_post_meta( $post_id, '_yoast_wpseo_opengraph-image', $url );
	update_post_meta( $post_id, '_yoast_wpseo_opengraph-image-id', $id );
	update_post_meta( $post_id, '_yoast_wpseo_twitter-image', $url );
	update_post_meta( $post_id, '_yoast_wpseo_twitter-image-id', $id );
}
add_action( 'save_post_page', 'poorthuis_zet_deelafbeelding', 5 );
add_action( 'save_post_vacature', 'poorthuis_zet_deelafbeelding', 5 );
