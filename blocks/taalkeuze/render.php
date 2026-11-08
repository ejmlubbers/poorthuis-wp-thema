<?php
/**
 * Taalkeuze: de vlag van de huidige taal met een chevron; bij een klik klappen de andere talen uit.
 * Werkt zonder JavaScript (details/summary); header.js sluit hem bij een klik ernaast.
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'pll_the_languages' ) ) {
	return;
}
$langs = pll_the_languages( [ 'raw' => 1, 'hide_if_empty' => 0, 'hide_if_no_translation' => 0 ] );
if ( ! $langs || count( $langs ) < 2 ) {
	return;
}
$current = null;
foreach ( $langs as $l ) {
	if ( ! empty( $l['current_lang'] ) ) {
		$current = $l;
	}
}
$current = $current ?: reset( $langs );
$vlag    = fn( $l ) => sprintf( '<img src="%s" alt="" width="20" height="14" class="ph-lang-flag">', esc_url( $l['flag'] ) );
?>
<details <?php echo get_block_wrapper_attributes( [ 'class' => 'ph-lang' ] ); ?>>
	<summary aria-label="<?php echo esc_attr( poorthuis_t( 'Taal kiezen', 'Choose language' ) . ': ' . $current['name'] ); ?>">
		<?php echo $vlag( $current ); // phpcs:ignore ?>
		<svg class="ph-lang-chev" viewBox="0 0 12 8" width="10" height="7" aria-hidden="true"><path d="M1 1.5l5 5 5-5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
	</summary>
	<ul>
		<?php foreach ( $langs as $l ) :
			if ( ! empty( $l['current_lang'] ) ) {
				continue;
			} ?>
			<li><a href="<?php echo esc_url( $l['url'] ); ?>" hreflang="<?php echo esc_attr( $l['locale'] ?? $l['slug'] ); ?>" lang="<?php echo esc_attr( $l['slug'] ); ?>"><?php echo $vlag( $l ); // phpcs:ignore ?><span><?php echo esc_html( $l['name'] ); ?></span></a></li>
		<?php endforeach; ?>
	</ul>
</details>
