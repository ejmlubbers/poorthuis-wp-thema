/* Cookiemelding: Google Analytics pas na toestemming. Keuze in cookie ph_consent (6 maanden). */
( function () {
	const box = document.querySelector( '.ph-cookie' );
	const ga = window.poorthuisCookies && window.poorthuisCookies.ga;
	if ( ! box || ! ga ) return;

	const get = () => ( document.cookie.match( /(?:^|; )ph_consent=(yes|no)/ ) || [] )[ 1 ];
	const set = ( v ) => { document.cookie = 'ph_consent=' + v + '; max-age=' + 60 * 60 * 24 * 182 + '; path=/; SameSite=Lax' + ( location.protocol === 'https:' ? '; Secure' : '' ); };

	let loaded = false;
	const loadGa = () => {
		if ( loaded ) return;
		loaded = true;
		const s = document.createElement( 'script' );
		s.async = true;
		s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent( ga );
		document.head.appendChild( s );
		window.dataLayer = window.dataLayer || [];
		window.gtag = function () { window.dataLayer.push( arguments ); };
		window.gtag( 'js', new Date() );
		window.gtag( 'config', ga );
	};
	// analytics-cookies van Google weghalen als iemand zijn toestemming intrekt
	const clearGa = () => {
		document.cookie.split( '; ' ).forEach( ( c ) => {
			const n = c.split( '=' )[ 0 ];
			if ( /^_ga/.test( n ) ) {
				[ location.hostname, '.' + location.hostname.replace( /^www\./, '' ) ].forEach( ( d ) => {
					document.cookie = n + '=; max-age=0; path=/; domain=' + d;
				} );
				document.cookie = n + '=; max-age=0; path=/';
			}
		} );
	};

	const show = () => { box.hidden = false; requestAnimationFrame( () => box.classList.add( 'is-open' ) ); };
	const hide = () => { box.classList.remove( 'is-open' ); setTimeout( () => { box.hidden = true; }, 250 ); };

	box.querySelector( '.ph-cookie-yes' ).addEventListener( 'click', () => { set( 'yes' ); loadGa(); hide(); } );
	box.querySelector( '.ph-cookie-no' ).addEventListener( 'click', () => { const had = get() === 'yes'; set( 'no' ); clearGa(); hide(); if ( had ) location.reload(); } );
	document.addEventListener( 'click', ( e ) => {
		if ( e.target.closest( '.ph-cookie-open' ) ) { e.preventDefault(); show(); }
	} );

	const v = get();
	if ( v === 'yes' ) loadGa();
	else if ( ! v ) show();
} )();
