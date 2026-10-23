/* Editor: keuze voor de variant; voorbeeld via de PHP-render. */
( function ( wp ) {
	const el = wp.element.createElement;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, SelectControl, TextControl, Disabled } = wp.components;
	const ServerSideRender = wp.serverSideRender;

	wp.blocks.registerBlockType( 'poorthuis/contact-form', {
		edit: function ( { attributes, setAttributes } ) {
			return el(
				'div',
				useBlockProps(),
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: 'Formulier' },
						el( SelectControl, {
							label: 'Soort',
							value: attributes.variant,
							options: [
								{ value: 'kort', label: 'Kort (naam, e-mail, bericht)' },
								{ value: 'volledig', label: 'Contact (met bedrijf en telefoon)' },
								{ value: 'stage', label: 'Stage-aanmelding (met cv)' },
								{ value: 'sollicitatie', label: 'Sollicitatie (met cv)' },
							],
							onChange: ( variant ) => setAttributes( { variant } ),
							__nextHasNoMarginBottom: true,
						} ),
						el( TextControl, {
							label: 'Onderwerp van de e-mail',
							help: 'Leeg = automatisch op basis van de soort.',
							value: attributes.subject,
							onChange: ( subject ) => setAttributes( { subject } ),
							__nextHasNoMarginBottom: true,
						} ),
						el( TextControl, {
							label: 'Tekst op de knop',
							value: attributes.button,
							onChange: ( button ) => setAttributes( { button } ),
							__nextHasNoMarginBottom: true,
						} )
					)
				),
				el( Disabled, null, el( ServerSideRender, { block: 'poorthuis/contact-form', attributes } ) )
			);
		},
		save: () => null,
	} );
} )( window.wp );
