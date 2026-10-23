/* Zijpaneel "Gegevens" voor projecten en vacatures (zonder bouwstap). */
( function ( wp ) {
	const { registerPlugin } = wp.plugins;
	const { PluginDocumentSettingPanel } = wp.editor;
	const { TextControl } = wp.components;
	const { useSelect } = wp.data;
	const { useEntityProp } = wp.coreData;
	const el = wp.element.createElement;

	const FIELDS = {
		project: [
			[ 'plaats', 'Plaats', 'Bijv. Megalopolis' ],
			[ 'land', 'Landcode', 'Twee letters, bijv. GR' ],
			[ 'sector', 'Sector', 'Bijv. Energieproductie' ],
			[ 'lat', 'Breedtegraad', 'Bijv. 37.40 (positief = noord)', 'number' ],
			[ 'lng', 'Lengtegraad', 'Bijv. 22.14 (positief = oost)', 'number' ],
		],
		vacature: [
			[ 'dienstverband', 'Dienstverband', 'Bijv. Fulltime, Parttime, Stage' ],
			[ 'locatie', 'Locatie', 'Bijv. Oldenzaal + op locatie' ],
		],
	};

	function Panel() {
		const type = useSelect( ( s ) => s( 'core/editor' ).getCurrentPostType(), [] );
		const [ meta, setMeta ] = useEntityProp( 'postType', type, 'meta' );
		const fields = FIELDS[ type ];
		if ( ! fields || ! meta ) return null;
		return el(
			PluginDocumentSettingPanel,
			{ name: 'poorthuis-gegevens', title: 'Gegevens', initialOpen: true },
			fields.map( ( [ key, label, help, kind ] ) =>
				el( TextControl, {
					key,
					label,
					help,
					type: kind || 'text',
					step: kind === 'number' ? 'any' : undefined,
					value: meta[ key ] ?? '',
					onChange: ( v ) => setMeta( { ...meta, [ key ]: kind === 'number' ? ( v === '' ? 0 : parseFloat( v ) ) : v } ),
					__nextHasNoMarginBottom: true,
					__next40pxDefaultSize: true,
				} )
			),
			type === 'project' &&
				el( 'p', { style: { color: '#757575', fontSize: 12 } }, 'De uitgelichte afbeelding verschijnt in het kaartje op de wereldbol.' )
		);
	}

	registerPlugin( 'poorthuis-gegevens', { render: Panel } );
} )( window.wp );
