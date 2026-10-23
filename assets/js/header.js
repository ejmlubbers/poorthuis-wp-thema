/* Kop: op de homepage doorzichtig over de hero, wit zodra je scrolt.
   Voet: op mobiel zijn Menu en Informatie uitklapbaar. */
( function () {
	const header = document.querySelector( '.ph-header' );
	if ( ! header || ! document.body.classList.contains( 'home' ) ) return;
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
