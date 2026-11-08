<?php
/**
 * Oude adressen van de vorige site (/language/nl/…, /language/en/…, /language/de/…)
 * doorsturen naar de nieuwe pagina's, zodat links in oude mails en documenten blijven werken.
 * Onbekende oude adressen gaan naar de homepage van die taal.
 */

defined( 'ABSPATH' ) || exit;

const POORTHUIS_OUDE_ADRESSEN = [
	'nl' => [
		'home'                    => '/',
		'over-poorthuis'          => '/over-ons/',
		'onze-vakmensen'          => '/over-ons/',
		'certificering'           => '/over-ons/',
		'onze-diensten'           => '/onze-diensten/',
		'revisie'                 => '/onze-diensten/#revisie',
		'modificatie'             => '/onze-diensten/#modificatie',
		'gespecialiseerd-laswerk' => '/onze-diensten/#laswerk',
		'machinale-bewerking'     => '/onze-diensten/#machinale-bewerking',
		'mobiele-service'         => '/onze-diensten/',
		'prijs-advies-levertijd-2' => '/contact/',
		'contact'                 => '/contact/',
		'werken-bij-poorthuis'    => '/werken-bij/',
		'vacatures'               => '/werken-bij/#vacatures',
	],
	'en' => [
		'about-us'                       => '/en/about-us/',
		'overhauling'                    => '/en/services/#revisie',
		'modification'                   => '/en/services/#modificatie',
		'specialized-welding'            => '/en/services/#laswerk',
		'machining'                      => '/en/services/#machinale-bewerking',
		'mobile-service'                 => '/en/services/',
		'price-advice-and-delivery-time' => '/en/contact-us/',
		'contact-2'                      => '/en/contact-us/',
		'vacancies'                      => '/en/careers/',
	],
	'de' => [
		'homepage-de'                   => '/de/',
		'ueber-poorthuis'               => '/de/ueber-uns/',
		'revision'                      => '/de/leistungen/#revisie',
		'modifizierung'                 => '/de/leistungen/#modificatie',
		'spezialisiert-auf-schweissen'  => '/de/leistungen/#laswerk',
		'bearbeitung'                   => '/de/leistungen/#machinale-bewerking',
		'mobiler-dienst'                => '/de/leistungen/',
		'preis-beratung-und-lieferzeit' => '/de/kontakt/',
		'kontakt'                       => '/de/kontakt/',
	],
];

add_action( 'template_redirect', function () {
	if ( ! is_404() ) {
		return;
	}
	$path = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );
	if ( ! preg_match( '#^language/(nl|en|de)(?:/([^/]+))?#', $path, $m ) ) {
		return;
	}
	$lang = $m[1];
	$slug = $m[2] ?? '';
	$home = [ 'nl' => '/', 'en' => '/en/', 'de' => '/de/' ][ $lang ];
	wp_safe_redirect( home_url( POORTHUIS_OUDE_ADRESSEN[ $lang ][ $slug ] ?? $home ), 301, 'Poorthuis' );
	exit;
}, 1 );
