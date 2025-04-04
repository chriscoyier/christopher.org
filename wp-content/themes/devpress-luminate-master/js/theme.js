/*!
 * Script for initializing globally-used functions and libs.
 *
 * @since 1.0.0
 */
(function($) {

 	var luminate = {

 		// Cache selectors
	 	cache: {
			$document: $(document),
			$window: $(window),
			$page: $('#page')
		},

		// Init functions
		init: function() {

			this.bindEvents();

		},

		// Bind Elements
		bindEvents: function() {

			var self = this;

			self.navigationInit();

			this.cache.$document.on( 'ready', function() {

				self.fitVidsInit();

			} );

			this.cache.$window.on( 'resize', self.debounce( function() {
					$.sidr( 'close', 'offcanvas' );
				}
			) );

		},

		/**
		 * Initialize the mobile menu functionality.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		navigationInit: function() {

			// Add dropdown toggle to display child menu items
			$('.main-navigation .menu > .menu-item-has-children').append( '<span class="dropdown-toggle" />');

			// When mobile submenu is tapped/clicked
			$('.dropdown-toggle').fastClick( function() {
				var $submenu = $(this).parent().find('.children, .sub-menu'),
					$toggle = $(this);
				$submenu.toggle( 0, function() {
					$toggle.toggleClass('toggled');
				});
			});

			// Off-canvas menu
			var sources = [];
			if ( document.getElementById( 'primary-navigation-section' ) ) {
				sources[0] = '#primary-navigation-section';
			}
			if ( document.getElementById( 'secondary-section' ) ) {
				sources[1] = '#secondary-section';
			}
			$( '.navigation-button' ).sidr({
				name: 'offcanvas',
				renaming: false,
				source: sources.join(","),
				side : 'right',
				onOpen : function() {
					$('body').addClass( 'offcanvas-open-js' );
					$('.offcanvas-open-js #page').on( 'click', function(){
						$.sidr( 'close', 'offcanvas' );
					});
				},
				onClose : function() {
					$('body').removeClass( 'offcanvas-open-js' );
				}
			});

		},

		// Initialize FitVids
		fitVidsInit: function() {

			// Make sure lib is loaded.
			if (!$.fn.fitVids) {
				return;
			}

			// Run FitVids
			$('.hentry').fitVids();

		},

		/**
		 * Debounce function.
		 *
		 * @since  1.0.0
		 * @link http://remysharp.com/2010/07/21/throttling-function-calls
		 *
		 * @return void
		 */
		debounce: function(fn, delay) {
			var timer = null;
			return function () {
				var context = this, args = arguments;
				clearTimeout(timer);
				timer = setTimeout(function () {
					fn.apply(context, args);
				}, delay);
			};
		}

 	};

 	luminate.init();

 })(jQuery);