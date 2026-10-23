( function () {
	document.querySelectorAll( '.ph-video[data-youtube]' ).forEach( ( box ) => {
		const frame = box.querySelector( '.ph-video-frame' );
		const btn = box.querySelector( '.ph-video-btn' );
		const play = () => {
			const f = document.createElement( 'iframe' );
			f.src = 'https://www.youtube-nocookie.com/embed/' + box.dataset.youtube + '?autoplay=1&rel=0';
			f.title = btn.getAttribute( 'aria-label' );
			f.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
			f.allowFullscreen = true;
			frame.replaceChildren( f );
			box.classList.add( 'is-playing' );
		};
		btn.addEventListener( 'click', play );
		box.querySelector( '.ph-video-caption' )?.addEventListener( 'click', play );
	} );
} )();
