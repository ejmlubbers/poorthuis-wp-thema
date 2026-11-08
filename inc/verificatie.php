<?php
/**
 * Verificatiebestanden van zoekmachines (Google Search Console e.d.) die normaal in de
 * hoofdmap van de server staan. Het thema beantwoordt deze adressen zelf.
 */

defined( 'ABSPATH' ) || exit;

const POORTHUIS_VERIFICATIE = [
	'google605d5edcfd422590.html' => 'google-site-verification: google605d5edcfd422590.html',
];

add_action( 'parse_request', function () {
	$path = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );
	if ( isset( POORTHUIS_VERIFICATIE[ $path ] ) ) {
		status_header( 200 );
		header( 'Content-Type: text/html; charset=utf-8' );
		echo POORTHUIS_VERIFICATIE[ $path ]; // phpcs:ignore
		exit;
	}
}, 0 );
