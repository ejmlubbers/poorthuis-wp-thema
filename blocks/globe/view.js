/* Wereldbol — stippenbol in de geest van Shopify Live View, zonder bibliotheken.
   Landstippen: een Fibonacci-verdeling van 90.000 punten over de bol; welke op
   land liggen staat als bitset in assets/data/land-90000.bin (11 kB, vooraf
   berekend met de Natural Earth 110m-kaart). */
( function () {
	const N = 90000;
	const GOLDEN = Math.PI * ( 3 - Math.sqrt( 5 ) );

	function toXYZ( lat, lng ) {
		const a = ( lat * Math.PI ) / 180, b = ( lng * Math.PI ) / 180;
		return [ Math.cos( a ) * Math.sin( b ), Math.sin( a ), Math.cos( a ) * Math.cos( b ) ];
	}
	function slerp( p, q, om, f ) {
		if ( om < 1e-6 ) return p;
		const s = Math.sin( om ), a = Math.sin( ( 1 - f ) * om ) / s, b = Math.sin( f * om ) / s;
		return [ p[ 0 ] * a + q[ 0 ] * b, p[ 1 ] * a + q[ 1 ] * b, p[ 2 ] * a + q[ 2 ] * b ];
	}

	async function landDots( url ) {
		const bits = new Uint8Array( await ( await fetch( url ) ).arrayBuffer() );
		const out = [];
		for ( let i = 0; i < N; i++ ) {
			if ( ! ( bits[ i >> 3 ] & ( 1 << ( i & 7 ) ) ) ) continue;
			const y = 1 - ( i / ( N - 1 ) ) * 2, r = Math.sqrt( 1 - y * y ), th = GOLDEN * i;
			out.push( r * Math.sin( th ), y, r * Math.cos( th ) ); // zelfde conventie als toXYZ
		}
		return new Float32Array( out );
	}

	function init( root ) {
		const data = JSON.parse( root.dataset.globe );
		const projects = data.projects, home = data.home;
		const wrap = root.querySelector( '.ph-globe' );
		const canvas = wrap.querySelector( 'canvas' );
		const ctx = canvas.getContext( '2d' );
		const reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		let size = { w: 600, h: 600 }, dots = null;
		const rot = { lng: 2, lat: 58 };        // start: Europa links onder in beeld
		const vel = { x: 0, y: 0 };
		let drag = null, target = null, lastInteract = 0;
		let pinned = null, hover = null;

		// markers en kaartje
		const mk = ( cls, html ) => { const e = document.createElement( cls === 'card' ? 'div' : 'span' ); e.className = 'ph-globe-' + cls; if ( html ) e.innerHTML = html; wrap.appendChild( e ); return e; };
		const homeEl = mk( 'home', '<i></i><em></em>' );
		homeEl.querySelector( 'em' ).textContent = home.name;
		const hsEls = projects.map( ( p ) => {
			const b = document.createElement( 'button' );
			b.type = 'button';
			b.className = 'ph-globe-hs';
			b.setAttribute( 'aria-label', p.name + ', ' + p.city );
			b.innerHTML = '<span></span>';
			b.addEventListener( 'mouseenter', () => { hover = p; draw(); } );
			b.addEventListener( 'mouseleave', () => { hover = null; draw(); } );
			b.addEventListener( 'focus', () => { hover = p; draw(); } );
			b.addEventListener( 'blur', () => { hover = null; draw(); } );
			b.addEventListener( 'click', () => open( p ) );
			wrap.appendChild( b );
			return b;
		} );
		const card = mk( 'card' );
		const shown = () => hover || pinned;
		let cardFor = null;
		function fillCard( p ) {
			if ( cardFor === p ) return;
			cardFor = p;
			card.innerHTML = '';
			if ( ! p ) return;
			if ( p.img ) { const im = new Image(); im.src = p.img; im.alt = ''; card.appendChild( im ); }
			const t = document.createElement( 'div' );
			const b = document.createElement( 'b' ); b.textContent = p.city + ', ' + p.country;
			const s = document.createElement( 'span' ); s.textContent = p.name;
			t.append( b, s ); card.appendChild( t );
		}

		function open( p, fromList ) {
			lastInteract = performance.now();
			if ( ! fromList && pinned === p ) { pinned = null; draw(); return; }
			pinned = p;
			// de bol valt rechtsboven buiten beeld: zet het project links onder het midden
			target = { lng: -p.lng - 28, lat: Math.min( 75, p.lat + 22 ) };
			hsEls.forEach( ( el, i ) => el.classList.toggle( 'on', projects[ i ] === p ) );
		}

		// projectenlijst onder de teller
		const countBtn = root.querySelector( '.ph-globe-count' );
		const list = root.querySelector( '.ph-globe-list' );
		const setList = ( on ) => { list.hidden = ! on; countBtn.setAttribute( 'aria-expanded', on ); };
		countBtn.addEventListener( 'click', () => setList( list.hidden ) );
		list.addEventListener( 'click', ( e ) => {
			const b = e.target.closest( 'button[data-id]' );
			if ( ! b ) return;
			const p = projects.find( ( x ) => String( x.id ) === b.dataset.id );
			if ( p ) open( p, true );
			setList( false );
		} );
		document.addEventListener( 'pointerdown', ( e ) => { if ( ! list.hidden && ! root.querySelector( '.ph-globe-list-wrap' ).contains( e.target ) ) setList( false ); } );
		document.addEventListener( 'keydown', ( e ) => { if ( e.key === 'Escape' ) setList( false ); } );

		// formaat
		new ResizeObserver( ( [ e ] ) => {
			const { width, height } = e.contentRect;
			size = { w: Math.round( width ), h: Math.round( height ) };
			const dpr = Math.min( window.devicePixelRatio || 1, 2 );
			canvas.width = width * dpr; canvas.height = height * dpr;
			canvas.style.width = width + 'px'; canvas.style.height = height + 'px';
			draw();
		} ).observe( wrap );

		function rotate( v ) {
			const a = ( rot.lng * Math.PI ) / 180, b = ( rot.lat * Math.PI ) / 180;
			const ca = Math.cos( a ), sa = Math.sin( a ), cb = Math.cos( b ), sb = Math.sin( b );
			const x1 = v[ 0 ] * ca + v[ 2 ] * sa, z1 = -v[ 0 ] * sa + v[ 2 ] * ca, y1 = v[ 1 ];
			return [ x1, y1 * cb - z1 * sb, y1 * sb + z1 * cb ];
		}
		const geom = () => { const R = Math.min( size.w, size.h ) * 0.42; return { R, cx: size.w / 2, cy: size.h / 2 }; };

		function step( now ) {
			if ( target ) {
				const dl = ( ( target.lng - rot.lng + 540 ) % 360 ) - 180;
				rot.lng += dl * 0.09; rot.lat += ( target.lat - rot.lat ) * 0.09;
				if ( Math.abs( dl ) < 0.05 && Math.abs( target.lat - rot.lat ) < 0.05 ) target = null;
			} else if ( ! drag ) {
				rot.lng += vel.x; rot.lat += vel.y;
				vel.x *= 0.94; vel.y *= 0.94;
				if ( ! reduce && ! shown() && now - lastInteract > 3500 ) rot.lng += 0.06;
			}
			rot.lat = Math.max( -75, Math.min( 75, rot.lat ) );
		}

		function draw() {
			const { w, h } = size, { R, cx, cy } = geom();
			const dpr = canvas.width / Math.max( 1, w );
			ctx.setTransform( dpr, 0, 0, dpr, 0, 0 );
			ctx.clearRect( 0, 0, w, h );

			// lichte schaduw net buiten de bol, zodat de rand leest
			const shadow = ctx.createRadialGradient( cx, cy, R * 0.9, cx, cy, R * 1.2 );
			shadow.addColorStop( 0, 'rgba(8,6,36,0.26)' ); shadow.addColorStop( 0.45, 'rgba(8,6,36,0.12)' ); shadow.addColorStop( 1, 'rgba(8,6,36,0)' );
			ctx.fillStyle = shadow; ctx.beginPath(); ctx.arc( cx, cy, R * 1.2, 0, 6.283 ); ctx.fill();
			// bol: doorlopend verloop, alleen binnen de rand
			const atmo = ctx.createRadialGradient( cx, cy, 0, cx, cy, R );
			atmo.addColorStop( 0, 'rgba(130,142,240,0.18)' ); atmo.addColorStop( 0.6, 'rgba(130,142,240,0.13)' );
			atmo.addColorStop( 0.92, 'rgba(145,158,250,0.12)' ); atmo.addColorStop( 1, 'rgba(150,165,255,0.06)' );
			ctx.fillStyle = atmo; ctx.beginPath(); ctx.arc( cx, cy, R, 0, 6.283 ); ctx.fill();
			const shine = ctx.createRadialGradient( cx - R * 0.4, cy - R * 0.45, 0, cx - R * 0.2, cy - R * 0.2, R * 1.05 );
			shine.addColorStop( 0, 'rgba(170,180,255,0.10)' ); shine.addColorStop( 1, 'rgba(170,180,255,0)' );
			ctx.fillStyle = shine; ctx.beginPath(); ctx.arc( cx, cy, R, 0, 6.283 ); ctx.fill();

			// landstippen: helderheid en grootte lopen vloeiend af naar de rand
			if ( dots ) {
				const NB = 24, bands = Array.from( { length: NB }, () => [] );
				for ( let i = 0; i < dots.length; i += 3 ) {
					const p = rotate( [ dots[ i ], dots[ i + 1 ], dots[ i + 2 ] ] );
					if ( p[ 2 ] <= 0 ) continue;
					bands[ Math.min( NB - 1, Math.floor( p[ 2 ] * NB ) ) ].push( cx + p[ 0 ] * R, cy - p[ 1 ] * R );
				}
				const rad = Math.max( 0.55, R / 520 );
				for ( let b = 0; b < NB; b++ ) {
					const arr = bands[ b ];
					if ( ! arr.length ) continue;
					const e = Math.pow( ( b + 0.5 ) / NB, 0.6 ), rr = rad * ( 0.7 + 0.45 * e );
					ctx.fillStyle = 'rgba(206,214,255,' + ( 0.08 + 0.8 * e ).toFixed( 3 ) + ')';
					ctx.beginPath();
					for ( let k = 0; k < arr.length; k += 2 ) { ctx.moveTo( arr[ k ] + rr, arr[ k + 1 ] ); ctx.arc( arr[ k ], arr[ k + 1 ], rr, 0, 6.283 ); }
					ctx.fill();
				}
			}

			// lijnen vanuit Oldenzaal, met een klein boogje boven het oppervlak
			const hv = toXYZ( home.lat, home.lng ), sel = shown();
			ctx.lineWidth = 1.2;
			projects.forEach( ( p ) => {
				const to = toXYZ( p.lat, p.lng );
				const om = Math.acos( Math.max( -1, Math.min( 1, hv[ 0 ] * to[ 0 ] + hv[ 1 ] * to[ 1 ] + hv[ 2 ] * to[ 2 ] ) ) );
				ctx.strokeStyle = sel === p ? 'rgba(241,201,68,0.95)' : 'rgba(241,201,68,0.38)';
				ctx.beginPath();
				let pen = false;
				for ( let s = 0; s <= 48; s++ ) {
					const f = s / 48, v = slerp( hv, to, om, f );
					const lift = 1 + Math.sin( Math.PI * f ) * Math.min( 0.18, om * 0.12 );
					const q = rotate( [ v[ 0 ] * lift, v[ 1 ] * lift, v[ 2 ] * lift ] );
					if ( ! ( q[ 2 ] > 0 || Math.hypot( q[ 0 ], q[ 1 ] ) > 1 ) ) { pen = false; continue; }
					const X = cx + q[ 0 ] * R, Y = cy - q[ 1 ] * R;
					if ( ! pen ) { ctx.moveTo( X, Y ); pen = true; } else ctx.lineTo( X, Y );
				}
				ctx.stroke();
			} );

			// DOM-markers meebewegen
			const place = ( el, lat, lng ) => {
				const q = rotate( toXYZ( lat, lng ) ), vis = q[ 2 ] > 0.08;
				const X = cx + q[ 0 ] * R, Y = cy - q[ 1 ] * R;
				el.style.transform = 'translate(' + X.toFixed( 1 ) + 'px,' + Y.toFixed( 1 ) + 'px) translate(-50%,-50%) scale(' + ( 0.7 + 0.3 * q[ 2 ] ).toFixed( 3 ) + ')';
				el.style.opacity = vis ? Math.min( 1, ( q[ 2 ] - 0.08 ) * 6 ).toFixed( 2 ) : 0;
				el.style.pointerEvents = vis ? 'auto' : 'none';
				return { X, Y, vis };
			};
			place( homeEl, home.lat, home.lng );
			let act = null;
			projects.forEach( ( p, i ) => { const pos = place( hsEls[ i ], p.lat, p.lng ); if ( sel === p ) act = pos; } );
			fillCard( sel );
			if ( act && act.vis ) {
				const left = act.X > w * 0.55;
				card.style.opacity = 1;
				card.style.transform = 'translate(' + ( left ? act.X - 18 : act.X + 18 ).toFixed( 1 ) + 'px,' + ( act.Y - 20 ).toFixed( 1 ) + 'px) translate(' + ( left ? '-100%' : '0' ) + ',-100%)';
			} else card.style.opacity = 0;
		}

		// slepen
		wrap.addEventListener( 'pointerdown', ( e ) => {
			if ( e.target.closest( 'button' ) ) return;
			drag = { x: e.clientX, y: e.clientY }; target = null; vel.x = vel.y = 0;
			lastInteract = performance.now();
			wrap.setPointerCapture( e.pointerId );
		} );
		wrap.addEventListener( 'pointermove', ( e ) => {
			if ( ! drag ) return;
			const k = ( 180 / ( Math.PI * geom().R ) ) * 1.1;
			const dx = ( e.clientX - drag.x ) * k, dy = ( e.clientY - drag.y ) * k;
			rot.lng += dx; rot.lat += dy;
			const cap = ( v ) => Math.max( -2.5, Math.min( 2.5, v * 0.6 ) );
			vel.x = cap( dx ); vel.y = cap( dy );
			drag.x = e.clientX; drag.y = e.clientY;
			lastInteract = performance.now();
		} );
		const up = () => { drag = null; lastInteract = performance.now(); };
		wrap.addEventListener( 'pointerup', up );
		wrap.addEventListener( 'pointercancel', up );

		// alleen animeren als de bol in beeld is
		let raf = 0, visible = false;
		const loop = ( now ) => { step( now ); draw(); raf = visible ? requestAnimationFrame( loop ) : 0; };
		new IntersectionObserver( ( [ e ] ) => {
			visible = e.isIntersecting;
			if ( visible && ! raf ) raf = requestAnimationFrame( loop );
		} ).observe( root );

		landDots( data.land ).then( ( d ) => { dots = d; draw(); } );
	}

	document.querySelectorAll( '[data-globe]' ).forEach( init );
} )();
