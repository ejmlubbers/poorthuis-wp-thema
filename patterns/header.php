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
			<!-- wp:navigation {"overlayMenu":"never","className":"ph-nav","layout":{"type":"flex","justifyContent":"right"},"style":{"spacing":{"blockGap":"30px"}}} -->
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

			<!-- wp:html -->
			<?php
			$ph_links = [
				[ '/', 'Home' ],
				[ '/over-ons/', poorthuis_t( 'Over ons', 'About us' ) ],
				[ '/onze-diensten/', poorthuis_t( 'Onze diensten', 'Our services' ) ],
				[ '/werken-bij/', poorthuis_t( 'Werken bij', 'Careers' ) ],
				[ '/contact/', poorthuis_t( 'Contact', 'Contact', 'Kontakt' ) ],
			];
			?>
			<button type="button" class="ph-mnav-open" aria-haspopup="dialog" aria-controls="ph-mnav" aria-label="<?php echo esc_attr( poorthuis_t( 'Menu openen', 'Open menu', 'Menü öffnen' ) ); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></button>
			<dialog id="ph-mnav" class="ph-mnav" aria-label="Menu">
				<div class="ph-mnav-top">
					<a class="ph-mnav-logo" href="<?php echo poorthuis_url( '/' ); ?>"><img src="<?php echo poorthuis_img( 'logo-kop-color.png' ); ?>" alt="Poorthuis Industrial Services" height="38"></a>
					<button type="button" class="ph-mnav-close" aria-label="<?php echo esc_attr( poorthuis_t( 'Menu sluiten', 'Close menu', 'Menü schließen' ) ); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></button>
				</div>
				<nav class="ph-mnav-main" aria-label="<?php echo esc_attr( poorthuis_t( 'Hoofdmenu', 'Main menu', 'Hauptmenü' ) ); ?>">
					<ul>
						<?php foreach ( $ph_links as [ $path, $label ] ) : ?>
						<li><a href="<?php echo poorthuis_url( $path ); ?>"><?php echo esc_html( $label ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</nav>
				<div class="ph-mnav-foot">
					<p class="ph-mnav-contact"><a href="tel:+31541513415">+31 541-513 415</a><a href="mailto:info@poorthuis.com">info@poorthuis.com</a></p>
					<ul class="ph-mnav-small">
						<?php if ( get_page_by_path( 'algemene-voorwaarden' ) ) : ?>
						<li><a href="<?php echo poorthuis_url( '/algemene-voorwaarden/' ); ?>"><?php echo esc_html( poorthuis_t( 'Algemene voorwaarden', 'Terms & conditions' ) ); ?></a></li>
						<?php endif; ?>
						<li><a href="<?php echo poorthuis_url( '/privacyverklaring/' ); ?>"><?php echo esc_html( poorthuis_t( 'Privacyverklaring', 'Privacy statement' ) ); ?></a></li>
						<?php if ( function_exists( 'poorthuis_ga_id' ) && poorthuis_ga_id() ) : ?>
						<li><a href="#" class="ph-cookie-open"><?php echo esc_html( poorthuis_t( 'Cookie-instellingen', 'Cookie settings' ) ); ?></a></li>
						<?php endif; ?>
					</ul>
					<p class="ph-mnav-copy">© <?php echo esc_html( gmdate( 'Y' ) ); ?> Poorthuis Industrial Services</p>
				</div>
			</dialog>
			<!-- /wp:html -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</header>
<!-- /wp:group -->
