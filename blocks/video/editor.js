( function ( wp ) {
	const el = wp.element.createElement;
	const { InspectorControls, MediaUpload, MediaUploadCheck, useBlockProps } = wp.blockEditor;
	const { PanelBody, TextControl, Button } = wp.components;

	// accepteert een volledige YouTube-link of alleen de id
	const toId = ( v ) => {
		const m = String( v ).match( /(?:youtu\.be\/|v=|embed\/|shorts\/)([\w-]{11})/ );
		return m ? m[ 1 ] : String( v ).trim();
	};

	wp.blocks.registerBlockType( 'poorthuis/video', {
		edit: function ( { attributes: a, setAttributes } ) {
			const poster = a.posterUrl || ( a.youtube ? 'https://i.ytimg.com/vi/' + a.youtube + '/hqdefault.jpg' : '' );
			return el(
				'div',
				useBlockProps( { className: 'ph-video' } ),
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: 'Video' },
						el( TextControl, { label: 'YouTube-link of id', value: a.youtube, onChange: ( v ) => setAttributes( { youtube: toId( v ) } ), __nextHasNoMarginBottom: true } ),
						el( TextControl, { label: 'Tekst onder de video', value: a.label, onChange: ( label ) => setAttributes( { label } ), __nextHasNoMarginBottom: true } ),
						el( MediaUploadCheck, null, el( MediaUpload, {
							allowedTypes: [ 'image' ],
							value: a.posterId,
							onSelect: ( m ) => setAttributes( { posterId: m.id, posterUrl: m.sizes?.large?.url || m.url } ),
							render: ( { open } ) => el( 'div', { style: { display: 'flex', gap: 8 } },
								el( Button, { variant: 'secondary', onClick: open }, a.posterUrl ? 'Andere voorvertoning' : 'Kies voorvertoning' ),
								a.posterUrl && el( Button, { variant: 'tertiary', isDestructive: true, onClick: () => setAttributes( { posterId: undefined, posterUrl: '' } ) }, 'Weghalen' )
							),
						} ) )
					)
				),
				el( 'div', { className: 'ph-video-frame' },
					el( 'div', { className: 'ph-video-btn' }, poster && el( 'img', { src: poster, alt: '' } ) )
				),
				el( 'span', { className: 'ph-video-caption' }, '▶ ' + ( a.label || 'Bekijk de bedrijfsfilm' ) )
			);
		},
		save: () => null,
	} );
} )( window.wp );
