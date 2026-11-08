<?php
/**
 * Formulier. Varianten: kort (homepage), volledig (contactpagina), stage.
 * Verstuurt naar /wp-json/poorthuis/v1/contact (inc/contact.php).
 */

defined( 'ABSPATH' ) || exit;

// Pagina's met een formulier niet in de paginacache (W3 Total Cache e.d.): het formulier bevat
// een ondertekend tijdstip, en een oude kopie uit de cache zou na een dag 'verlopen' melden.
if ( ! defined( 'DONOTCACHEPAGE' ) ) {
	define( 'DONOTCACHEPAGE', true );
}

$variant = $attributes['variant'] ?? 'volledig';
$lang    = poorthuis_lang();
$t       = 'poorthuis_t';

$subjects = [
	'kort'     => 'Contactformulier homepage poorthuis.com',
	'volledig' => 'Contactformulier poorthuis.com',
	'stage'    => 'Stage-aanmelding poorthuis.com',
	'sollicitatie' => 'Sollicitatie via poorthuis.com',
];
$subject = ( $attributes['subject'] ?? '' ) ?: $subjects[ $variant ];
$button  = ( $attributes['button'] ?? '' ) ?: ( in_array( $variant, [ 'stage', 'sollicitatie' ], true ) ? $t( 'Verstuur sollicitatie', 'Send application' ) : $t( 'Versturen', 'Send' ) );
if ( is_singular( 'vacature' ) && ! ( $attributes['subject'] ?? '' ) ) {
	$subject .= ' — ' . get_the_title(); // zodat je in de mail ziet op welke vacature het is
}

// [naam, label, type, verplicht, breed]
$fields = [
	'kort'     => [
		[ 'naam', $t( 'Naam', 'Name' ), 'text', true, true ],
		[ 'email', $t( 'E-mailadres', 'Email address' ), 'email', true, true ],
		[ 'bericht', $t( 'Uw bericht', 'Your message' ), 'textarea', true, true ],
	],
	'volledig' => [
		[ 'naam', $t( 'Naam', 'Name' ), 'text', true, false ],
		[ 'bedrijf', $t( 'Bedrijf', 'Company' ), 'text', false, false ],
		[ 'email', $t( 'E-mailadres', 'Email address' ), 'email', true, false ],
		[ 'telefoon', $t( 'Telefoonnummer', 'Phone number' ), 'tel', false, false ],
		[ 'bericht', $t( 'Uw bericht', 'Your message' ), 'textarea', true, true ],
	],
	'stage'    => [
		[ 'voornaam', $t( 'Voornaam', 'First name' ), 'text', true, false ],
		[ 'achternaam', $t( 'Achternaam', 'Last name' ), 'text', true, false ],
		[ 'email', $t( 'E-mailadres', 'Email address' ), 'email', true, false ],
		[ 'telefoon', $t( 'Telefoonnummer', 'Phone number' ), 'tel', false, false ],
		[ 'opleiding', $t( 'Opleiding', 'Course' ), 'text', true, false ],
		[ 'school', $t( 'Onderwijsinstelling', 'School' ), 'text', false, false ],
		[ 'studiejaar', $t( 'Studiejaar', 'Year of study' ), 'text', false, false ],
		[ 'periode', $t( 'Gewenste stageperiode', 'Preferred period' ), 'text', false, false ],
		[ 'leren', $t( 'Wat zou je bij ons willen leren?', 'What would you like to learn?' ), 'textarea', false, true ],
		[ 'motivatie', $t( 'Motivatie', 'Motivation' ), 'textarea', false, true ],
		[ 'cv', $t( 'Cv (pdf of Word, max. 5 MB)', 'CV (pdf or Word, max. 5 MB)' ), 'file', false, true ],
	],
	'sollicitatie' => [
		[ 'naam', $t( 'Naam', 'Name' ), 'text', true, false ],
		[ 'email', $t( 'E-mailadres', 'Email address' ), 'email', true, false ],
		[ 'telefoon', $t( 'Telefoonnummer', 'Phone number' ), 'tel', false, true ],
		[ 'motivatie', $t( 'Motivatie', 'Motivation' ), 'textarea', false, true ],
		[ 'cv', $t( 'Cv (pdf of Word, max. 5 MB)', 'CV (pdf or Word, max. 5 MB)' ), 'file', false, true ],
	],
][ $variant ] ?? [];

