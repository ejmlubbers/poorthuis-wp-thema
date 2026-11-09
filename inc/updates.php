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
 * Bewaard: 3 uur als het lukt, 15 minuten als GitHub niet bereikbaar was.
 */
function poorthuis_laatste_release(): ?array {
	$cached = get_site_transient( 'poorthuis_release' );
	if ( is_array( $cached ) ) {
		return $cached ?: null;
	}
	// Eerst GitHub zelf (?v=… per minuut anders, zodat de 5-minutenkopie van de CDN geen oude versie geeft),
	// dan jsDelivr als reserve: gedeelde hosting wordt door GitHub soms geweigerd (te veel aanvragen per adres).
	$bronnen = [
		'https://raw.githubusercontent.com/' . POORTHUIS_UPDATE_REPO . '/main/style.css?v=' . floor( time() / 60 ),
		'https://cdn.jsdelivr.net/gh/' . POORTHUIS_UPDATE_REPO . '@main/style.css?v=' . floor( time() / 60 ),
	];
	$out    = [];
	$status = [];
	foreach ( $bronnen as $bron ) {
		$res  = wp_remote_get( $bron, [ 'timeout' => 8, 'headers' => [ 'User-Agent' => 'poorthuis-theme' ] ] );
		$code = is_wp_error( $res ) ? $res->get_error_message() : wp_remote_retrieve_response_code( $res );
		$status[] = wp_parse_url( $bron, PHP_URL_HOST ) . ': ' . $code;
		if ( 200 === $code && preg_match( '/^Version:\s*([0-9][0-9.]*)/m', (string) wp_remote_retrieve_body( $res ), $m ) ) {
			$out = [
				'version' => $m[1],
				'url'     => 'https://github.com/' . POORTHUIS_UPDATE_REPO . '/releases/tag/v' . $m[1],
				'package' => 'https://github.com/' . POORTHUIS_UPDATE_REPO . '/releases/download/v' . $m[1] . '/poorthuis.zip',
			];
			break;
		}
	}
	update_option( 'poorthuis_update_status', [ 'tijd' => gmdate( 'c' ), 'bronnen' => $status, 'versie' => $out['version'] ?? null ], false );
	set_site_transient( 'poorthuis_release', $out, $out ? 3 * HOUR_IN_SECONDS : 15 * MINUTE_IN_SECONDS );
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

// Na een update meteen opnieuw kijken. "Opnieuw controleren" bij Updates vraagt GitHub ook opnieuw:
// prioriteit 0, zodat het geheugen leeg is vóórdat WordPress zelf (prioriteit 10) gaat controleren.
add_action( 'upgrader_process_complete', fn() => delete_site_transient( 'poorthuis_release' ) );
add_action( 'load-update-core.php', function () {
	if ( isset( $_GET['force-check'] ) ) {
		delete_site_transient( 'poorthuis_release' );
	}
}, 0 );
add_action( 'delete_site_transient_update_themes', fn() => delete_site_transient( 'poorthuis_release' ) );

// Controlepunt voor beheerders: wat krijgt deze server terug bij de updatecontrole?
// GET /wp-json/poorthuis/v1/updatestatus?opnieuw=1 vraagt het meteen opnieuw.
add_action( 'rest_api_init', function () {
	register_rest_route( 'poorthuis/v1', '/updatestatus', [
		'methods'             => 'GET',
		'permission_callback' => fn() => current_user_can( 'update_themes' ),
		'callback'            => function ( WP_REST_Request $req ) {
			if ( $req->get_param( 'opnieuw' ) ) {
				delete_site_transient( 'poorthuis_release' );
				poorthuis_laatste_release();
			}
			return [
				'geinstalleerd' => wp_get_theme( 'poorthuis' )->get( 'Version' ),
				'laatste'       => get_option( 'poorthuis_update_status' ),
				'bewaard'       => get_site_transient( 'poorthuis_release' ),
			];
		},
	] );
} );
