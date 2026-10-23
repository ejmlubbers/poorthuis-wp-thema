/* Kop: op de homepage doorzichtig over de hero, wit zodra je scrolt. */
( function () {
	const header = document.querySelector( '.ph-header' );
	if ( ! header || ! document.body.classList.contains( 'home' ) ) return;
	header.classList.add( 'is-overlay' );
	const update = () => header.classList.toggle( 'is-scrolled', window.scrollY > 40 );
	update();
	window.addEventListener( 'scroll', update, { passive: true } );
} )();
