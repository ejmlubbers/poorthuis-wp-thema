( function ( wp ) {
	// In de editor alleen een plaatsaanduiding; op de site toont render.php de vlaggen.
	wp.blocks.registerBlockType( 'poorthuis/taalkeuze', {
		edit: function () {
			return wp.element.createElement( 'span', wp.blockEditor.useBlockProps( { style: { fontSize: '13px', opacity: 0.7 } } ), '🌐 Taal ▾' );
		},
		save: function () { return null; },
	} );
} )( window.wp );
