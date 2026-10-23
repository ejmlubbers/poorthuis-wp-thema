<?php
/**
 * Wereldbol. Het canvas en de markers bouwt view.js op; hier alleen de
 * gegevens en de teller met projectenlijst (ook bruikbaar zonder JavaScript).
 */

defined( 'ABSPATH' ) || exit;

$projects = poorthuis_projects();
$nl       = ! function_exists( 'pll_current_language' ) || 'nl' === pll_current_language();
$data     = [
	'land'     => get_theme_file_uri( 'assets/data/land-90000.bin' ),
	'projects' => $projects,
	'home'     => [ 'lat' => 52.31, 'lng' => 6.93, 'name' => 'Oldenzaal' ],
];
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'ph-globe-col' ] ); ?>
	data-globe="<?php echo esc_attr( wp_json_encode( $data ) ); ?>">
	<div class="ph-globe" aria-hidden="true"><canvas></canvas></div>
	<div class="ph-globe-list-wrap">
		<button type="button" class="ph-globe-count" aria-expanded="false">
			<?php echo esc_html( $nl ? 'Projecten' : 'Projects' ); ?> <b><?php echo count( $projects ); ?></b><span class="ph-globe-live"></span>
		</button>
		<ul class="ph-globe-list" hidden>
			<?php foreach ( $projects as $p ) : ?>
				<li><button type="button" data-id="<?php echo (int) $p['id']; ?>">
					<?php if ( $p['img'] ) : ?><img src="<?php echo esc_url( $p['img'] ); ?>" alt="" loading="lazy" width="36" height="36"><?php else : ?><i></i><?php endif; ?>
					<span><b><?php echo esc_html( $p['city'] . ', ' . $p['country'] ); ?></b><em><?php echo esc_html( $p['name'] ); ?></em></span>
				</button></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
