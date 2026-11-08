<?php
/**
 * Updates via GitHub: WordPress toont "Nu bijwerken" bij dit thema zodra er op
 * github.com/ejmlubbers/poorthuis-wp-thema een nieuwere release staat (met poorthuis.zip erbij).
 * Werkt via de kop "Update URI" in style.css en het filter update_themes_github.com (WP 6.1+).
 */

defined( 'ABSPATH' ) || exit;

const POORTHUIS_UPDATE_REPO = 'ejmlubbers/poorthuis-wp-thema';

/**
 * Nieuwste versie op GitHub. Leest het versienummer uit style.css op raw.githubusercontent.com
 * (geen API, dus geen limiet van 60 vragen per uur per server) en downloadt de zip van de release.
 * Bewaard: 6 uur als het lukt, 15 minuten als GitHub niet bereikbaar was.
 */
function poorthuis_laatste_release(): ?array {
	$cached = get_site_transient( 'poorthuis_release' );
	if ( is_array( $cached ) ) {
		return $cached ?: null;
	}
	$res = wp_remote_get( 'https://raw.githubusercontent.com/' . POORTHUIS_UPDATE_REPO . '/main/style.css', [
		'timeout' => 8,
		'headers' => [ 'User-Agent' => 'poorthuis-theme' ],
	] );
	$out = [];
	if ( 200 === wp_remote_retrieve_response_code( $res ) && preg_match( '/^Version:\s*([0-9][0-9.]*)/m', (string) wp_remote_retrieve_body( $res ), $m ) ) {
		$out = [
			'version' => $m[1],
			'url'     => 'https://github.com/' . POORTHUIS_UPDATE_REPO . '/releases/tag/v' . $m[1],
			'package' => 'https://github.com/' . POORTHUIS_UPDATE_REPO . '/releases/download/v' . $m[1] . '/poorthuis.zip',
		];
	}
	set_site_transient( 'poorthuis_release', $out, $out ? 6 * HOUR_IN_SECONDS : 15 * MINUTE_IN_SECONDS );
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
