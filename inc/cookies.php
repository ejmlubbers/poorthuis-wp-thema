<?php
/**
 * Cookiemelding en Google Analytics.
 * Analytics laadt pas nadat de bezoeker op "Accepteren" klikt; de keuze staat in de cookie
 * ph_consent (6 maanden). Het meet-ID (G-…) staat bij Instellingen → Algemeen.
 * Zonder meet-ID verschijnt er geen melding: de site plaatst dan geen cookies die toestemming vragen.
 */

defined( 'ABSPATH' ) || exit;

function poorthuis_ga_id(): string {
	$id = strtoupper( trim( (string) get_option( 'poorthuis_ga_id', '' ) ) );
	return preg_match( '/^G-[A-Z0-9]{4,}$/', $id ) ? $id : '';
}

add_action( 'init', function () {
	register_setting( 'general', 'poorthuis_ga_id', [
		'type'              => 'string',
		'show_in_rest'      => true,
		'default'           => '',
		'sanitize_callback' => fn( $v ) => strtoupper( sanitize_text_field( $v ) ),
	] );
} );

add_action( 'admin_init', function () {
	add_settings_field( 'poorthuis_ga_id', 'Google Analytics-meet-ID', function () {
		printf(
			'<input type="text" name="poorthuis_ga_id" value="%s" class="regular-text" placeholder="G-XXXXXXXXXX"><p class="description">Te vinden in Google Analytics bij Beheer → Gegevensstromen. Leeg = geen statistieken en geen cookiemelding.</p>',
			esc_attr( get_option( 'poorthuis_ga_id' ) )
		);
	}, 'general' );
} );

add_action( 'wp_enqueue_scripts', function () {
	if ( ! poorthuis_ga_id() ) {
		return;
	}
	wp_enqueue_script( 'poorthuis-cookies', get_theme_file_uri( 'assets/js/cookies.js' ), [], POORTHUIS_VERSION, [ 'strategy' => 'defer', 'in_footer' => true ] );
	wp_localize_script( 'poorthuis-cookies', 'poorthuisCookies', [ 'ga' => poorthuis_ga_id() ] );
} );

add_action( 'wp_footer', function () {
	if ( ! poorthuis_ga_id() || is_admin() ) {
		return;
	}
	?>
	<div class="ph-cookie" role="dialog" aria-live="polite" aria-label="<?php echo esc_attr( poorthuis_t( 'Cookies', 'Cookies', 'Cookies' ) ); ?>" hidden>
		<p class="ph-cookie-title"><?php echo esc_html( poorthuis_t( 'Cookies', 'Cookies', 'Cookies' ) ); ?></p>
		<p class="ph-cookie-text"><?php echo wp_kses_post( sprintf(
			poorthuis_t(
				'Voor de beste gebruikerservaring gebruiken wij cookies. Daarmee meten we anoniem hoe onze website wordt gebruikt, zodat we hem kunnen verbeteren. Meer hierover leest u in onze <a href="%s">privacyverklaring</a>.',
				'We use cookies to give you the best experience on our website. They let us measure anonymously how the site is used, so we can improve it. Read more in our <a href="%s">privacy statement</a>.',
				'Für die beste Nutzererfahrung verwenden wir Cookies. Damit messen wir anonym, wie unsere Website genutzt wird, um sie zu verbessern. Mehr dazu in unserer <a href="%s">Datenschutzerklärung</a>.'
			),
			poorthuis_url( '/privacyverklaring/' )
		) ); ?></p>
		<div class="ph-cookie-buttons">
			<button type="button" class="ph-cookie-yes"><?php echo esc_html( poorthuis_t( 'Accepteren', 'Accept', 'Akzeptieren' ) ); ?></button>
			<button type="button" class="ph-cookie-no"><?php echo esc_html( poorthuis_t( 'Alleen noodzakelijk', 'Necessary only', 'Nur notwendige' ) ); ?></button>
		</div>
	</div>
	<?php
} );
