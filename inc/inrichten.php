<?php
/**
 * Inrichten: zet de inhoud van de nieuwe site klaar (pagina's in NL/EN/DE uit de themapatronen,
 * projecten voor de wereldbol, de stagevacature, de privacyverklaring) en stelt Polylang en
 * de homepage in. Veilig om opnieuw te draaien: wat al bestaat (zelfde slug) wordt overgeslagen.
 *
 * Aanroepen: POST /wp-json/poorthuis/v1/inrichten (alleen beheerders), of via tools/seed.php lokaal.
 *
 * Onderhoudsmodus: zolang de optie poorthuis_onderhoud aan staat, zien bezoekers (niet ingelogd)
 * alleen een scherm "Site wordt verbouwd". Uitzetten: Instellingen → Algemeen, of via de REST-API
 * (/wp/v2/settings, poorthuis_onderhoud = false).
 */

defined( 'ABSPATH' ) || exit;

/* ------------------------------------------------------------------ onderhoudsscherm */

add_action( 'init', function () {
	register_setting( 'general', 'poorthuis_onderhoud', [ 'type' => 'boolean', 'show_in_rest' => true, 'default' => true ] );
	register_setting( 'general', 'poorthuis_form_recipient', [ 'type' => 'string', 'show_in_rest' => true, 'sanitize_callback' => 'sanitize_email' ] );
	register_setting( 'general', 'poorthuis_form_bcc', [ 'type' => 'string', 'show_in_rest' => true, 'sanitize_callback' => 'sanitize_email' ] );
} );

add_action( 'admin_init', function () {
	add_settings_field( 'poorthuis_onderhoud', 'Site wordt verbouwd', function () {
		printf( '<label><input type="checkbox" name="poorthuis_onderhoud" value="1" %s> Bezoekers zien alleen het scherm "Site wordt verbouwd" (ingelogde beheerders zien de site gewoon)</label>', checked( poorthuis_onderhoud(), true, false ) );
	}, 'general' );
} );

/** Staat de onderhoudsmodus aan? Bij een nieuwe installatie: ja, tot het inrichten klaar is. */
function poorthuis_onderhoud(): bool {
	return (bool) get_option( 'poorthuis_onderhoud', true );
}

