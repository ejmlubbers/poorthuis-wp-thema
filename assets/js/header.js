/* Kop: doorzichtig over een donkere eerste sectie (body.ph-dark-top), wit zodra je scrolt.
   Voet: op mobiel zijn Menu en Informatie uitklapbaar. */
( function () {
	const header = document.querySelector( '.ph-header' );
	if ( ! header || ! document.body.classList.contains( 'ph-dark-top' ) ) return;
	header.classList.add( 'is-overlay' );
	const update = () => header.classList.toggle( 'is-scrolled', window.scrollY > 40 );
	update();
	window.addEventListener( 'scroll', update, { passive: true } );
} )();

( function () {
	document.querySelectorAll( '.ph-footer .ph-foot-head' ).forEach( ( head ) => {
		const col = head.closest( '.wp-block-column' );
		const btn = document.createElement( 'button' );
		btn.type = 'button';
		btn.className = 'ph-foot-toggle';
		btn.textContent = head.textContent;
		btn.setAttribute( 'aria-expanded', 'false' );
		head.replaceChildren( btn );
		btn.addEventListener( 'click', () => {
			if ( window.matchMedia( '(min-width: 782px)' ).matches ) return;
			const open = col.dataset.open !== 'true';
			col.dataset.open = open;
			btn.setAttribute( 'aria-expanded', open );
		} );
	} );
} )();

/* Taalkeuze: dichtklappen bij een klik ernaast of met Escape. */
( function () {
	const close = () => document.querySelectorAll( '.ph-lang[open]' ).forEach( ( d ) => d.removeAttribute( 'open' ) );
	document.addEventListener( 'click', ( e ) => { if ( ! e.target.closest( '.ph-lang' ) ) close(); } );
	document.addEventListener( 'keydown', ( e ) => { if ( 'Escape' === e.key ) close(); } );
} )();

/* Mobiel menu: <dialog> met logo, grote links en een klein voetmenu.
   showModal() zet hem in de top layer (boven alles, focus blijft erin, Escape sluit). */
( function () {
	const dlg = document.getElementById( 'ph-mnav' );
	const open = document.querySelector( '.ph-mnav-open' );
	if ( ! dlg || ! open || typeof dlg.showModal !== 'function' ) return;
	const here = location.pathname.replace( /\/+$/, '' ) || '/';
	dlg.querySelectorAll( '.ph-mnav-main a' ).forEach( ( a ) => {
		if ( ( new URL( a.href ).pathname.replace( /\/+$/, '' ) || '/' ) === here ) a.setAttribute( 'aria-current', 'page' );
	} );
	open.addEventListener( 'click', () => { dlg.showModal(); open.setAttribute( 'aria-expanded', 'true' ); } );
	dlg.addEventListener( 'close', () => open.setAttribute( 'aria-expanded', 'false' ) );
	dlg.querySelector( '.ph-mnav-close' ).addEventListener( 'click', () => dlg.close() );
	// sluiten na een klik op een link (ook #-links op dezelfde pagina en cookie-instellingen)
	dlg.addEventListener( 'click', ( e ) => { if ( e.target.closest( 'a' ) ) dlg.close(); } );
	// terug naar desktopbreedte met het menu open: dichtdoen
	window.matchMedia( '(min-width: 782px)' ).addEventListener( 'change', ( m ) => { if ( m.matches ) dlg.close(); } );
} )();
