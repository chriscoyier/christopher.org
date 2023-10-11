// License: GPLv2+
var el = wp.element.createElement,
	registerBlockType = wp.blocks.registerBlockType,
	serverSideRender = wp.serverSideRender,
	InspectorControls = wp.blockEditor.InspectorControls,
	TextControl = wp.components.TextControl,
	SelectControl = wp.components.SelectControl,
	RangeControl = wp.components.RangeControl,
	ToggleControl = wp.components.ToggleControl;
	var {__} = window.wp.i18n;
/*
 * Here's where we register the block in JavaScript.
 */

registerBlockType( 'amazon-pip/amazon-product', {
	title: __('Amazon Product Block','amazon-product-in-a-post-plugin'),
	icon: el('svg', { width: 30, height: 30, viewBox: '0 0 360 360', color:'#555d66',x: "0px", y: "0px"}, 
		el('path', { d: "M196.7,140.1c-3,0.4-6.3,0.8-9.8,1c-3.5,0.2-7.1,0.6-10.9,1c-5.7,0.7-11.3,1.5-16.6,2.6c-5.3,1-10.3,2.6-15,4.6c-9.1,3.7-16.4,9.1-21.8,16.4c-5.4,7.3-8.2,16.3-8.2,27.2c0,13.1,3.9,23,11.8,29.6c7.9,6.6,17.7,9.9,29.4,9.9c7.4,0,14.1-0.6,20.1-1.9c4.5-1.3,8.9-3.2,13.2-5.7c4.3-2.6,8.7-6,13.2-10.3l6.8,7.8c1.8,2,4.9,4.8,9.5,8.5c2.9,1,5.3,0.9,7.3-0.3c2.9-2.4,6.5-5.2,10.8-8.4c4.3-3.2,7.8-6,10.5-8.4c1.1-0.8,1.6-1.9,1.6-3c0-1.2-0.4-2.3-1.3-3.4c-2.7-3.1-5.1-6.4-7.3-9.8c-2.2-3.4-3.3-8.3-3.3-14.6v-54.4c0-4.1-0.3-8.1-1-11.8c-0.7-3.7-1.8-7.3-3.5-10.7c-1.7-3.4-4.3-6.6-7.7-9.5c-5.8-4.9-12.6-8.3-20.5-10.2c-7.9-1.9-15.3-2.9-22.1-2.9h-5.7c-6.2,0.4-12.4,1.4-18.6,2.9c-6.2,1.5-11.9,3.7-17.1,6.8c-5.3,3.1-9.8,7.1-13.6,12c-3.8,4.9-6.5,10.9-8,18.1c-0.4,1.7-0.1,3,0.9,3.7c1,0.7,2.1,1.2,3.1,1.5l27,3.4c1.3-0.5,2.3-1.1,3-2.1c0.8-0.9,1.2-2,1.5-3.1c1.2-5.2,3.8-9.1,7.8-11.7c4-2.6,8.5-4.1,13.5-4.6h2.1c2.9,0,5.7,0.6,8.6,1.7c2.9,1.1,5.2,2.9,7,5.5c2,2.7,3,5.7,3.1,9.1l0,0c0.1,3.4,0.2,6.8,0.2,10h0V140.1z M196.7,167c0,5.2-0.1,10-0.2,14.5c-0.1,4.4-1.8,9-5,13.8c-3.5,6.3-8.7,10.3-15.7,11.8c-0.4,0-1,0.1-1.7,0.3c-0.6,0.2-1.4,0.2-2.3,0.2c-5.4,0-9.6-1.8-12.7-5.3c-3.1-3.5-4.6-8.3-4.6-14.1c0-7.3,2.1-12.9,6.3-16.6c4.2-3.8,9.2-6.5,15-8.3l0,0c3.2-0.7,6.6-1.1,10.1-1.3c3.6-0.2,7.2-0.3,10.8-0.3V167z"}),
		el('path', { d: "M272.7,244.9c-1-0.2-1.9-0.1-2.9,0.3c-1,0.5-2.1,0.8-3,1.1c-1,0.3-1.9,0.6-2.9,1c-13.6,4.8-27.2,8.4-40.8,10.8c-13.6,2.3-27,3.5-40.1,3.5c-20.6,0-40.6-2.5-59.9-7.7c-19.3-5.1-36.9-12.2-52.9-21.3c-0.7-0.4-1.3-0.6-1.9-0.6c-0.7,0-1.2,0.3-1.7,0.9c-0.3,0.3-0.4,0.6-0.4,1c0,0.6,0.4,1.2,1.3,1.9c15,12.6,32.1,22.5,51.2,29.7c19.1,7.1,39.7,10.7,61.6,10.7c13.9,0,28.2-1.6,42.9-4.9c14.8-3.3,28.3-8.3,40.6-14.9c1.5-1,3.1-1.9,4.8-2.8c1.7-0.9,3.3-1.9,4.8-3.1l0,0c1.2-0.7,1.7-1.6,1.7-2.7c-0.1-0.6-0.3-1.1-0.5-1.6C274.3,245.5,273.6,245.1,272.7,244.9z"}),
		el('path', { d: "M295.8,230.8c-0.7-0.6-2.1-1.1-4.1-1.6c-2-0.5-4.6-0.8-7.8-1c-1.3-0.1-2.7-0.1-4-0.1c-1.8,0-3.8,0.1-5.9,0.3c-3.3,0.3-6.7,0.8-10,1.6c-3.7,1.2-7.1,2.8-10,4.7c-1.2,0.8-1.6,1.5-1.4,2.2c0.2,0.7,0.9,1,2,1c1.4-0.4,2.8-0.6,4.4-0.6c1.6,0,3.3-0.2,5-0.6c3.2-0.3,6.5-0.4,9.7-0.4c0.9,0,1.7,0,2.4,0c4,0.2,6.6,1,7.7,2.6c0.8,1,1,2.5,0.7,4.6c-0.3,2.2-0.9,4.5-1.8,7.1c-0.9,2.6-1.9,5.3-2.9,7.9l-2.7,6.6c-0.4,1.1-0.2,1.9,0.6,2.3c0.8,0.4,1.7,0.1,2.7-0.9c2.7-1.9,5.1-4.2,7.2-7c2-2.8,3.7-5.5,4.9-8.4c1.2-2.8,2.2-5.6,2.9-8.3c0.8-2.8,1.1-5.1,1.1-7h0v-1.2c0-0.9-0.1-1.7-0.3-2.3C296.1,231.7,295.9,231.2,295.8,230.8z"}),
		el('path', { d: "M301,28H62c-17.8,0-32.2,14.4-32.2,32.2v238.9c0,17.8,14.4,32.2,32.2,32.2H301c17.8,0,32.2-14.4,32.2-32.2V60.2C333.2,42.4,318.8,28,301,28z M317.7,286.9c0,16-13,28.9-28.9,28.9H74.2c-16,0-28.9-13-28.9-28.9V72.4c0-16,13-28.9,28.9-28.9h214.6c16,0,28.9,13,28.9,28.9V286.9z"})
	),
	category: 'amazon-product-category',
	description: __('Main Amazon Product. Enter the ASIN(s) below - to add multiple products, either add another block or add add more ASINs separated by a comma.','amazon-product-in-a-post-plugin'),

	edit: function( props ) {
		function getFormOptions() {
			let options = [];
			for ( let i = 0; i < appipTemplates.templates.length; i++ ) {
				if( appipTemplates.templates[ i ].location === 'core' || appipTemplates.templates[ i ].location === 'product' ){
				// 'core' is for all, 'product' is specific to this block
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
				block: 'amazon-pip/amazon-product',
				attributes: props.attributes,
				} ,
			 ),
/* SECTION 6: Inspector Panel Options */
			el( InspectorControls,{},
				el( PanelBody, {
					title: __('Main Settings','amazon-product-in-a-post-plugin'),
					initialOpen: true,
					},
					el( TextControl,   {
						label: __('ASIN(s)','amazon-product-in-a-post-plugin'),
						placeholder: __('Enter ASIN or ASINs','amazon-product-in-a-post-plugin'),
						value: props.attributes.asin,
						onChange: ( value ) => {  props.setAttributes( { asin: value } ); },
						}
					),
					el( 'input',   {
						value: props.attributes.is_block,
						type: 'hidden',
						name: 'is_block',
						onChange: ( value ) => {  props.setAttributes( { is_block: value } ); },
						}
					),
					el( SelectControl,   {
						label: __('Template','amazon-product-in-a-post-plugin'),
						value: props.attributes.template,
						options: getFormOptions(),
						onChange: ( value ) => {  props.setAttributes( { template: value } ); },
						}
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
				el(PanelBody, {
					title: __('Element Settings','amazon-product-in-a-post-plugin'),
					initialOpen: false,
					},
					el( ToggleControl, {
							label: __('Show Description?','amazon-product-in-a-post-plugin'),
							checked: props.attributes.desc,
							onChange: function (event) {
								props.setAttributes({desc: !props.attributes.desc});
							}
						}
					),
					el( ToggleControl, {
							label: __('Show Product Features?','amazon-product-in-a-post-plugin'),
							checked: props.attributes.features,
							onChange: function (event) {
								props.setAttributes({features: !props.attributes.features});
							}
						}
					),
					el( ToggleControl, {
							label: __('Show List Price?','amazon-product-in-a-post-plugin'),
							checked: props.attributes.listprice,
							onChange: function (event) {
								props.setAttributes({listprice: !props.attributes.listprice});
							}
						}
					),
					el( ToggleControl, {
							label: __('Show New Price?','amazon-product-in-a-post-plugin'),
							checked: props.attributes.new_price,
							onChange: function (event) {
								props.setAttributes({new_price: !props.attributes.new_price});
							}
						}
					),
					el( ToggleControl, {
							label: __('Show Used Price?','amazon-product-in-a-post-plugin'),
							checked: props.attributes.used_price,
							onChange: function (event) {
								props.setAttributes({used_price: !props.attributes.used_price});
							}
						}
					),
					el( ToggleControl, {
							label: __('Use Cart URL?','amazon-product-in-a-post-plugin'),
							checked: props.attributes.use_carturl,
							onChange: function (event) {
								props.setAttributes({use_carturl: !props.attributes.use_carturl});
							}
						}
					),
					el( ToggleControl, {
							label: __('Show Gallery Images?','amazon-product-in-a-post-plugin'),
							checked: props.attributes.gallery,
							onChange: function (event) {
								props.setAttributes({gallery: !props.attributes.gallery});
							}
						}
					),
				  ),
				 el( PanelBody, {
					title: __('Additional Settings','amazon-product-in-a-post-plugin'),
					initialOpen: false,
					},
					el( TextControl,   {
						label: __('Replace Title Text','amazon-product-in-a-post-plugin'),
						value: props.attributes.replace_title,
						placeholder: __('New Title Text (optional)','amazon-product-in-a-post-plugin'),
						onChange: ( value ) => {  props.setAttributes( { replace_title: value } ); },
						}
					),
					el( 'div', {'style': { 'padding': '0', 'fontSize': '11px', 'fontStyle': 'italic', 'color': '#5A5A5A', 'marginTop': '5px', 'marginBottom': '15px'},},
						__('If you add multiple products and want to replace each title, separate them by a double colon (::), for example: "New Title 1::New Title 2::New Title 3", etc.','amazon-product-in-a-post-plugin')
					),
					el( ToggleControl, {
							label: __('Show on Single Pages only?','amazon-product-in-a-post-plugin'),
							checked: props.attributes.single_only,
							info:'You can set globally in Plugin Settings.',
							onChange: function (event) {
								props.setAttributes({single_only: !props.attributes.single_only});
							}
						}
					),
				    el( ToggleControl, {
							label: __('Hide Title?','amazon-product-in-a-post-plugin'),
							checked: props.attributes.hide_title,
							onChange: function (event) {
								props.setAttributes({hide_title: !props.attributes.hide_title});
							}
						}
					),
					el( RangeControl, {
							label: __('Title Trim Length (#characters)','amazon-product-in-a-post-plugin'),
							min: 0,
							max: 150,
							style: {width:'50px',},
							value: void '' !== props.attributes.title_charlen ? props.attributes.title_charlen : 0,
							help: __('Use 0 to show full title.','amazon-product-in-a-post-plugin'),
							onChange: ( value ) => { props.setAttributes( { title_charlen: value } ); },
					} ),
					el( ToggleControl, {
							label: __('Hide Product Image?','amazon-product-in-a-post-plugin'),
							checked: props.attributes.hide_image,
							onChange: function (event) {
								props.setAttributes({hide_image: !props.attributes.hide_image});
							}
						}
					),
					el( ToggleControl, {
							label: __('Hide Larger Image Text?','amazon-product-in-a-post-plugin'),
							checked: props.attributes.hide_lg_img_text,
							onChange: function (event) {
								props.setAttributes({hide_lg_img_text: !props.attributes.hide_lg_img_text});
							}
						}
					),
					el( ToggleControl, {
							label: __( 'Hide Pre-Order Date?', 'amazon-product-in-a-post-plugin'),
							checked: props.attributes.hide_release_date,
							description: __('Hide the "Available on XX Date" text when item is a pre-order.','amazon-product-in-a-post-plugin'),
							onChange: function (event) {
								props.setAttributes({hide_release_date: !props.attributes.hide_release_date});
							}
						}
					),
					el( RangeControl, {
						label: __( '# Images in Gallery', 'amazon-product-in-a-post-plugin'),
						value: void '' !== props.attributes.image_count ? props.attributes.image_count : -1,
						min: -1,
						max: 10,
						help: __( '-1 will show all available images.', 'amazon-product-in-a-post-plugin' ),
						onChange: ( value ) => { props.setAttributes( { image_count: value } ); },
					} ),

				 ),
			),
		];
	},

	// We're going to be rendering in PHP, so save() can just return null.
	save: function() {
		return null;
	},
} );