add_action( 'template_redirect', function () {
	if ( ! poorthuis_onderhoud() || current_user_can( 'edit_posts' ) ) {
		return;
	}
	status_header( 503 );
	header( 'Retry-After: 3600' );
	nocache_headers();
	$logo = esc_url( get_theme_file_uri( 'assets/images/logo-kop-color.png' ) );
	?><!DOCTYPE html>
<html lang="nl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex">
<title>Poorthuis Industrial Services</title>
<style>
html,body{margin:0;height:100%}
body{display:grid;place-items:center;min-height:100vh;min-width:100vw;background:#fff;font-family:system-ui,-apple-system,"Segoe UI",Arial,sans-serif;color:#302E80;text-align:center}
main{padding:24px}
img{height:56px;width:auto;display:block;margin:0 auto 34px}
h1{font-weight:400;font-size:clamp(26px,4vw,38px);margin:0 0 10px}
p{margin:0;color:#5A5A6E;font-size:16px;line-height:1.6}
.streep{position:fixed;left:0;right:0;bottom:0;height:10px;background:repeating-linear-gradient(-45deg,#F1C944 0 14px,#302E80 14px 28px)}
</style></head>
<body><main>
<img src="<?php echo $logo; ?>" alt="Poorthuis">
<h1>Site wordt verbouwd</h1>
<p>We zijn de website aan het vernieuwen en zijn zo weer terug.<br>We are renewing our website · Wir erneuern unsere Website</p>
<p style="margin-top:22px"><a href="tel:+31541513415" style="color:#302E80">+31 541 513 415</a> · <a href="mailto:info@poorthuis.com" style="color:#302E80">info@poorthuis.com</a></p>
</main><div class="streep"></div></body></html>
	<?php
	exit;
}, 0 );

/* ------------------------------------------------------------------ inrichten */

add_action( 'rest_api_init', function () {
	register_rest_route( 'poorthuis/v1', '/inrichten', [
		'methods'             => 'POST',
		'permission_callback' => fn() => current_user_can( 'manage_options' ),
		'callback'            => function () {
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
			return new WP_REST_Response( [ 'log' => poorthuis_inrichten( true ) ], 200 );
		},
	] );
} );

/** Patrooninhoud met geneste patronen uitgeklapt, zodat alles bewerkbare blokken zijn. */
function poorthuis_patroon( string $slug ): string {
	$p = WP_Block_Patterns_Registry::get_instance()->get_registered( $slug );
	if ( ! $p ) {
		return '';
	}
	return preg_replace_callback( '/<!-- wp:pattern \{"slug":"([^"]+)"\} \/-->/', fn( $m ) => poorthuis_patroon( $m[1] ), $p['content'] );
}

/** Afbeelding uit het thema in de mediabibliotheek zetten (één keer). */
function poorthuis_thema_media( string $file, string $title ): int {
	$existing = get_posts( [ 'post_type' => 'attachment', 'meta_key' => '_poorthuis_seed', 'meta_value' => $file, 'fields' => 'ids', 'numberposts' => 1, 'post_status' => 'any' ] );
	if ( $existing ) {
		return (int) $existing[0];
	}
	$tmp = wp_tempnam( basename( $file ) );
	copy( get_theme_file_path( 'assets/images/' . $file ), $tmp );
	$id = media_handle_sideload( [ 'name' => basename( $file ), 'tmp_name' => $tmp ], 0, $title );
	if ( is_wp_error( $id ) ) {
		return 0;
	}
	update_post_meta( $id, '_poorthuis_seed', $file );
	return $id;
}

/**
 * Alles klaarzetten. $instellingen = true zet ook Polylang, homepage, permalinks en
 * formulierontvanger (alleen bij de eerste inrichting nodig).
 */
function poorthuis_inrichten( bool $instellingen = false ): array {
	$log = [];
	$pll = function_exists( 'pll_set_post_language' );

	// ---- talen en Polylang-instellingen
	if ( $pll && $instellingen ) {
		$model = PLL()->model;
		$have  = wp_list_pluck( $model->get_languages_list(), 'slug' );
		$add   = fn( $args ) => isset( $model->languages ) ? $model->languages->add( $args ) : $model->add_language( $args );
		$want  = [
			[ 'name' => 'Nederlands', 'slug' => 'nl', 'locale' => 'nl_NL', 'rtl' => false, 'term_group' => 0, 'flag' => 'nl' ],
			[ 'name' => 'English', 'slug' => 'en', 'locale' => 'en_GB', 'rtl' => false, 'term_group' => 1, 'flag' => 'gb' ],
			[ 'name' => 'Deutsch', 'slug' => 'de', 'locale' => 'de_DE', 'rtl' => false, 'term_group' => 2, 'flag' => 'de' ],
		];
		foreach ( $want as $l ) {
			if ( ! in_array( $l['slug'], $have, true ) ) {
				$add( $l );
				$log[] = 'taal toegevoegd: ' . $l['slug'];
			}
		}
		$settings = [
			'default_lang'  => 'nl',
			'force_lang'    => 1,              // taal uit de map: /en/…, /de/…
			'rewrite'       => 1,              // zonder /language/ in het adres
			'hide_default'  => 1,              // Nederlands zonder /nl/
			'browser'       => 0,              // niet doorsturen op browsertaal
			'redirect_lang' => 1,              // homepage van een taal op /en/ en /de/
			'post_types'    => [ 'vacature' ], // projecten blijven gedeeld tussen de talen
			'taxonomies'    => [],
		];
		$options = PLL()->options;
		if ( $options instanceof ArrayAccess ) {
			foreach ( $settings as $k => $v ) {
				$options[ $k ] = $v;
			}
			if ( method_exists( $options, 'save' ) ) {
				$options->save();
			}
		} else {
			update_option( 'polylang', array_merge( (array) get_option( 'polylang', [] ), $settings ) );
		}
		$model->clean_languages_cache();
		$log[] = 'Polylang ingesteld';
	}

	// ---- pagina's: [nl-slug => [titel, patroon]] en per taal [slug, titel]
	$pages = [
		'home'              => [ 'Home', 'pagina-home', [ 'en' => [ 'home-en', 'Home' ], 'de' => [ 'home-de', 'Startseite' ] ] ],
		'over-ons'          => [ 'Over ons', 'pagina-over-ons', [ 'en' => [ 'about-us', 'About us' ], 'de' => [ 'ueber-uns', 'Über uns' ] ] ],
		'onze-diensten'     => [ 'Onze diensten', 'pagina-diensten', [ 'en' => [ 'services', 'Our services' ], 'de' => [ 'leistungen', 'Unsere Leistungen' ] ] ],
		'werken-bij'        => [ 'Werken bij', 'pagina-werken-bij', [ 'en' => [ 'careers', 'Careers' ], 'de' => [ 'karriere', 'Karriere' ] ] ],
		'contact'           => [ 'Contact', 'pagina-contact', [ 'en' => [ 'contact-us', 'Contact' ], 'de' => [ 'kontakt', 'Kontakt' ] ] ],
		'privacyverklaring' => [ 'Privacyverklaring', 'pagina-privacy', [ 'en' => [ 'privacy-statement', 'Privacy statement' ], 'de' => [ 'datenschutz', 'Datenschutzerklärung' ] ] ],
	];
	$status = getenv( 'POORTHUIS_SEED_STATUS' ) ?: 'publish';
	$maak   = function ( string $slug, string $title, string $pattern ) use ( $status ): int {
		$id = wp_insert_post( [
			'post_type'    => 'page',
			'post_status'  => $status,
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => poorthuis_patroon( 'poorthuis/' . $pattern ),
		] );
		update_post_meta( $id, '_wp_page_template', 'page-no-title' );
		return (int) $id;
	};
	$ids = [];
	foreach ( $pages as $slug => [ $title, $pattern, $vert ] ) {
		$found = get_page_by_path( $slug );
		if ( $found && ( ! $pll || in_array( pll_get_post_language( $found->ID ), [ false, 'nl' ], true ) ) ) {
			$ids[ $slug ] = $found->ID;
		} else {
			$ids[ $slug ] = $maak( $slug, $title, $pattern );
			$log[]        = "pagina gemaakt: $slug";
		}
		if ( ! $pll ) {
			continue;
		}
		pll_set_post_language( $ids[ $slug ], 'nl' );
		$trans = [ 'nl' => $ids[ $slug ] ];
		foreach ( $vert as $lang => [ $tslug, $ttitle ] ) {
			$tid = pll_get_post( $ids[ $slug ], $lang );
			if ( ! $tid ) {
				$tid = $maak( $tslug, $ttitle, $pattern . '-' . $lang );
				pll_set_post_language( $tid, $lang );
				$log[] = "pagina gemaakt ($lang): $tslug";
			}
			$trans[ $lang ] = $tid;
		}
		pll_save_post_translations( $trans );
	}

	// ---- projecten (de onsite-projecten uit de bedrijfspresentatie 2024)
	$projects = [
		[ 'Energiecentrale Megalopolis', 'Megalopolis', 'GR', 37.40, 22.14, 'projecten/megalopolis.webp', 'Energieproductie' ],
		[ 'Energie- en ontziltingscentrale Sohar', 'Sohar', 'OM', 24.35, 56.71, 'projecten/sohar.webp', 'Energieproductie' ],
		[ 'Power Plant 10 (PP10)', 'Riyad', 'SA', 24.63, 46.72, 'projecten/riyad-pp10.webp', 'Energieproductie' ],
		[ 'Energiecentrale Kozienice', 'Kozienice', 'PL', 51.58, 21.55, 'projecten/kozienice.webp', 'Energieproductie' ],
		[ 'Energiecentrale Hamm', 'Hamm (Westfalen)', 'DE', 51.68, 7.82, 'projecten/hamm.webp', 'Energieproductie' ],
		[ 'Centrale Merwedekanaal, unit 12', 'Utrecht', 'NL', 52.08, 5.10, 'projecten/utrecht-mk12.webp', 'Stadsverwarming' ],
		[ 'Energiecentrale Riviera Beach', 'Riviera Beach, Florida', 'US', 26.78, -80.06, 'projecten/riviera-beach.webp', 'Energieproductie' ],
	];
	foreach ( $projects as $i => [ $name, $city, $cc, $lat, $lng, $img, $sector ] ) {
		if ( get_posts( [ 'post_type' => 'project', 'title' => $name, 'post_status' => 'any', 'fields' => 'ids', 'numberposts' => 1 ] ) ) {
			continue;
		}
		$id = wp_insert_post( [ 'post_type' => 'project', 'post_status' => 'publish', 'post_title' => $name, 'menu_order' => $i ] );
		foreach ( [ 'plaats' => $city, 'land' => $cc, 'lat' => $lat, 'lng' => $lng, 'sector' => $sector ] as $k => $v ) {
			update_post_meta( $id, $k, $v );
		}
		if ( $media = poorthuis_thema_media( $img, $name ) ) {
			set_post_thumbnail( $id, $media );
		}
		$log[] = "project gemaakt: $name";
	}

	// ---- stagevacature in drie talen
	$vac = [
		'nl' => [ 'stagiair-techniek', 'Stagiair techniek', 'Een technische stage waarin je méér doet dan alleen meekijken.', 'Stage', 'Oldenzaal + op locatie' ],
		'en' => [ 'technical-intern', 'Technical intern', 'A technical internship where you do more than just look over someone’s shoulder.', 'Internship', 'Oldenzaal + on site' ],
		'de' => [ 'praktikant-technik', 'Praktikant Technik', 'Ein technisches Praktikum, in dem du mehr tust als nur zuzuschauen.', 'Praktikum', 'Oldenzaal + vor Ort' ],
	];
	$vtrans = [];
	foreach ( $vac as $lang => [ $slug, $title, $excerpt, $soort, $plaats ] ) {
		$found = get_page_by_path( $slug, OBJECT, 'vacature' );
		$id    = $found ? $found->ID : 0;
		if ( ! $id ) {
			$id = wp_insert_post( [
				'post_type'    => 'vacature',
				'post_status'  => 'publish',
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_excerpt' => $excerpt,
				'post_content' => poorthuis_patroon( 'poorthuis/vacature-stage' . ( 'nl' === $lang ? '' : '-' . $lang ) ),
			] );
			update_post_meta( $id, 'dienstverband', $soort );
			update_post_meta( $id, 'locatie', $plaats );
			$log[] = "vacature gemaakt ($lang): $title";
		}
		if ( $pll ) {
			pll_set_post_language( $id, $lang );
		}
		$vtrans[ $lang ] = $id;
		if ( ! $pll ) {
			break; // zonder Polylang alleen de Nederlandse
		}
	}
	if ( $pll ) {
		pll_save_post_translations( $vtrans );
	}

	// ---- instellingen
	if ( $instellingen ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
		update_option( 'wp_page_for_privacy_policy', $ids['privacyverklaring'] );
		update_option( 'blogname', 'Poorthuis Industrial Services' );
		update_option( 'blogdescription', 'Solutions by Specialists' );
		update_option( 'permalink_structure', '/%postname%/' );
		foreach ( [ [ 'sample-page', 'page' ], [ 'hello-world', 'post' ] ] as [ $s, $t ] ) {
			if ( $old = get_page_by_path( $s, OBJECT, $t ) ) {
				wp_delete_post( $old->ID, true );
			}
		}
		flush_rewrite_rules();
		$log[] = 'instellingen gezet (homepage, privacypagina, permalinks)';
	}
	return $log;
}
