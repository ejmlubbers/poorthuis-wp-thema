<?php
/**
 * Opmaak van de formuliermails: de inzending voor Poorthuis en de automatische
 * reactie aan de afzender. Gebouwd zoals de mailhandtekening: alleen tabellen,
 * inline stijlen en Arial, zodat het ook in Outlook (Windows) klopt.
 * Afbeeldingen komen uit de mediabibliotheek van poorthuis.com (dezelfde als de handtekening).
 */

defined( 'ABSPATH' ) || exit;

const POORTHUIS_MAIL_BLAUW = '#302E80';
const POORTHUIS_MAIL_GEEL  = '#F1C944';
const POORTHUIS_MAIL_INKT  = '#1A1A1A';
const POORTHUIS_MAIL_GRIJS = '#6B6B7B';
const POORTHUIS_MAIL_LIJN  = '#E1E0EA';
const POORTHUIS_MAIL_PAPER = '#F5F5F8';
const POORTHUIS_MAIL_FONT  = 'Arial, Helvetica, sans-serif';
const POORTHUIS_MAIL_LOGO  = 'https://poorthuis.com/wp-content/uploads/2026/11/poorthuis-logo-p-donker-2x.png';

/** De algemene mailhandtekening (gemaakt met docs/mailhandtekening/handtekening.js). */
function poorthuis_mail_handtekening( bool $en ): string {
	$file = __DIR__ . '/handtekening-' . ( $en ? 'en' : 'nl' ) . '.html';
	return is_readable( $file ) ? (string) file_get_contents( $file ) : '';
}

/** Rijen "label – waarde" als nette tabel. $rows = [ [label, waarde], ... ] */
function poorthuis_mail_velden( array $rows ): string {
	$f   = POORTHUIS_MAIL_FONT;
	$out = '';
	foreach ( $rows as $i => [ $label, $value ] ) {
		$border = $i ? 'border-top:1px solid ' . POORTHUIS_MAIL_LIJN . ';' : '';
		$out   .= '<tr>'
			. '<td valign="top" width="150" style="width:150px;padding:11px 16px 11px 0;' . $border . 'font-family:' . $f . ';font-size:11px;line-height:16px;letter-spacing:1px;text-transform:uppercase;color:' . POORTHUIS_MAIL_GRIJS . ';">' . esc_html( $label ) . '</td>'
			. '<td valign="top" style="padding:10px 0;' . $border . 'font-family:' . $f . ';font-size:15px;line-height:22px;color:' . POORTHUIS_MAIL_INKT . ';">' . nl2br( esc_html( $value ) ) . '</td>'
			. '</tr>';
	}
	return '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">' . $out . '</table>';
}

/** Knop die ook in Outlook een knop blijft (gevulde tabelcel). */
function poorthuis_mail_knop( string $href, string $text ): string {
	return '<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;"><tr>'
		. '<td bgcolor="' . POORTHUIS_MAIL_BLAUW . '" style="background-color:' . POORTHUIS_MAIL_BLAUW . ';border-radius:24px;padding:12px 26px;">'
		. '<a href="' . esc_url( $href ) . '" style="font-family:' . POORTHUIS_MAIL_FONT . ';font-size:15px;line-height:18px;font-weight:bold;color:#FFFFFF;text-decoration:none;">' . esc_html( $text ) . ' &rarr;</a>'
		. '</td></tr></table>';
}

/** Kleine bovenkop met gele streep ervoor, zoals op de site. */
function poorthuis_mail_bovenkop( string $text ): string {
	return '<p style="margin:0 0 10px 0;font-family:' . POORTHUIS_MAIL_FONT . ';font-size:11px;line-height:16px;letter-spacing:1.5px;text-transform:uppercase;color:' . POORTHUIS_MAIL_BLAUW . ';">'
		. '<span style="color:' . POORTHUIS_MAIL_GEEL . ';">&#8212;&#8212;</span>&nbsp; ' . esc_html( $text ) . '</p>';
}

/**
 * Het omhulsel: blauwe kop met logo, gele lijn, witte inhoud, kleine voetregel.
 * $preheader is de grijze voorbeeldregel die mailprogramma's naast het onderwerp tonen.
 */
function poorthuis_mail_omhulsel( string $inhoud, string $preheader, string $voet, bool $en ): string {
	$f = POORTHUIS_MAIL_FONT;
	return '<!DOCTYPE html><html lang="' . ( $en ? 'en' : 'nl' ) . '"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="color-scheme" content="light"><title>Poorthuis</title></head>'
		. '<body style="margin:0;padding:0;background-color:' . POORTHUIS_MAIL_PAPER . ';">'
		. '<div style="display:none;max-height:0;overflow:hidden;opacity:0;">' . esc_html( $preheader ) . '</div>'
		. '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="' . POORTHUIS_MAIL_PAPER . '" style="border-collapse:collapse;background-color:' . POORTHUIS_MAIL_PAPER . ';"><tr><td align="center" style="padding:28px 12px;">'
		. '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="width:100%;max-width:600px;border-collapse:collapse;">'
		// kop
		. '<tr><td bgcolor="' . POORTHUIS_MAIL_BLAUW . '" style="background-color:' . POORTHUIS_MAIL_BLAUW . ';padding:22px 32px;border-radius:12px 12px 0 0;">'
		. '<a href="https://poorthuis.com" style="text-decoration:none;"><img src="' . POORTHUIS_MAIL_LOGO . '" width="199" height="45" alt="Poorthuis – Solutions by specialists" style="display:block;width:199px;height:45px;border:0;font-family:' . $f . ';font-size:16px;font-weight:bold;color:#FFFFFF;"></a>'
		. '</td></tr>'
		. '<tr><td height="4" bgcolor="' . POORTHUIS_MAIL_GEEL . '" style="height:4px;line-height:4px;font-size:0;background-color:' . POORTHUIS_MAIL_GEEL . ';">&nbsp;</td></tr>'
		// inhoud
		. '<tr><td bgcolor="#FFFFFF" style="background-color:#FFFFFF;padding:34px 32px 30px 32px;border-radius:0 0 12px 12px;font-family:' . $f . ';font-size:15px;line-height:23px;color:' . POORTHUIS_MAIL_INKT . ';">'
		. $inhoud
		. '</td></tr>'
		// voet
		. '<tr><td style="padding:18px 32px 0 32px;font-family:' . $f . ';font-size:12px;line-height:18px;color:' . POORTHUIS_MAIL_GRIJS . ';text-align:center;">' . $voet . '</td></tr>'
		. '</table></td></tr></table></body></html>';
}