$uid      = wp_unique_id( 'ph-form-' );
$autocomp = [ 'naam' => 'name', 'voornaam' => 'given-name', 'achternaam' => 'family-name', 'email' => 'email', 'telefoon' => 'tel', 'bedrijf' => 'organization' ];
?>
<form <?php echo get_block_wrapper_attributes( [ 'class' => 'ph-form ph-form--' . $variant ] ); ?>
	action="<?php echo esc_url( wp_make_link_relative( rest_url( 'poorthuis/v1/contact' ) ) ); ?>" method="post" enctype="multipart/form-data" novalidate
	data-ok="<?php echo esc_attr( in_array( $variant, [ 'stage', 'sollicitatie' ], true ) ? $t( 'Bedankt voor je aanmelding! We nemen snel contact met je op.', 'Thanks for applying! We will be in touch soon.' ) : $t( 'Bedankt! Wij nemen spoedig contact met u op.', 'Thank you! We will be in touch shortly.' ) ); ?>"
	data-sending="<?php echo esc_attr( $t( 'Bezig met versturen…', 'Sending…' ) ); ?>"
	data-error="<?php echo esc_attr( $t( 'Versturen is niet gelukt.', 'Sending failed.' ) ); ?>">
	<div class="ph-form-grid">
		<?php foreach ( $fields as [ $name, $label, $type, $req, $wide ] ) :
			$id = $uid . '-' . $name; ?>
			<div class="ph-field<?php echo $wide ? ' is-wide' : ''; ?>">
				<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
				<?php if ( 'textarea' === $type ) : ?>
					<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" rows="4"<?php echo $req ? ' required' : ''; ?>></textarea>
				<?php elseif ( 'file' === $type ) : ?>
					<input id="<?php echo esc_attr( $id ); ?>" type="file" name="cv" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
				<?php else : ?>
					<input id="<?php echo esc_attr( $id ); ?>" type="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( $name ); ?>"<?php echo isset( $autocomp[ $name ] ) ? ' autocomplete="' . esc_attr( $autocomp[ $name ] ) . '"' : ''; ?><?php echo $req ? ' required' : ''; ?>>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>

	<!-- onzichtbaar voor mensen; bots vullen het in -->
	<div class="ph-hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
	<input type="hidden" name="_t" value="<?php echo esc_attr( poorthuis_form_token() ); ?>">
	<input type="hidden" name="_lang" value="<?php echo esc_attr( $lang ); ?>">
	<input type="hidden" name="_subject" value="<?php echo esc_attr( $subject ); ?>">
	<input type="hidden" name="_variant" value="<?php echo esc_attr( $variant ); ?>">

	<label class="ph-check">
		<input type="checkbox" name="akkoord" required>
		<span><?php echo wp_kses_post( sprintf(
			$t( 'Ik ga akkoord met de verwerking van mijn gegevens volgens de <a href="%s">privacyverklaring</a>.', 'I agree to the processing of my data in accordance with the <a href="%s">privacy statement</a>.' ),
			esc_url( get_privacy_policy_url() ?: '#' )
		) ); ?></span>
	</label>

	<div class="wp-block-button"><button type="submit" class="wp-block-button__link wp-element-button"><?php echo esc_html( $button ); ?> <span class="ph-arrow" aria-hidden="true">→</span></button></div>
	<p class="ph-form-status" role="status" aria-live="polite"></p>
</form>
