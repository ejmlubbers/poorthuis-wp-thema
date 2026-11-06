<?php
/**
 * Title: Home · Over Poorthuis (EN)
 * Slug: poorthuis/home-over-en
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
<figure class="wp-block-image size-large is-resized"><img src="<?php echo poorthuis_img( 'team-samen.webp' ); ?>" alt="The Poorthuis team" style="object-fit:cover;height:440px"/></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:paragraph {"className":"is-style-eyebrow"} -->
<p class="is-style-eyebrow">About Poorthuis</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">A close-knit team of craftsmen</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Poorthuis is known for its specialist team that feels closely involved with the client. Clear communication and good collaboration form the basis of our work. Because our trade falls outside standard training, we train our people internally to become specialists.</p>
<!-- /wp:paragraph -->
<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button {"className":"is-style-lijn"} -->
<div class="wp-block-button is-style-lijn"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/en/about-us/' ) ); ?>">More about us</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
