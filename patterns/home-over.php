<?php
/**
 * Title: Home · Over Poorthuis
 * Slug: poorthuis/home-over
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
<figure class="wp-block-image size-large is-resized"><img src="<?php echo poorthuis_img( 'team-samen.webp' ); ?>" alt="Het team van Poorthuis" style="object-fit:cover;height:440px"/></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:paragraph {"className":"is-style-eyebrow"} -->
<p class="is-style-eyebrow">Over Poorthuis</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">Een hecht team van vaklieden</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Poorthuis staat bekend om haar specialistische team dat zich nauw betrokken voelt bij de klant. Heldere communicatie en goede onderlinge samenwerking vormen de basis van ons werk. Omdat ons vak niet binnen een standaard opleiding valt, leiden wij onze mensen intern op tot specialist.</p>
<!-- /wp:paragraph -->
<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button {"className":"is-style-lijn"} -->
<div class="wp-block-button is-style-lijn"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/over-ons/' ) ); ?>">Meer over ons</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
