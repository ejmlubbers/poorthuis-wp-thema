( function () {
	document.querySelectorAll( '.ph-video[data-youtube]' ).forEach( ( box ) => {
		box.querySelector( '.ph-video-btn' ).addEventListener( 'click', () => {
			const f = document.createElement( 'iframe' );
			f.src = 'https://www.youtube-nocookie.com/embed/' + box.dataset.youtube + '?autoplay=1&rel=0';
			f.title = box.querySelector( '.ph-video-btn' ).getAttribute( 'aria-label' );
			f.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
			f.allowFullscreen = true;
			box.replaceChildren( f );
		} );
	} );
} )();
