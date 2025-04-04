// License: GPLv2+
var el = wp.element.createElement,
	registerBlockType = wp.blocks.registerBlockType,
	serverSideRender = wp.serverSideRender,
	TextControl = wp.components.TextControl,
	InspectorControls = wp.blockEditor.InspectorControls,
	Panel = wp.components.Panel,
	PanelBody = wp.components.PanelBody,
	PanelRow = wp.components.PanelRow;
	var {__} = window.wp.i18n;

/* Here's where we register the block in JavaScript. */
registerBlockType( 'amazon-pip/amazon-search', {
	title: __('Amazon Search Block','amazon-product-in-a-post-plugin'),
	keywords: [ 'amazon' , 'affiliate' ],
	icon: el('svg', 
		{ width: 30, height: 30, viewBox: '0 0 360 360', color:'#555d66',x: "0px", y: "0px" }, 
el('path', { d: "M161.2,113c-2.6,0.4-5.4,0.7-8.3,0.8c-3,0.2-6,0.5-9.3,0.8c-4.9,0.6-9.6,1.3-14.1,2.2c-4.5,0.9-8.8,2.2-12.7,3.9c-7.8,3.1-13.9,7.7-18.5,13.9c-4.6,6.2-6.9,13.9-6.9,23.1c0,11.2,3.4,19.5,10.1,25.1c6.7,5.6,15,8.4,25,8.4c6.3,0,12-0.5,17.1-1.6c3.8-1.1,7.6-2.7,11.2-4.9c3.6-2.2,7.4-5.1,11.2-8.7l5.7,6.7c1.5,1.7,4.2,4.1,8,7.2c2.4,0.9,4.5,0.8,6.2-0.3c2.4-2,5.5-4.4,9.2-7.1c3.7-2.7,6.6-5.1,9-7.1c0.9-0.7,1.4-1.6,1.4-2.6c0-1-0.4-2-1.1-2.9c-2.3-2.6-4.3-5.4-6.2-8.3c-1.8-2.9-2.8-7.1-2.8-12.4V103c0-3.5-0.3-6.8-0.8-10c-0.6-3.2-1.5-6.2-3-9.1c-1.5-2.9-3.6-5.6-6.5-8.1c-4.9-4.1-10.7-7-17.4-8.6c-6.7-1.6-13-2.4-18.8-2.4h-4.9c-5.3,0.4-10.6,1.2-15.8,2.4c-5.2,1.3-10.1,3.2-14.6,5.8c-4.5,2.6-8.3,6-11.6,10.2c-3.2,4.1-5.5,9.3-6.8,15.4c-0.3,1.5-0.1,2.5,0.8,3.2c0.9,0.6,1.8,1.1,2.7,1.2l23,2.9c1.1-0.4,1.9-1,2.6-1.8c0.7-0.8,1.1-1.7,1.2-2.6c1-4.4,3.2-7.7,6.6-10c3.4-2.2,7.2-3.5,11.4-3.9h1.8c2.4,0,4.8,0.5,7.3,1.4c2.4,0.9,4.4,2.5,5.9,4.7c1.7,2.3,2.5,4.9,2.6,7.8l0,0c0.1,2.9,0.1,5.7,0.1,8.5h0V113z M161.2,135.9c0,4.4-0.1,8.5-0.2,12.3c-0.1,3.8-1.6,7.7-4.3,11.7c-3,5.4-7.4,8.7-13.3,10c-0.4,0-0.8,0.1-1.4,0.2c-0.5,0.1-1.2,0.2-1.9,0.2c-4.6,0-8.2-1.5-10.8-4.5c-2.6-3-3.9-7-3.9-12c0-6.2,1.8-10.9,5.3-14.1c3.6-3.2,7.8-5.6,12.8-7.1l0,0c2.7-0.6,5.6-0.9,8.6-1.1c3-0.2,6.1-0.3,9.2-0.3V135.9z"}),
el('path', { d: "M225.8,202.1c-0.8-0.2-1.6-0.1-2.4,0.3c-0.9,0.4-1.8,0.7-2.6,1c-0.8,0.2-1.6,0.5-2.4,0.8c-11.5,4.1-23.1,7.2-34.7,9.1c-11.6,2-22.9,3-34.1,3c-17.5,0-34.5-2.2-50.8-6.5c-16.4-4.3-31.3-10.4-44.9-18.1c-0.6-0.3-1.1-0.5-1.6-0.5c-0.6,0-1,0.3-1.4,0.7c-0.2,0.2-0.4,0.5-0.4,0.8c0,0.5,0.4,1,1.1,1.6c12.8,10.7,27.3,19.1,43.5,25.2c16.2,6.1,33.7,9.1,52.4,9.1c11.8,0,23.9-1.4,36.5-4.2c12.5-2.8,24-7,34.5-12.6c1.3-0.8,2.6-1.6,4.1-2.4c1.4-0.8,2.8-1.6,4.1-2.6l0,0c1-0.6,1.4-1.3,1.4-2.3c-0.1-0.5-0.2-0.9-0.5-1.3C227.2,202.6,226.6,202.3,225.8,202.1z"}),
el('path', { d: "M218.4,189.4c-3.2,1.1-6,2.4-8.5,4c-1,0.6-1.4,1.3-1.2,1.8c0.2,0.6,0.7,0.9,1.7,0.9c1.1-0.3,2.4-0.5,3.8-0.5c1.4,0,2.8-0.2,4.3-0.5c2.8-0.3,5.5-0.4,8.3-0.4c0.7,0,1.4,0,2.1,0c3.4,0.2,5.6,0.9,6.6,2.2c0.6,0.8,0.8,2.1,0.6,3.9c-0.2,1.8-0.7,3.8-1.5,6.1c-0.8,2.2-1.6,4.5-2.5,6.7l-2.3,5.6c-0.3,1-0.1,1.6,0.5,1.9c0.7,0.3,1.5,0.1,2.3-0.7c2.3-1.6,4.3-3.6,6.1-5.9c1.7-2.3,3.1-4.7,4.2-7.1c1-2.4,1.9-4.7,2.5-7.1c0.6-2.3,1-4.3,1-5.9h0v-1c0-0.8-0.1-1.5-0.3-2c-0.2-0.5-0.4-0.9-0.5-1.3c-0.6-0.5-1.8-0.9-3.5-1.3c-1.7-0.4-3.9-0.7-6.6-0.8c-1.1-0.1-2.3-0.1-3.4-0.1c-1.6,0-3.2,0.1-5,0.2C224,188.3,221.2,188.7,218.4,189.4z"}),
el('path', { d: "M352.6,311.5L270.1,229c13.4-21.8,21.2-47.5,21.2-75c0-78.9-64-142.9-142.9-142.9c-79,0-142.9,64-142.9,142.9c0,79,63.9,143,142.9,143c29,0,55.8-8.6,78.3-23.3l82,82c4.2,4.2,14.8,0.6,23.6-8.2l12.3-12.3C353.3,326.4,357,315.8,352.6,311.5zM148.4,272c-65.1,0-118-52.9-118-118s52.9-117.9,118-117.9c65.1,0,117.9,52.7,117.9,117.9C266.3,219.1,213.5,272,148.4,272z"}),
	),
	category: 'amazon-product-category',
	description:__('Use this plock to find amazon products by keyword.','amazon-product-in-a-post-plugin'),
	edit: function( props ) {
		function getFormOptions() {
			let options = [];
			for ( let i = 0; i < appipTemplates.templates.length; i++ ) {
				if( appipTemplates.templates[ i ].location === 'core' || appipTemplates.templates[ i ].location === 'search' ){
				// 'core' is for all, 'search' is specific to this block
					options.push( {
						label: appipTemplates.templates[ i ].name,
						value: appipTemplates.templates[ i ].ID,
					} );
				}
			}
			return options;
		}
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
				block: 'amazon-pip/amazon-search',
				attributes: props.attributes,
			},
			function($vg){
				console.log($vg);
			},
			),
			el( InspectorControls, {},
					el(PanelBody, {
						title: __('Search Settings','amazon-product-in-a-post-plugin'),
						initialOpen: true,
						},
						el( ToggleControl,{
							label: __('Search Product Titles?','amazon-product-in-a-post-plugin'),
							checked: props.attributes.search_title,
							onChange: function (event) {
								props.setAttributes({search_title: !props.attributes.search_title});
							}
						}),
						el( TextControl, {
							label: __("Keywords(s)",'amazon-product-in-a-post-plugin'),
							value: props.attributes.keywords,
							onChange: ( value ) => { 
								props.setAttributes( { keywords: value } )
							},
						}),
						el( TextControl, {
							label: __('Search Index','amazon-product-in-a-post-plugin'),
							placeholder: __('i.e., All, Blended, Baby, Books','amazon-product-in-a-post-plugin'),
							value: props.attributes.search_index,
							onChange: ( value ) => { 
								props.setAttributes( { search_index: value } );
								if(value == 'All' || value == "Blended"){
									if( props.attributes.search_index > 5)
										props.setAttributes( { search_index: 5 });
									if( props.attributes.browse_node != '')
										props.setAttributes( { browse_node: '' });
									//console.log(props);
								}
							},
						} ),
					   	el( 'a',{
							'class': 'appip-help-message',
							'href': 'https://docs.aws.amazon.com/AWSECommerceService/latest/DG/localevalues.html',
							'target': '_blank',
							'style':{'margin-top':'-18px','margin-bottom': '15px', 'display': 'block', 'font-size': '.9em', 'text-decoration': 'none', 'line-height': '1.25'},
							},
							__('see more on SeachIndexs, BrowseNodes and sorting options.','amazon-product-in-a-post-plugin'),
					   ),
						el( SelectControl, {
							label: __('Availability','amazon-product-in-a-post-plugin'),
							value: props.attributes.availability,
							options:[
								{ label: "Available", value: "Available"},
								{ label: "Include Out Of Stock", value: "IncludeOutOfStock"},
								], 
							default: "Available",
							onChange: ( value ) => { props.setAttributes( { availability: value } ); },
						} ),

						el( TextControl, {
							label: __('Browse Node (number)','amazon-product-in-a-post-plugin'),
							placeholder: __('i.e., 1000 (Books)','amazon-product-in-a-post-plugin'),
							value: props.attributes.browse_node,
							onChange: ( value ) => { props.setAttributes( { browse_node: value } ); },
						} ),
						el( SelectControl, {
							label: __('Condition','amazon-product-in-a-post-plugin'),
							value: props.attributes.condition,
							options:[
								{ label: "New", value: "New"},
								{ label: "Used", value: "Used"},
								{ label: "Collectible", value: "Collectible"},
								{ label: "Refurbished", value: "Refurbished"},
								{ label: "All", value: "All"},
								{ label: 'Release Date DESC', value: '-releasedate'},
								], 
							default: "New",
							onChange: ( value ) => { props.setAttributes( { condition: value } ); },
						} ),
						el( RangeControl, {
							label: __('Item Page','amazon-product-in-a-post-plugin'),
							min: 1,
							max: 10,
							value: void '' !== props.attributes.item_page ? props.attributes.item_page : 1,
							help: __('Page of products (1-10).','amazon-product-in-a-post-plugin'),
							onChange: ( value ) => { props.setAttributes( { item_page: value } ); },
						} ),
						el( RangeControl, {
							label: __('#Products to Show','amazon-product-in-a-post-plugin'),
							min: 1,
							max: 10,
							value: void '' !== props.attributes.item_count ? props.attributes.item_count : 10,
							help: __('Number of Products to return (1-10).','amazon-product-in-a-post-plugin'),
							onChange: ( value ) => { props.setAttributes( { item_count: value } ); },
						} ),
						el( TextControl, {
							label: __('Sort Products By','amazon-product-in-a-post-plugin'),
							placeholder: __('i.e., titlerank, salesrank','amazon-product-in-a-post-plugin'),
							value: props.attributes.sort,
							onChange: ( value ) => { props.setAttributes( { sort: value } ); },
						} ),
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
					el(PanelBody, {
						title: __('Additional Settings','amazon-product-in-a-post-plugin'),
						initialOpen: false,
						},
						el( TextControl, {
							label: __('Fields','amazon-product-in-a-post-plugin'),
							value: props.attributes.fields,
							onChange: ( value ) => { props.setAttributes( { fields: value } ); },
						}),
						el( 'div',{'class': 'appip-help-message','style': {'padding': '0','fontSize': '11px','fontStyle': 'italic','color': '#5A5A5A','marginTop': '5px','marginBottom': '15px'},},
							__('Some common fields are ') + '`image`, `title`, `button`, `gallery`, `price`, `author`, `med-image`, and `sm-image`' + '. ' + __('They will be displayed in the order you add them. See "Shortcode Help" for other available fields.','amazon-product-in-a-post-plugin')
						),
					   el( SelectControl,   {
							label: __('Template','amazon-product-in-a-post-plugin'),
							value: props.attributes.template,
							options: getFormOptions(),
							onChange: ( value ) => {  props.setAttributes( { template: value } ); },
							} ),

						el( RangeControl, {
							label: __('# Columns','amazon-product-in-a-post-plugin'),
							value: void '' !== props.attributes.columns ? props.attributes.columns : 3,
							min: 1,
							max: 8,
							info:'Only used for "Grid" template.',
							onChange: ( value ) => { props.setAttributes( { columns: value } ); },
						} ),
						el( 'input',   {
							value: props.attributes.is_block,
							type: 'hidden',
							name: 'is_block',
							onChange: ( value ) => {  props.setAttributes( { is_block: value } ); },
						} ),
						el( TextControl, {
							label: __('Element Labels (optional)','amazon-product-in-a-post-plugin'),
							placeholder: 'i.e.,desc::Description:,features::Contains:',
							value: props.attributes.labels,
							onChange: ( value ) => { props.setAttributes( { labels: value } ); },
						} ),
						el( 'div',{'style': {'padding': '0 10px','fontSize': '11px','fontStyle': 'italic','color': '#5A5A5A','marginTop': '-16px','marginBottom': '15px'},},
							__('Add the field and the label separated by a double colon (::). Labels are assigned to all ASINs added. Separate each label set by a comma.','amazon-product-in-a-post-plugin')
						),
						el( ToggleControl,{
							label: __('Use Cart URL?','amazon-product-in-a-post-plugin'),
							checked: props.attributes.use_carturl,
							onChange: function (event) {
								props.setAttributes({use_carturl: !props.attributes.use_carturl});
							}
						}),
						el( ToggleControl,{
							label: __('Show on Single Pages only?','amazon-product-in-a-post-plugin'),
							checked: props.attributes.single_only,
							info:'You can set globally in Plugin Settings.',
							onChange: function (event) {props.setAttributes({single_only: !props.attributes.single_only});}
						}),
						el( ToggleControl,{
							label: __('New Window','amazon-product-in-a-post-plugin'),
							checked: props.attributes.newWindow,
							value: true,
							onChange: ( value ) => { props.setAttributes( { newWindow: value } ); },
						} ),
						el( RangeControl, {
							label: __('Title Trim Length (#characters)','amazon-product-in-a-post-plugin'),
							min: 0,
							max: 150,
							style: {width:'50px',},
							value: void '' !== props.attributes.title_charlen ? props.attributes.title_charlen : 0,
							help: __('Use 0 to show full title.','amazon-product-in-a-post-plugin'),
							onChange: ( value ) => { props.setAttributes( { title_charlen: value } ); },
						} ),
						el( RangeControl, {
							label: __('#Images in Gallery','amazon-product-in-a-post-plugin'),
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