<?php
/**
 * Formulieren: één REST-route die het formulierblok (blocks/contact-form)
 * verstuurt via wp_mail (en dus via Easy WP SMTP / Microsoft 365).
 * Spamwering zonder plugin: een onzichtbaar honeypotveld, een minimale
 * invultijd en een simpele limiet per IP-adres.
 */

defined( 'ABSPATH' ) || exit;

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

	$rows = [];
	foreach ( POORTHUIS_FORM_FIELDS as $k => [ $label ] ) {
		if ( ! isset( $p[ $k ] ) || '' === trim( (string) $p[ $k ] ) ) {
			continue;
		}
		$v      = in_array( $k, [ 'bericht', 'motivatie', 'leren' ], true ) ? sanitize_textarea_field( $p[ $k ] ) : sanitize_text_field( $p[ $k ] );
		$rows[] = sprintf( '<tr><th align="left" valign="top" style="padding:6px 16px 6px 0;color:#555;font-weight:600">%s</th><td style="padding:6px 0">%s</td></tr>', esc_html( $label ), nl2br( esc_html( $v ) ) );
	}
	$page    = esc_url_raw( $p['_page'] ?? '' );
	$rows[]  = sprintf( '<tr><th align="left" style="padding:6px 16px 6px 0;color:#555;font-weight:600">Pagina</th><td style="padding:6px 0">%s</td></tr>', esc_html( $page ) );
	$subject = sanitize_text_field( $p['_subject'] ?? 'Formulier poorthuis.com' );

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

	$body = '<div style="font-family:Arial,sans-serif;font-size:15px;color:#1a1a1a"><table cellspacing="0" cellpadding="0">' . implode( '', $rows ) . '</table></div>';
	$sent = wp_mail(
		poorthuis_form_recipient(),
		$subject,
		$body,
		[ 'Content-Type: text/html; charset=UTF-8', 'Reply-To: ' . $email ],
		$attachments
	);
	array_map( 'wp_delete_file', $attachments );

	return $sent
		? new WP_REST_Response( [ 'ok' => true ], 200 )
		: $fail( 'Versturen is niet gelukt. Mail ons gerust direct op info@poorthuis.com.', 500 );
}
