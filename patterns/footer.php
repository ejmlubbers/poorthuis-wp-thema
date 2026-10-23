<?php
/**
 * Title: Voet
 * Slug: poorthuis/footer
 * Inserter: no
 */
$ico = fn( $n ) => '<span class="ph-foot-ico"><img src="' . poorthuis_img( '../icons/' . $n . '-wit.svg' ) . '" alt="" width="16" height="16"></span>';
?>
<!-- wp:group {"tagName":"footer","className":"ph-footer","backgroundColor":"blue-darker","style":{"spacing":{"padding":{"top":"64px","bottom":"0"}}},"layout":{"type":"constrained","wideSize":"1240px"}} -->
<footer class="wp-block-group ph-footer has-blue-darker-background-color has-background" style="padding-top:64px;padding-bottom:0">
	<!-- wp:columns {"align":"wide","className":"ph-footer-grid","style":{"spacing":{"blockGap":{"left":"40px"}}}} -->
	<div class="wp-block-columns alignwide ph-footer-grid">
		<!-- wp:column {"width":"41%"} -->
		<div class="wp-block-column" style="flex-basis:41%">
			<!-- wp:image {"width":"auto","height":"30px","sizeSlug":"full","className":"ph-foot-logo","style":{"border":{"radius":"0px"}}} -->
			<figure class="wp-block-image size-full is-resized has-custom-border ph-foot-logo"><img src="<?php echo poorthuis_img( 'logo-white.png' ); ?>" alt="Poorthuis Industrial Services" style="border-radius:0px;width:auto;height:30px"/></figure>
			<!-- /wp:image -->
			<!-- wp:paragraph {"className":"ph-foot-tagline"} -->
			<p class="ph-foot-tagline">Poorthuis Industrial Services — Solutions by Specialists.</p>
			<!-- /wp:paragraph -->
			<!-- wp:html -->
			<ul class="ph-foot-contact">
				<li><?php echo $ico( 'pin' ); ?>Schelmaatstraat 12, 7575 BD Oldenzaal</li>
				<li><?php echo $ico( 'telefoon' ); ?><a href="tel:+31541513415">+31 541-513 415</a></li>
				<li><?php echo $ico( 'mail' ); ?><a href="mailto:info@poorthuis.com">info@poorthuis.com</a></li>
			</ul>
			<!-- /wp:html -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:paragraph {"className":"ph-foot-head"} -->
			<p class="ph-foot-head">Menu</p>
			<!-- /wp:paragraph -->
			<!-- wp:list {"className":"ph-foot-links"} -->
			<ul class="wp-block-list ph-foot-links">
				<!-- wp:list-item --><li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li><!-- /wp:list-item -->
				<!-- wp:list-item --><li><a href="<?php echo esc_url( home_url( '/over-ons/' ) ); ?>">Over ons</a></li><!-- /wp:list-item -->
				<!-- wp:list-item --><li><a href="<?php echo esc_url( home_url( '/onze-diensten/' ) ); ?>">Onze diensten</a></li><!-- /wp:list-item -->
				<!-- wp:list-item --><li><a href="<?php echo esc_url( home_url( '/werken-bij/' ) ); ?>">Werken bij</a></li><!-- /wp:list-item -->
				<!-- wp:list-item --><li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a></li><!-- /wp:list-item -->
			</ul>
			<!-- /wp:list -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:paragraph {"className":"ph-foot-head"} -->
			<p class="ph-foot-head">Informatie</p>
			<!-- /wp:paragraph -->
			<!-- wp:list {"className":"ph-foot-links"} -->
			<ul class="wp-block-list ph-foot-links">
				<!-- wp:list-item --><li><a href="<?php echo esc_url( home_url( '/algemene-voorwaarden/' ) ); ?>">Algemene voorwaarden</a></li><!-- /wp:list-item -->
				<!-- wp:list-item --><li><a href="<?php echo esc_url( home_url( '/privacyverklaring/' ) ); ?>">Privacyverklaring</a></li><!-- /wp:list-item -->
			</ul>
			<!-- /wp:list -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

	<!-- wp:group {"align":"wide","className":"ph-foot-bottom","layout":{"type":"flex","justifyContent":"space-between"}} -->
	<div class="wp-block-group alignwide ph-foot-bottom">
		<!-- wp:paragraph -->
		<p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Poorthuis Industrial Services — Alle rechten voorbehouden</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</footer>
<!-- /wp:group -->
