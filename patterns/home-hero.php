<?php
/**
 * Title: Home · Hero
 * Slug: poorthuis/home-hero
 * Categories: poorthuis-secties
 */
?>
<!-- wp:group {"align":"full","className":"ph-dark ph-hero is-style-streep","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"20px"}},"background":{"backgroundImage":{"url":"<?php echo poorthuis_img( 'header-home.webp' ); ?>","source":"file","title":""},"backgroundSize":"cover","backgroundPosition":"50% 50%"}},"layout":{"type":"constrained","wideSize":"1240px"}} -->
<div class="wp-block-group alignfull ph-dark ph-hero is-style-streep" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:20px">
<!-- wp:columns {"verticalAlignment":"bottom","align":"wide","className":"ph-hero-cols"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-bottom ph-hero-cols">
<!-- wp:column {"verticalAlignment":"bottom","width":"27%"} -->
<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:27%">
<!-- wp:image {"sizeSlug":"large","className":"ph-menneke","style":{"border":{"radius":"0px"}}} -->
<figure class="wp-block-image size-large ph-menneke has-custom-border"><img src="<?php echo poorthuis_img( 'menneke-zonder-voetstuk.webp' ); ?>" alt="Poorthuis menneke" style="border-radius:0px"/></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->
<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center">
<!-- wp:paragraph {"className":"is-style-eyebrow"} -->
<p class="is-style-eyebrow">Sinds 1949 · Oldenzaal (NL)</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":1,"className":"ph-hero-title"} -->
<h1 class="wp-block-heading ph-hero-title"><span class="ph-light">Solutions by</span><br>Specialists</h1>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Inspectie, revisie, service en modificatie van afsluiters en stoom- en drukinstallaties, op locatie of in onze eigen werkplaats. Ruim 75 jaar expertise in afsluitertechniek, wereldwijd ingezet door een team van 21 specialisten.</p>
<!-- /wp:paragraph -->
<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/onze-diensten/' ) ); ?>">Onze diensten</a></div>
<!-- /wp:button -->
<!-- wp:button {"className":"is-style-lijn"} -->
<div class="wp-block-button is-style-lijn"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Direct contact</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
