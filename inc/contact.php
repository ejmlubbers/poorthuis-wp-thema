<?php
/**
 * Formulieren: één REST-route die het formulierblok (blocks/contact-form)
 * verstuurt via wp_mail (en dus via Easy WP SMTP / Microsoft 365).
 * Spamwering zonder plugin: een onzichtbaar honeypotveld, een minimale
 * invultijd en een simpele limiet per IP-adres.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/mail/opmaak.php';

/** Foutmeldingen voor bezoekers van de Engelse site. */
const POORTHUIS_FORM_ERRORS_EN = [
	'Het formulier is verlopen. Herlaad de pagina en probeer het opnieuw.' => 'The form has expired. Please reload the page and try again.',
	'Te veel berichten in korte tijd. Probeer het later opnieuw.'          => 'Too many messages in a short time. Please try again later.',
	'Vul een geldig e-mailadres in.'                                         => 'Please enter a valid email address.',
	'Het cv moet een pdf- of Word-bestand van maximaal 5 MB zijn.'           => 'Your CV must be a PDF or Word file of up to 5 MB.',
	'Versturen is niet gelukt. Mail ons gerust direct op info@poorthuis.com.' => 'Sending failed. Feel free to email us directly at info@poorthuis.com.',
];

const POORTHUIS_FORM_FIELDS = [
	// sleutel => [label, verplicht]
	'naam'          => [ 'Naam', false ],
	'voornaam'      => [ 'Voornaam', false ],
	'achternaam'    => [ 'Achternaam', false ],
	'bedrijf'       => [ 'Bedrijf', false ],
	'email'         => [ 'E-mailadres', true ],
	'telefoon'      => [ 'Telefoon', false ],
	'opleiding'     => [ 'Opleiding', false ],
	'school'        => [ 'Onderwijsinstelling', false ],
	'studiejaar'    => [ 'Studiejaar', false ],
	'periode'       => [ 'Gewenste stageperiode', false ],
	'leren'         => [ 'Wat wil je bij ons leren?', false ],
	'bericht'       => [ 'Bericht', false ],
	'motivatie'     => [ 'Motivatie', false ],
];

/** Engelse veldnamen, voor de kopie in de automatische reactie aan Engelstalige afzenders. */
const POORTHUIS_FORM_LABELS_EN = [
	'naam' => 'Name', 'voornaam' => 'First name', 'achternaam' => 'Last name', 'bedrijf' => 'Company',
	'email' => 'Email address', 'telefoon' => 'Phone', 'opleiding' => 'Course', 'school' => 'School',
	'studiejaar' => 'Year of study', 'periode' => 'Preferred period', 'leren' => 'What would you like to learn?',
	'bericht' => 'Message', 'motivatie' => 'Motivation',
];

/**
 * Automatische reactie aan de afzender: bevestiging, kopie van wat hij invulde en de algemene handtekening.
 * Contact in de u-vorm, stage en sollicitatie in de je-vorm (zoals op de site).
 * Teksten: concept, nog door Eline/Poorthuis goed te keuren.
 */
