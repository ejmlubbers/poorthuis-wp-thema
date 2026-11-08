<?php
/**
 * Title: Kop
 * Slug: poorthuis/header
 * Inserter: no
 */
?>
<!-- wp:group {"tagName":"header","className":"ph-header","layout":{"type":"constrained","wideSize":"1240px"}} -->
<header class="wp-block-group ph-header">
	<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"17px","bottom":"17px"}}},"layout":{"type":"flex","justifyContent":"space-between","flexWrap":"nowrap"}} -->
	<div class="wp-block-group alignwide" style="padding-top:17px;padding-bottom:17px">
		<!-- wp:image {"width":"auto","height":"42px","sizeSlug":"full","linkDestination":"custom","className":"ph-logo","style":{"border":{"radius":"0px"}}} -->
		<figure class="wp-block-image size-full is-resized has-custom-border ph-logo"><a href="<?php echo poorthuis_url( '/' ); ?>"><img src="<?php echo poorthuis_img( 'logo-kop-color.png' ); ?>" alt="Poorthuis Industrial Services" style="border-radius:0px;width:auto;height:42px"/><img class="ph-logo-white" src="<?php echo poorthuis_img( 'logo-kop-white.png' ); ?>" alt="" aria-hidden="true" style="border-radius:0px;width:auto;height:42px"/></a></figure>
		<!-- /wp:image -->

		<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","blockGap":"24px"}} -->
		<div class="wp-block-group">
			<!-- wp:navigation {"overlayMenu":"mobile","layout":{"type":"flex","justifyContent":"right"},"style":{"spacing":{"blockGap":"30px"}}} -->
				<!-- wp:navigation-link {"label":"Home","url":"<?php echo poorthuis_url( '/' ); ?>","kind":"custom"} /-->
				<!-- wp:navigation-link {"label":"<?php echo esc_html( poorthuis_t( 'Over ons', 'About us' ) ); ?>","url":"<?php echo poorthuis_url( '/over-ons/' ); ?>","kind":"custom"} /-->
				<!-- wp:navigation-link {"label":"<?php echo esc_html( poorthuis_t( 'Onze diensten', 'Our services' ) ); ?>","url":"<?php echo poorthuis_url( '/onze-diensten/' ); ?>","kind":"custom"} /-->
				<!-- wp:navigation-link {"label":"<?php echo esc_html( poorthuis_t( 'Werken bij', 'Careers' ) ); ?>","url":"<?php echo poorthuis_url( '/werken-bij/' ); ?>","kind":"custom"} /-->
				<!-- wp:navigation-link {"label":"<?php echo esc_html( poorthuis_t( 'Contact', 'Contact', 'Kontakt' ) ); ?>","url":"<?php echo poorthuis_url( '/contact/' ); ?>","kind":"custom"} /-->
			<!-- /wp:navigation -->

			<!-- wp:html -->
			<a class="ph-phone" href="tel:+31541513415" aria-label="<?php echo esc_attr( poorthuis_t( 'Bel', 'Call' ) ); ?> +31 541-513 415"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/></svg></a>
			<!-- /wp:html -->

			<!-- wp:poorthuis/taalkeuze /-->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</header>
<!-- /wp:group -->
