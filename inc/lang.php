<?php
/**
 * Talen. Polylang regelt de vertaalde pagina's (NL standaard, EN onder /en/, DE onder /de/);
 * deze helpers laten kop, voet en blokken de taal van de pagina volgen.
 * Zonder Polylang is alles gewoon Nederlands.
 */

defined( 'ABSPATH' ) || exit;

/** 'nl', 'en' of 'de'. */
function poorthuis_lang(): string {
	$l = function_exists( 'pll_current_language' ) ? (string) pll_current_language() : '';
	return in_array( $l, [ 'en', 'de' ], true ) ? $l : 'nl';
}

/**
 * Duitse teksten voor kop, voet, formulieren en mails, op basis van de Nederlandse tekst.
 * (In de code staat overal poorthuis_t( 'Nederlands', 'English' ); de Duitse versie komt hieruit.)
 */
const POORTHUIS_DE = [
	// kop en voet
	'Over ons'                  => 'Über uns',
	'Onze diensten'             => 'Unsere Leistungen',
	'Werken bij'                => 'Karriere',
	'Bel'                       => 'Anrufen',
	'Informatie'                => 'Informationen',
	'Algemene voorwaarden'      => 'AGB',
	'Privacyverklaring'         => 'Datenschutzerklärung',
	'Cookie-instellingen'       => 'Cookie-Einstellungen',
	'Alle rechten voorbehouden' => 'Alle Rechte vorbehalten',
	'Taal kiezen'               => 'Sprache wählen',
	// blokken
	'Bekijk de bedrijfsfilm'    => 'Unternehmensfilm ansehen',
	'Projecten'                 => 'Projekte',
	'Interesse?'                => 'Interesse?',
	'Laat je gegevens achter en vertel kort wie je bent. We nemen snel contact met je op.' => 'Hinterlasse deine Daten und erzähl uns kurz, wer du bist. Wir melden uns schnell bei dir.',
	'Terug naar alle vacatures' => 'Zurück zu allen Stellenangeboten',
	// formulier
	'Versturen'                 => 'Senden',
	'Verstuur sollicitatie'     => 'Bewerbung senden',
	'Naam'                      => 'Name',
	'E-mailadres'               => 'E-Mail-Adresse',
	'Uw bericht'                => 'Ihre Nachricht',
	'Bedrijf'                   => 'Firma',
	'Telefoonnummer'            => 'Telefonnummer',
	'Voornaam'                  => 'Vorname',
	'Achternaam'                => 'Nachname',
	'Opleiding'                 => 'Ausbildung / Studium',
	'Onderwijsinstelling'       => 'Schule / Hochschule',
	'Studiejaar'                => 'Ausbildungsjahr',
	'Gewenste stageperiode'     => 'Gewünschter Zeitraum',
	'Wat zou je bij ons willen leren?' => 'Was möchtest du bei uns lernen?',
	'Motivatie'                 => 'Motivation',
	'Cv (pdf of Word, max. 5 MB)' => 'Lebenslauf (PDF oder Word, max. 5 MB)',
	'Bedankt voor je aanmelding! We nemen snel contact met je op.' => 'Danke für deine Bewerbung! Wir melden uns bald bei dir.',
	'Bedankt! Wij nemen spoedig contact met u op.' => 'Vielen Dank! Wir melden uns in Kürze bei Ihnen.',
	'Bezig met versturen…'      => 'Wird gesendet …',
	'Versturen is niet gelukt.' => 'Senden fehlgeschlagen.',
	'Ik ga akkoord met de verwerking van mijn gegevens volgens de <a href="%s">privacyverklaring</a>.' => 'Ich bin mit der Verarbeitung meiner Daten gemäß der <a href="%s">Datenschutzerklärung</a> einverstanden.',
];

/** Kies de tekst voor de huidige taal. Duits: derde argument, of anders uit POORTHUIS_DE. */
function poorthuis_t( string $nl, string $en, ?string $de = null ): string {
	switch ( poorthuis_lang() ) {
		case 'en':
			return $en;
		case 'de':
			return $de ?? POORTHUIS_DE[ $nl ] ?? $en;
		default:
			return $nl;
	}
}

/** Nederlandse paden en hun tegenhanger per taal. Gelijk houden met PATHS in tools/blocks.py. */
const POORTHUIS_PATHS = [
	'en' => [
		'/'                      => '/en/',
		'/over-ons/'             => '/en/about-us/',
		'/onze-diensten/'        => '/en/services/',
		'/werken-bij/'           => '/en/careers/',
		'/contact/'              => '/en/contact-us/',
		'/algemene-voorwaarden/' => '/en/terms-and-conditions/',
		'/privacyverklaring/'    => '/en/privacy-statement/',
	],
	'de' => [
		'/'                      => '/de/',
		'/over-ons/'             => '/de/ueber-uns/',
		'/onze-diensten/'        => '/de/leistungen/',
		'/werken-bij/'           => '/de/karriere/',
		'/contact/'              => '/de/kontakt/',
		'/algemene-voorwaarden/' => '/de/agb/',
		'/privacyverklaring/'    => '/de/datenschutz/',
	],
];

/** Interne link in de huidige taal, op basis van het Nederlandse pad. */
function poorthuis_url( string $nl_path ): string {
	[ $path, $frag ] = array_pad( explode( '#', $nl_path, 2 ), 2, '' );
	$lang = poorthuis_lang();
	if ( 'nl' !== $lang ) {
		$path = POORTHUIS_PATHS[ $lang ][ $path ] ?? $path;
	}
	return esc_url( home_url( $path ) . ( $frag ? '#' . $frag : '' ) );
}
