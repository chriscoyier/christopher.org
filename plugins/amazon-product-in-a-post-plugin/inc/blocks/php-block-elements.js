// License: GPLv2+

var el = wp.element.createElement,
	registerBlockType = wp.blocks.registerBlockType,
	serverSideRender = wp.serverSideRender,
	InspectorControls = wp.blockEditor.InspectorControls,
	TextControl = wp.components.TextControl;
	var {__} = window.wp.i18n;

/* Here's where we register the block in JavaScript. */
registerBlockType( 'amazon-pip/amazon-elements', {
	title: __('Amazon Elements Block','amazon-product-in-a-post-plugin'),
	icon: el('svg', 
		{ width: 30, height: 30, viewBox: '0 0 360 360', color:'#555d66',x: "0px", y: "0px" }, 
		el('path', { d: "M195.9,89.7c-3.3,0.5-6.8,0.8-10.5,1.1c-3.7,0.2-7.6,0.6-11.7,1.1c-6.2,0.7-12.1,1.6-17.8,2.8c-5.7,1.1-11.1,2.8-16.1,4.9c-9.8,3.9-17.6,9.8-23.4,17.6c-5.8,7.8-8.8,17.5-8.8,29.2c0,14.1,4.2,24.7,12.7,31.8c8.5,7,19,10.6,31.6,10.6c7.9,0,15.1-0.7,21.6-2c4.9-1.4,9.6-3.4,14.2-6.2c4.6-2.8,9.3-6.4,14.2-11.1l7.3,8.4c1.9,2.1,5.3,5.2,10.2,9.1c3.1,1.1,5.7,1,7.8-0.3c3.1-2.6,6.9-5.6,11.6-9c4.6-3.4,8.4-6.4,11.3-9c1.1-0.9,1.7-2,1.7-3.3c0-1.3-0.5-2.5-1.4-3.7c-2.8-3.3-5.5-6.8-7.8-10.5c-2.3-3.7-3.5-8.9-3.5-15.7V77.1c0-4.4-0.4-8.6-1.1-12.7c-0.7-4-1.9-7.9-3.8-11.5c-1.9-3.7-4.6-7.1-8.2-10.2c-6.2-5.2-13.5-8.9-22-10.9c-8.5-2-16.4-3.1-23.8-3.1h-6.2c-6.7,0.5-13.4,1.5-20,3.1c-6.6,1.6-12.7,4-18.4,7.4c-5.6,3.3-10.5,7.6-14.6,12.9c-4.1,5.2-7,11.7-8.6,19.4c-0.4,1.9-0.1,3.2,1,4c1.1,0.8,2.2,1.3,3.4,1.6l29,3.7c1.4-0.5,2.5-1.2,3.3-2.2c0.8-1,1.3-2.1,1.6-3.3c1.3-5.6,4.1-9.8,8.4-12.6c4.3-2.8,9.1-4.4,14.4-4.9h2.3c3.1,0,6.1,0.6,9.2,1.8c3.1,1.2,5.6,3.2,7.5,5.9c2.1,2.8,3.2,6.1,3.3,9.8l0,0c0.1,3.7,0.2,7.3,0.2,10.7h0V89.7z M196,118.7c0,5.6-0.1,10.8-0.3,15.5c-0.2,4.8-2,9.7-5.4,14.8c-3.8,6.8-9.4,11-16.8,12.7c-0.5,0-1.1,0.1-1.8,0.3c-0.7,0.2-1.5,0.3-2.5,0.3c-5.8,0-10.3-1.9-13.6-5.7c-3.3-3.8-5-8.9-5-15.2c0-7.9,2.2-13.8,6.7-17.9c4.5-4,9.9-7,16.1-8.9l0,0c3.4-0.7,7.1-1.2,10.9-1.4c3.8-0.2,7.7-0.3,11.6-0.3V118.7z"}),
		el('path', { d: "M273.3,211.6c1.8-1,3.5-2.1,5.2-3.3l0,0c1.2-0.7,1.8-1.7,1.8-2.9c-0.1-0.6-0.3-1.2-0.6-1.7c-0.4-0.7-1.2-1.2-2.2-1.4c-1-0.2-2.1-0.1-3.1,0.3c-1.1,0.5-2.2,0.9-3.2,1.2c-1,0.3-2.1,0.7-3.1,1.1c-14.6,5.2-29.2,9.1-43.8,11.6c-14.6,2.5-29,3.8-43.1,3.8c-22.1,0-43.6-2.7-64.3-8.2c-20.7-5.5-39.6-13.1-56.8-22.8c-0.7-0.4-1.4-0.6-2-0.6c-0.7,0-1.3,0.3-1.8,0.9c-0.3,0.3-0.4,0.7-0.4,1.1c0,0.6,0.4,1.3,1.4,2c16.1,13.6,34.4,24.2,55,31.8c20.5,7.6,42.6,11.5,66.2,11.5c14.9,0,30.2-1.8,46.1-5.3c15.8-3.5,30.3-8.9,43.6-16C269.8,213.5,271.5,212.5,273.3,211.6z"}),
		el('path', { d: "M303,188.8c-0.2-0.7-0.4-1.2-0.6-1.6c-0.8-0.6-2.3-1.2-4.5-1.7c-2.2-0.5-5-0.9-8.4-1.1c-1.4-0.1-2.9-0.2-4.3-0.2c-2,0-4.1,0.1-6.3,0.3c-3.6,0.3-7.2,0.9-10.8,1.7c-4,1.3-7.6,3-10.8,5.1c-1.2,0.8-1.7,1.6-1.5,2.3c0.2,0.7,0.9,1.1,2.1,1.1c1.5-0.4,3-0.6,4.8-0.6c1.7,0,3.5-0.2,5.4-0.6c3.5-0.3,7-0.5,10.5-0.5c0.9,0,1.8,0,2.6,0c4.3,0.2,7,1.1,8.3,2.8c0.8,1,1.1,2.7,0.8,5c-0.3,2.3-0.9,4.9-1.9,7.7c-1,2.8-2,5.6-3.1,8.5l-2.9,7c-0.4,1.2-0.2,2,0.7,2.4c0.9,0.4,1.9,0.1,2.9-0.9c2.9-2,5.5-4.6,7.7-7.5c2.2-3,4-5.9,5.3-9c1.3-3,2.4-6,3.2-8.9c0.8-3,1.2-5.5,1.2-7.5h0v-1.2C303.3,190.3,303.2,189.4,303,188.8z"}),
		el('polygon', { points: "158.9,350.1 178,350.1 202.6,244.9 183.5,244.9 	"}),
		el('polygon', { points: "57.2,300.4 133.7,350 133.7,327.2 88.8,297.5 133.7,267.7 133.7,244.9 57.2,294.3 	"}),
		el('polygon', { points: "225.9,267.7 270.8,297.5 225.9,327 225.9,350 302.2,300.4 302.2,294.5 225.9,244.9 	"}),
	),
	category: 'amazon-product-category',
	description: __('Enter the ASIN(s) and fields below - to add multiple products, either add another block or add add more ASINs separated by a comma.','amazon-product-in-a-post-plugin'),

	edit: function( props ) {
		function getButtonOptions() {
			let options = [{label: 'Default Button',value:'',}];
			for ( var key in appipTemplates.buttons ) {
				if (key === 'length' || !appipTemplates.buttons.hasOwnProperty(key)){
					continue;
				}else{
					options.push( {
						label: appipTemplates.buttons[ key ].dropdown_title,
						value: key,
					} );
				}
			}
			return options;
		}
		return [
			el( serverSideRender, {
				block: 'amazon-pip/amazon-elements',
				attributes: props.attributes,
			} ),
			el( InspectorControls, {},
				 el( PanelBody, {
					title: __( 'Main Product Settings', 'amazon-product-in-a-post-plugin' ),
					initialOpen: true,
					},
					el( TextControl, {
						label: __('ASIN(s)','amazon-product-in-a-post-plugin'),
						value: props.attributes.asin,
						onChange: ( value ) => { props.setAttributes( { asin: value } ); },
					} ),
					el( TextControl, {
						label: __('Fields','amazon-product-in-a-post-plugin'),
						value: props.attributes.fields,
						onChange: ( value ) => { props.setAttributes( { fields: value } ); },
					} ),
					el( 'div',{'style': {'padding': '0','fontSize': '11px','fontStyle': 'italic','color': '#5A5A5A','marginTop': '5px','marginBottom': '15px'},},
						__('Some common fields are ') + '`image`, `title`, `button`, `gallery`, `price`, `author`, `med-image`, and `sm-image`' + '. ' + __('They will be displayed in the order you add them. See "Shortcode Help" for other available fields.','amazon-product-in-a-post-plugin')
					),
					el( 'input',   {
						value: props.attributes.is_block,
						type: 'hidden',
						name: 'is_block',
						onChange: ( value ) => {  props.setAttributes( { is_block: value } ); },
					} ),
					el( TextControl, {
						label: __('Labels (optional)','amazon-product-in-a-post-plugin'),
						placeholder: 'i.e.,desc::Description:,features::Contains:',
						value: props.attributes.labels,
						onChange: ( value ) => { props.setAttributes( { labels: value } ); },
					} ),
					el( 'div',{'style': {'padding': '0 10px','fontSize': '11px','fontStyle': 'italic','color': '#5A5A5A','marginTop': '-16px','marginBottom': '15px'},},
						__('Add the field and the label separated by a double colon (::). Labels are assigned to all ASINs added. Separate each label set by a comma.','amazon-product-in-a-post-plugin')
					),
					el( SelectControl, {
						label: __('Button (optional)','amazon-product-in-a-post-plugin'),
						value: props.attributes.button,
						options: getButtonOptions(),
						onChange: ( value ) => { props.setAttributes( { button: value } ); },
					}),
					el('div',{'class': 'appip-help-message','style': {'padding': '0 10px','fontSize': '12px','fontStyle': 'italic','color': '#5A5A5A','marginTop': '-16px','marginBottom': '15px', 'line-height': '1.35'},},
						__('if you add a button to the "Fields" section, you can change the appearance by selecting a button style here. You can set the default button image from the "Button Settings" menu.','amazon-product-in-a-post-plugin')
					),					   				   
				),
				el( PanelBody, {
					title: __( 'Layout Settings', 'amazon-product-in-a-post-plugin' ),
					initialOpen: false,
					},
					el( RangeControl, {
						label: __('Title Trim Length (#characters)','amazon-product-in-a-post-plugin'),
						min: 0,
						max: 150,
						style: {width:'50px',},
						value: void '' !== props.attributes.title_charlen ? props.attributes.title_charlen : 0,
						help: __('Use 0 to show full title.','amazon-product-in-a-post-plugin'),
						onChange: ( value ) => { props.setAttributes( { title_charlen: value } ); },
					} ),
				),
				el( PanelBody, {
					title: __( 'Additional Settings', 'amazon-product-in-a-post-plugin' ),
					initialOpen: false,
					},
					el( ToggleControl,{
						label: __('Use Cart URL?','amazon-product-in-a-post-plugin'),
						checked: props.attributes.use_carturl,
						onChange: function (event) { props.setAttributes({use_carturl: !props.attributes.use_carturl});}
					} ),
					el( ToggleControl,{
						label: __('Show on Single Pages only?','amazon-product-in-a-post-plugin'),
						checked: props.attributes.single_only,
						info:'You can set globally in Plugin Settings.',
						onChange: function (event) {props.setAttributes({single_only: !props.attributes.single_only});}
					} ),
					el( ToggleControl,{
						label: __('New Window','amazon-product-in-a-post-plugin'),
						checked: props.attributes.newWindow,
						value: true,
						onChange: ( value ) => { props.setAttributes( { newWindow: value } ); },
					} ),
					el( RangeControl, {
						label: __('# Images in Gallery','amazon-product-in-a-post-plugin'),
						value: void '' !== props.attributes.image_count ? props.attributes.image_count : -1,
						min: -1,
						max: 10,
						help: __('-1 will show all available images. Only available if you add "gallery" to the fields.','amazon-product-in-a-post-plugin'),
						onChange: ( value ) => { props.setAttributes( { image_count: value } ); },
					} ),
				 ),
			),
		];
	},

	save: function() {
		return null;
	},
} );
