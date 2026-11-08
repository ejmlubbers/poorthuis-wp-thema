<?php
/**
 * Updates via GitHub: WordPress toont "Nu bijwerken" bij dit thema zodra er op
 * github.com/ejmlubbers/poorthuis-wp-thema een nieuwere release staat (met poorthuis.zip erbij).
 * Werkt via de kop "Update URI" in style.css en het filter update_themes_github.com (WP 6.1+).
 */

defined( 'ABSPATH' ) || exit;

const POORTHUIS_UPDATE_REPO = 'ejmlubbers/poorthuis-wp-thema';

/** Laatste release van GitHub (6 uur bewaard, zodat we GitHub niet bij elke pagina vragen). */
function poorthuis_laatste_release(): ?array {
	$cached = get_site_transient( 'poorthuis_release' );
	if ( is_array( $cached ) ) {
		return $cached ?: null;
	}
	$res  = wp_remote_get( 'https://api.github.com/repos/' . POORTHUIS_UPDATE_REPO . '/releases/latest', [
		'timeout' => 8,
		'headers' => [ 'Accept' => 'application/vnd.github+json', 'User-Agent' => 'poorthuis-theme' ],
	] );
	$data = json_decode( (string) wp_remote_retrieve_body( $res ), true );
	$out  = [];
	if ( 200 === wp_remote_retrieve_response_code( $res ) && ! empty( $data['tag_name'] ) ) {
		foreach ( (array) ( $data['assets'] ?? [] ) as $asset ) {
			if ( 'poorthuis.zip' === ( $asset['name'] ?? '' ) ) {
				$out = [
					'version' => ltrim( (string) $data['tag_name'], 'vV' ),
					'url'     => (string) ( $data['html_url'] ?? '' ),
					'package' => (string) $asset['browser_download_url'],
				];
			}
		}
	}
	set_site_transient( 'poorthuis_release', $out, $out ? 6 * HOUR_IN_SECONDS : HOUR_IN_SECONDS );
	return $out ?: null;
}

add_filter( 'update_themes_github.com', function ( $update, $theme_data, $stylesheet ) {
	if ( 'poorthuis' !== $stylesheet ) {
		return $update;
	}
	$rel = poorthuis_laatste_release();
	if ( ! $rel ) {
		return $update;
	}
	return [
		'theme'        => 'poorthuis',
		'version'      => $rel['version'],
		'url'          => $rel['url'],
		'package'      => $rel['package'],
		'requires'     => '6.6',
		'requires_php' => '7.4',
	];
}, 10, 3 );

// Na een update meteen opnieuw kijken, en "Nu controleren" bij Updates laat ook GitHub opnieuw vragen.
add_action( 'upgrader_process_complete', fn() => delete_site_transient( 'poorthuis_release' ) );
add_action( 'load-update-core.php', fn() => isset( $_GET['force-check'] ) && delete_site_transient( 'poorthuis_release' ) );
