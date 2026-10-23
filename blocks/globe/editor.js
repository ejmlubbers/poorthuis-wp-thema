/* In de editor een rustige plaatshouder; de bol zelf draait alleen op de site. */
( function ( wp ) {
	const el = wp.element.createElement;
	wp.blocks.registerBlockType( 'poorthuis/globe', {
		edit: function () {
			return el(
				'div',
				wp.blockEditor.useBlockProps( { className: 'ph-globe-placeholder' } ),
				el( 'strong', null, 'Wereldbol' ),
				el( 'span', null, 'Toont de projecten uit het menu Projecten. Bekijk de pagina om hem te laten draaien.' )
			);
		},
		save: () => null,
	} );
} )( window.wp );
