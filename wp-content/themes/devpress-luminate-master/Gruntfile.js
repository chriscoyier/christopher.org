'use strict';
module.exports = function(grunt) {

	// load all tasks
	require('load-grunt-tasks')(grunt, {scope: 'devDependencies'});

    grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		watch: {
			files: ['scss/*.scss'],
			tasks: 'sass',
			options: {
				livereload: true,
			},
		},
		sass: {
			default: {
		  		options : {
			  		style : 'expanded'
			  	},
			  	files: {
					'css/style.css':'scss/style.scss',
					'css/page-showcase.css':'scss/page-showcase.scss'
				}
			}
		},
	    postcss: {
			options: {
			map: true,
			processors: [
				require('autoprefixer-core')({browsers: 'last 2 versions'}),
			]
			},
			files: {
				'css/style.css':'css/style.css',
				'css/page-showcase.css':'css/page-showcase.css'
			}
		},
		cssmin: {
			options: {
				aggressiveMerging : false
			},
		    target: {
		        files: [{
		            expand: true,
		            cwd: 'css',
					src: ['*.css', '!*.min.css'],
					dest: 'css',
					ext: '.min.css'
		        }]
		    }
		},
		concat: {
		    release: {
		        src: [
		            'js/skip-link-focus-fix.js',
		            'js/jquery.fastclick.js',
		            'js/sidr.js',
		            'js/theme.js'
		        ],
		        dest: 'js/luminate.min.js',
		    }
		},
		uglify: {
			options: {
				mangle: {
					except: ['jQuery', 'sidr']
      			},
				drop_console: true
			},
		    release: {
			    files: {
				    'js/luminate.min.js' : 'js/luminate.min.js',
				    'js/jquery.fitvids.min.js' : 'js/jquery.fitvids.js'
				}
		    }
		},
	    // https://www.npmjs.org/package/grunt-wp-i18n
	    makepot: {
	        target: {
	            options: {
	                domainPath: '/languages/',
	                potFilename: 'luminate.pot',
	                potHeaders: {
	                poedit: true, // Includes common Poedit headers.
                    'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
                },
		        type: 'wp-theme',
		        updateTimestamp: false,
		        processPot: function( pot, options ) {
					pot.headers['report-msgid-bugs-to'] = 'https://devpress.com/';
		        	pot.headers['language'] = 'en_US';
		        	return pot;
					}
				}
			}
		},
	    replace: {
			styleVersion: {
				src: [
					'scss/style.scss',
					'style.css'
				],
				overwrite: true,
				replacements: [{
					from: /Version:.*$/m,
					to: 'Version: <%= pkg.version %>'
				}]
			},
			functionsVersion: {
				src: [
					'functions.php'
				],
				overwrite: true,
				replacements: [ {
					from: /^define\( 'LUMINATE_VERSION'.*$/m,
					to: 'define( \'LUMINATE_VERSION\', \'<%= pkg.version %>\' );'
				} ]
			},
		},
		cssjanus: {
			theme: {
				options: {
					swapLtrRtlInUrl: false
				},
				files: [
					{
						src: 'css/style.css',
						dest: 'css/style-rtl.css'
					},
					{
						src: 'css/style.min.css',
						dest: 'css/style-rtl.min.css'
					},
				]
			}
		}
	});

	grunt.registerTask( 'default', [
		'sass',
		'postcss',
		'cssmin'
    ]);

    grunt.registerTask( 'release', [
    	'replace',
    	'sass',
    	'postcss',
    	'cssmin',
    	'concat:release',
		'uglify:release',
		'makepot',
		'cssjanus'
	]);

};