function poorthuis_form_autoreply( string $to, string $name, string $variant, bool $en, array $velden ): bool {
	$je = in_array( $variant, [ 'stage', 'sollicitatie' ], true );
	if ( $en ) {
		$subject = $je ? 'Thank you for your application – Poorthuis Industrial Services' : 'Thank you for your message – Poorthuis Industrial Services';
		$titel   = $je ? 'Thank you for your application' : 'Thank you for your message';
		$aanhef  = $name ? 'Dear ' . $name . ',' : 'Hello,';
		$regels  = $je
			? [ 'Thank you for your application. We have received it and will be in touch with you soon.', 'You can simply reply to this email. Do you have an urgent question? Then call us on +31 541 513 415.' ]
			: [ 'Thank you for your message. We have received it and will get back to you shortly.', 'You can simply reply to this email. Do you have an urgent question or a breakdown? Then call us directly on +31 541 513 415.' ];
	} else {
		$subject = $je ? 'Bedankt voor je aanmelding – Poorthuis Industrial Services' : 'Bedankt voor uw bericht – Poorthuis Industrial Services';
		$titel   = $je ? 'Bedankt voor je aanmelding' : 'Bedankt voor uw bericht';
		$aanhef  = $name ? ( $je ? 'Hoi ' : 'Beste ' ) . $name . ',' : ( $je ? 'Hoi,' : 'Beste heer/mevrouw,' );
		$regels  = $je
			? [ 'Bedankt voor je aanmelding! We hebben hem goed ontvangen en nemen snel contact met je op.', 'Je kunt gewoon reageren op deze mail. Heb je een dringende vraag? Bel ons dan op +31 541 513 415.' ]
			: [ 'Bedankt voor uw bericht. Wij hebben het goed ontvangen en nemen binnen korte tijd contact met u op.', 'U kunt gewoon reageren op deze mail. Heeft u een dringende vraag of spoed? Bel ons dan direct op +31 541 513 415.' ];
	}
	$body = poorthuis_mail_bevestiging( compact( 'en', 'je', 'titel', 'aanhef', 'regels', 'velden' ) );
	return wp_mail( $to, $subject, $body, [ 'Content-Type: text/html; charset=UTF-8', 'Reply-To: ' . poorthuis_form_recipient() ] );
}

/** Ontvanger: in te stellen bij Instellingen → Algemeen. */
function poorthuis_form_recipient(): string {
	$to = get_option( 'poorthuis_form_recipient' );
	return is_email( $to ) ? $to : get_option( 'admin_email' );
}

add_action( 'admin_init', function () {
	register_setting( 'general', 'poorthuis_form_recipient', [ 'type' => 'string', 'sanitize_callback' => 'sanitize_email' ] );
	add_settings_field( 'poorthuis_form_recipient', 'Ontvanger formulieren', function () {
		printf(
			'<input type="email" name="poorthuis_form_recipient" value="%s" class="regular-text" placeholder="%s"><p class="description">Hier komen contact- en stageformulieren binnen. Leeg = het e-mailadres van de beheerder.</p>',
			esc_attr( get_option( 'poorthuis_form_recipient' ) ),
			esc_attr( get_option( 'admin_email' ) )
		);
	}, 'general' );
} );

/** Ondertekend tijdstip, zodat we kunnen zien hoe snel een formulier is ingevuld. */
function poorthuis_form_token(): string {
	$t = (string) time();
	return $t . '.' . wp_hash( 'poorthuis-form|' . $t );
}

add_action( 'rest_api_init', function () {
	register_rest_route( 'poorthuis/v1', '/contact', [
		'methods'             => 'POST',
		'permission_callback' => '__return_true',
		'callback'            => 'poorthuis_handle_form',
	] );
} );