/** Mail voor Poorthuis: een nieuwe inzending. */
function poorthuis_mail_inzending( array $args ): string {
	$f        = POORTHUIS_MAIL_FONT;
	$naam     = $args['naam'] ?: $args['email'];
	$soort    = $args['soort'];               // bijv. "Contactformulier"
	$taal     = $args['en'] ? 'Engelstalig' : 'Nederlandstalig';
	$reply    = 'mailto:' . rawurlencode( $args['email'] ) . '?subject=' . rawurlencode( 'Re: ' . $args['onderwerp'] );
	$inhoud   = poorthuis_mail_bovenkop( $soort . ' · ' . $taal )
		. '<h1 style="margin:0 0 6px 0;font-family:' . $f . ';font-size:24px;line-height:30px;font-weight:normal;color:' . POORTHUIS_MAIL_BLAUW . ';">Nieuw bericht van ' . esc_html( $naam ) . '</h1>'
		. '<p style="margin:0 0 22px 0;color:' . POORTHUIS_MAIL_GRIJS . ';font-size:14px;">Ingevuld op ' . esc_html( $args['wanneer'] ) . '</p>'
		. poorthuis_mail_velden( $args['velden'] )
		. ( $args['bijlage'] ? '<p style="margin:16px 0 0 0;font-size:14px;color:' . POORTHUIS_MAIL_INKT . ';">&#128206; Het cv zit als bijlage bij deze mail.</p>' : '' )
		. '<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;margin-top:26px;"><tr><td>' . poorthuis_mail_knop( $reply, 'Beantwoord ' . ( $args['naam'] ?: 'afzender' ) ) . '</td></tr></table>'
		. '<p style="margin:14px 0 0 0;font-size:13px;line-height:19px;color:' . POORTHUIS_MAIL_GRIJS . ';">Of klik gewoon op Beantwoorden: uw antwoord gaat dan direct naar ' . esc_html( $args['email'] ) . '.</p>';
	$voet     = 'Verstuurd via het formulier op <a href="' . esc_url( $args['pagina'] ) . '" style="color:' . POORTHUIS_MAIL_GRIJS . ';">' . esc_html( preg_replace( '#^https?://#', '', $args['pagina'] ) ) . '</a>';
	return poorthuis_mail_omhulsel( $inhoud, $soort . ' van ' . $naam, $voet, false );
}

/** Automatische reactie aan de afzender. */
function poorthuis_mail_bevestiging( array $args ): string {
	$f      = POORTHUIS_MAIL_FONT;
	$en     = $args['en'];
	$p      = fn( $t ) => '<p style="margin:0 0 14px 0;font-family:' . $f . ';font-size:15px;line-height:23px;color:' . POORTHUIS_MAIL_INKT . ';">' . esc_html( $t ) . '</p>';
	$inhoud = '<h1 style="margin:0 0 20px 0;font-family:' . $f . ';font-size:24px;line-height:30px;font-weight:normal;color:' . POORTHUIS_MAIL_BLAUW . ';">' . esc_html( $args['titel'] ) . '</h1>'
		. $p( $args['aanhef'] )
		. implode( '', array_map( $p, $args['regels'] ) )
		. '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;margin:10px 0 28px 0;"><tr>'
		. '<td bgcolor="' . POORTHUIS_MAIL_PAPER . '" style="background-color:' . POORTHUIS_MAIL_PAPER . ';border-left:3px solid ' . POORTHUIS_MAIL_GEEL . ';padding:18px 20px 12px 20px;">'
		. poorthuis_mail_bovenkop( $en ? 'Your message' : ( $args['je'] ? 'Wat je hebt ingevuld' : 'Uw bericht' ) )
		. poorthuis_mail_velden( $args['velden'] )
		. '</td></tr></table>'
		. poorthuis_mail_handtekening( $en );
	$voet   = $en
		? 'This is an automatic confirmation. You can simply reply to this email.'
		: ( $args['je'] ? 'Dit is een automatische bevestiging. Je kunt gewoon op deze mail reageren.' : 'Dit is een automatische bevestiging. U kunt gewoon op deze mail reageren.' );
	return poorthuis_mail_omhulsel( $inhoud, $args['regels'][0] ?? '', $voet, $en );
}
