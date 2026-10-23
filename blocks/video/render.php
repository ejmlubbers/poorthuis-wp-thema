<?php
/**
 * Bedrijfsfilm: rustige voorvertoning (afspeelknop alleen bij hover) met een
 * tekstregel eronder; view.js zet er pas na een klik YouTube in.
 */

defined( 'ABSPATH' ) || exit;

$id = preg_replace( '/[^\w-]/', '', $attributes['youtube'] ?? '' );
if ( ! $id ) {
	return;
}
$poster = ! empty( $attributes['posterId'] ) ? wp_get_attachment_image_url( $attributes['posterId'], 'large' ) : '';
$poster = $poster ?: ( $attributes['posterUrl'] ?? '' ) ?: poorthuis_img( 'al-70-jaar.webp' );
$label  = ( $attributes['label'] ?? '' ) ?: 'Bekijk de bedrijfsfilm';
$play   = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5v13l11-6.5z" fill="currentColor"/></svg>';
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'ph-video' ] ); ?> data-youtube="<?php echo esc_attr( $id ); ?>">
	<div class="ph-video-frame">
		<button type="button" class="ph-video-btn" aria-label="<?php echo esc_attr( $label ); ?>">
			<img src="<?php echo esc_url( $poster ); ?>" alt="" loading="lazy">
			<span class="ph-video-play"><?php echo $play; // phpcs:ignore -- vaste svg ?></span>
		</button>
	</div>
	<button type="button" class="ph-video-caption" tabindex="-1" aria-hidden="true"><?php echo $play; // phpcs:ignore ?> <?php echo esc_html( $label ); ?></button>
</div>