function poorthuis_handle_form( WP_REST_Request $req ) {
	$p    = $req->get_body_params();
	$en   = 'en' === ( $p['_lang'] ?? '' );
	$fail = fn( $msg, $code = 400 ) => new WP_REST_Response( [ 'ok' => false, 'message' => $en ? ( POORTHUIS_FORM_ERRORS_EN[ $msg ] ?? $msg ) : $msg ], $code );

	// honeypot: bots vullen dit verborgen veld in
	if ( ! empty( $p['website'] ) ) {
		return new WP_REST_Response( [ 'ok' => true ], 200 );
	}
	// minimaal 3 seconden en maximaal een dag tussen laden en versturen
	[ $t, $sig ] = array_pad( explode( '.', (string) ( $p['_t'] ?? '' ) ), 2, '' );
	if ( ! hash_equals( wp_hash( 'poorthuis-form|' . $t ), $sig ) || time() - (int) $t < 3 || time() - (int) $t > DAY_IN_SECONDS ) {
		return $fail( 'Het formulier is verlopen. Herlaad de pagina en probeer het opnieuw.' );
	}
	// maximaal 5 inzendingen per kwartier per IP
	$key   = 'ph_form_' . md5( $_SERVER['REMOTE_ADDR'] ?? '' );
	$count = (int) get_transient( $key );
	if ( $count >= 5 ) {
		return $fail( 'Te veel berichten in korte tijd. Probeer het later opnieuw.', 429 );
	}
	set_transient( $key, $count + 1, 15 * MINUTE_IN_SECONDS );

	$email = sanitize_email( $p['email'] ?? '' );
	if ( ! is_email( $email ) ) {
		return $fail( 'Vul een geldig e-mailadres in.' );
	}

	$velden = []; // [label, waarde] voor Poorthuis
	$kopie  = []; // dezelfde gegevens in de taal van de afzender
	foreach ( POORTHUIS_FORM_FIELDS as $k => [ $label ] ) {
		if ( ! isset( $p[ $k ] ) || '' === trim( (string) $p[ $k ] ) ) {
			continue;
		}
		$v        = in_array( $k, [ 'bericht', 'motivatie', 'leren' ], true ) ? sanitize_textarea_field( $p[ $k ] ) : sanitize_text_field( $p[ $k ] );
		$velden[] = [ $label, $v ];
		$kopie[]  = [ $en ? ( POORTHUIS_FORM_LABELS_EN[ $k ] ?? $label ) : $label, $v ];
	}
	$page = esc_url_raw( $p['_page'] ?? '' );
	$subject = sanitize_text_field( $p['_subject'] ?? 'Formulier poorthuis.com' );
	if ( $en ) {
		$subject .= ' (Engels)'; // zoals op de huidige site: Poorthuis ziet meteen in welke taal ze antwoorden
	}

	// cv als bijlage (alleen bij het stageformulier), max. 5 MB, pdf/doc/docx
	$attachments = [];
	$files       = $req->get_file_params();
	if ( ! empty( $files['cv']['tmp_name'] ) && UPLOAD_ERR_OK === $files['cv']['error'] ) {
		$f     = $files['cv'];
		$check = wp_check_filetype_and_ext( $f['tmp_name'], $f['name'], [
			'pdf'  => 'application/pdf',
			'doc'  => 'application/msword',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		] );
		if ( ! $check['ext'] || $f['size'] > 5 * MB_IN_BYTES ) {
			return $fail( 'Het cv moet een pdf- of Word-bestand van maximaal 5 MB zijn.' );
		}
		$tmp = trailingslashit( get_temp_dir() ) . wp_unique_filename( get_temp_dir(), 'cv-' . sanitize_file_name( $f['name'] ) );
		if ( move_uploaded_file( $f['tmp_name'], $tmp ) ) {
			$attachments[] = $tmp;
		}
	}

	$variant = sanitize_key( $p['_variant'] ?? '' );
	$naam    = sanitize_text_field( $p['naam'] ?? trim( ( $p['voornaam'] ?? '' ) . ' ' . ( $p['achternaam'] ?? '' ) ) );
	$soorten = [ 'kort' => 'Contactformulier (homepage)', 'volledig' => 'Contactformulier', 'stage' => 'Stage-aanmelding', 'sollicitatie' => 'Sollicitatie' ];
	$body    = poorthuis_mail_inzending( [
		'soort'     => $soorten[ $variant ] ?? 'Formulier',
		'naam'      => $naam,
		'email'     => $email,
		'onderwerp' => $subject,
		'en'        => $en,
		'velden'    => $velden,
		'pagina'    => $page ?: home_url( '/' ),
		'wanneer'   => wp_date( 'j F Y \o\m H:i' ),
		'bijlage'   => (bool) $attachments,
	] );
	$sent = wp_mail(
		poorthuis_form_recipient(),
		$subject,
		$body,
		[ 'Content-Type: text/html; charset=UTF-8', 'Reply-To: ' . $email ],
		$attachments
	);
	array_map( 'wp_delete_file', $attachments );

	if ( $sent ) {
		poorthuis_form_autoreply( $email, sanitize_text_field( $p['naam'] ?? $p['voornaam'] ?? '' ), $variant, $en, $kopie );
	}

	return $sent
		? new WP_REST_Response( [ 'ok' => true ], 200 )
		: $fail( 'Versturen is niet gelukt. Mail ons gerust direct op info@poorthuis.com.', 500 );
}
