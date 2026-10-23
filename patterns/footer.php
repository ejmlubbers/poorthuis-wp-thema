<?php
/**
 * Title: Voet
 * Slug: poorthuis/footer
 * Inserter: no
 */
?>
<!-- wp:group {"tagName":"footer","className":"ph-dark","backgroundColor":"blue-darker","style":{"spacing":{"padding":{"top":"64px","bottom":"0"}},"elements":{"link":{"color":{"text":"#ffffff"}}}},"layout":{"type":"constrained","wideSize":"1240px"}} -->
<footer class="wp-block-group ph-dark has-blue-darker-background-color has-background has-link-color" style="padding-top:64px;padding-bottom:0">
	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"40px"}}}} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"width":"44%"} -->
		<div class="wp-block-column" style="flex-basis:44%">
			<!-- wp:image {"width":"auto","height":"30px","sizeSlug":"full","style":{"border":{"radius":"0px"}}} -->
			<figure class="wp-block-image size-full is-resized has-custom-border"><img src="<?php echo poorthuis_img( 'logo-white.png' ); ?>" alt="Poorthuis Industrial Services" style="border-radius:0px;width:auto;height:30px"/></figure>
			<!-- /wp:image -->
			<!-- wp:paragraph {"style":{"typography":{"fontSize":"15px"}}} -->
			<p style="font-size:15px">Poorthuis Industrial Services — Solutions by Specialists.</p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px","lineHeight":"1.8"}}} -->
			<p style="font-size:14px;line-height:1.8">Schelmaatstraat 12, 7575 BD Oldenzaal<br><a href="tel:+31541513415">+31 541-513 415</a><br><a href="mailto:info@poorthuis.com">info@poorthuis.com</a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"13px","letterSpacing":"0.1em","textTransform":"uppercase"}},"textColor":"yellow"} -->
			<h4 class="wp-block-heading has-yellow-color has-text-color" style="font-size:13px;letter-spacing:0.1em;text-transform:uppercase">Menu</h4>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"style":{"typography":{"fontSize":"15px","lineHeight":"2"}}} -->
			<p style="font-size:15px;line-height:2"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a><br><a href="<?php echo esc_url( home_url( '/over-ons/' ) ); ?>">Over ons</a><br><a href="<?php echo esc_url( home_url( '/onze-diensten/' ) ); ?>">Onze diensten</a><br><a href="<?php echo esc_url( home_url( '/werken-bij/' ) ); ?>">Werken bij</a><br><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"13px","letterSpacing":"0.1em","textTransform":"uppercase"}},"textColor":"yellow"} -->
			<h4 class="wp-block-heading has-yellow-color has-text-color" style="font-size:13px;letter-spacing:0.1em;text-transform:uppercase">Informatie</h4>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"style":{"typography":{"fontSize":"15px","lineHeight":"2"}}} -->
			<p style="font-size:15px;line-height:2"><a href="<?php echo esc_url( home_url( '/algemene-voorwaarden/' ) ); ?>">Algemene voorwaarden</a><br><a href="<?php echo esc_url( home_url( '/privacyverklaring/' ) ); ?>">Privacyverklaring</a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

	<!-- wp:group {"align":"wide","style":{"border":{"top":{"color":"#ffffff1f","width":"1px"}},"spacing":{"padding":{"top":"18px","bottom":"18px"},"margin":{"top":"40px"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
	<div class="wp-block-group alignwide" style="border-top-color:#ffffff1f;border-top-width:1px;margin-top:40px;padding-top:18px;padding-bottom:18px">
		<!-- wp:paragraph {"style":{"typography":{"fontSize":"12.5px"}}} -->
		<p style="font-size:12.5px">© <?php echo esc_html( gmdate( 'Y' ) ); ?> Poorthuis Industrial Services — Alle rechten voorbehouden</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</footer>
<!-- /wp:group -->
