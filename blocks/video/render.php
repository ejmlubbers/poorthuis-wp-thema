<?php
/** Bedrijfsfilm: eigen voorvertoning; view.js zet er pas na een klik YouTube in. */

defined( 'ABSPATH' ) || exit;

$id = preg_replace( '/[^\w-]/', '', $attributes['youtube'] ?? '' );
if ( ! $id ) {
	return;
}
$poster = ! empty( $attributes['posterId'] ) ? wp_get_attachment_image_url( $attributes['posterId'], 'large' ) : '';
$poster = $poster ?: ( $attributes['posterUrl'] ?? '' ) ?: poorthuis_img( 'al-70-jaar.webp' );
$label  = $attributes['label'] ?? '';
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'ph-video' ] ); ?> data-youtube="<?php echo esc_attr( $id ); ?>">
	<button type="button" class="ph-video-btn" aria-label="<?php echo esc_attr( $label ?: 'Video afspelen' ); ?>">
		<img src="<?php echo esc_url( $poster ); ?>" alt="" loading="lazy">
		<span class="ph-video-play"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5v13l11-6.5z" fill="currentColor"/></svg></span>
		<?php if ( $label ) : ?><span class="ph-video-label"><?php echo esc_html( $label ); ?></span><?php endif; ?>
	</button>
</div>
