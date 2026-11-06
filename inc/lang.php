<?php
/**
 * Talen. Polylang regelt de vertaalde pagina's (NL standaard, EN onder /en/);
 * deze helpers laten kop, voet en blokken de taal van de pagina volgen.
 * Zonder Polylang is alles gewoon Nederlands.
 */

defined( 'ABSPATH' ) || exit;

/** 'en' of 'nl'. */
function poorthuis_lang(): string {
	return function_exists( 'pll_current_language' ) && 'en' === pll_current_language() ? 'en' : 'nl';
}

/** Kies de tekst voor de huidige taal. */
function poorthuis_t( string $nl, string $en ): string {
	return 'en' === poorthuis_lang() ? $en : $nl;
}

/** Nederlandse paden en hun Engelse tegenhanger. */
const POORTHUIS_EN_PATHS = [
	'/'                     => '/en/',
	'/over-ons/'            => '/en/about-us/',
	'/onze-diensten/'       => '/en/services/',
	'/werken-bij/'          => '/en/careers/',
	'/contact/'             => '/en/contact-us/',
	'/algemene-voorwaarden/' => '/en/terms-and-conditions/',
	'/privacyverklaring/'   => '/en/privacy-statement/',
];

/** Interne link in de huidige taal, op basis van het Nederlandse pad. */
function poorthuis_url( string $nl_path ): string {
	[ $path, $frag ] = array_pad( explode( '#', $nl_path, 2 ), 2, '' );
	if ( 'en' === poorthuis_lang() ) {
		$path = POORTHUIS_EN_PATHS[ $path ] ?? $path;
	}
	return esc_url( home_url( $path ) . ( $frag ? '#' . $frag : '' ) );
}
