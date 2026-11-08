<?php
/**
 * Title: Home · Over Poorthuis (DE)
 * Slug: poorthuis/home-over-de
 * Categories: poorthuis-secties
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1240px"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center">
<!-- wp:column {"width":"48%"} -->
<div class="wp-block-column" style="flex-basis:48%">
<!-- wp:image {"sizeSlug":"large","height":"440px","scale":"cover"} -->
<figure class="wp-block-image size-large is-resized"><img src="<?php echo poorthuis_img( 'team-samen.webp' ); ?>" alt="Das Team von Poorthuis" style="object-fit:cover;height:440px"/></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:paragraph {"className":"is-style-eyebrow"} -->
<p class="is-style-eyebrow">Über Poorthuis</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">Ein eingespieltes Team von Fachleuten</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Poorthuis ist bekannt für sein spezialisiertes Team, das sich eng mit dem Kunden verbunden fühlt. Klare Kommunikation und gute Zusammenarbeit bilden die Grundlage unserer Arbeit. Da unser Fach in keiner Standardausbildung vorkommt, bilden wir unsere Mitarbeiter intern zu Spezialisten aus.</p>
<!-- /wp:paragraph -->
<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button {"className":"is-style-lijn"} -->
<div class="wp-block-button is-style-lijn"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/de/ueber-uns/' ) ); ?>">Mehr über uns</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
