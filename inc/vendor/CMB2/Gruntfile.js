module.exports = function(grunt) {

	// load all grunt tasks in package.json matching the `grunt-*` pattern
	require('load-grunt-tasks')(grunt);

	grunt.initConfig({

		pkg: grunt.file.readJSON( 'package.json' ),

		phpunit: {
			classes: {},
			options: {
				excludeGroup: 'cmb2-ajax-embed',
			}
		},

		dirs: {
			lang: 'languages'
		},

		makepot: {
			target: {
				options: {
					domainPath: 'languages/',
					potComments: '',
					potFilename: 'cmb2.pot',
					type: 'wp-plugin',
					updateTimestamp: true,
					potHeaders: {
						poedit: true,
						'language': 'en_US',
						'x-poedit-keywordslist': true
					},
					processPot: function( pot, options ) {
						pot.headers['report-msgid-bugs-to'] = 'http://wordpress.org/support/plugin/cmb2';
						pot.headers['last-translator'] = 'CMB2 Team hello@cmb2.io';
						pot.headers['language-team'] = 'CMB2 Team hello@cmb2.io';
						var today = new Date();
						pot.headers['po-revision-date'] = today.getFullYear() +'-'+ ( today.getMonth() + 1 ) +'-'+ today.getDate() +' '+ today.getUTCHours() +':'+ today.getUTCMinutes() +'+'+ today.getTimezoneOffset();
						return pot;
					}
				}
			}
		},

		potomo: {
			dist: {
				options: {
					poDel: false
				},
				files: [{
					expand: true,
					cwd: '<%= dirs.lang %>/',
					src: ['*.po'],
					dest: '<%= dirs.lang %>/',
					ext: '.mo',
					nonull: true
				}]
			}
		},

		checktextdomain: {
			options: {
				text_domain: 'cmb2',
				create_report_file: true,
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d',
					' __ngettext:1,2,3d',
					'__ngettext_noop:1,2,3d',
					'_c:1,2d',
					'_nc:1,2,4c,5d'
				]
			},
			files: {
				src: [
					'**/*.php', // Include all files
					'!node_modules/**', // Exclude node_modules/
					],
				expand: true
			}
		},

		csscomb: {
			dist: {
				files: [{
					expand: false,
					cwd: 'css/',
					src: ['css/cmb2.css','css/cmb2-display.css'],
					dest: 'css/',
				}]
			}
		},

		sass: {
			dist: {
				options: {
					style: 'expanded',
					lineNumbers: true
				},
				files: {
				  'css/cmb2.css': 'css/sass/cmb2.scss',
				  'css/cmb2-front.css': 'css/sass/cmb2-front.scss',
				  'css/cmb2-display.css': 'css/sass/cmb2-display.scss'
				}
			}
		},

		usebanner: {
			taskName: {
				options: {
					position: 'top',
					banner: '/*!\n' +
						' * <%= pkg.title %> - v<%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd") %>\n' +
						' * <%= pkg.homepage %>\n' +
						' * Copyright (c) <%= grunt.template.today("yyyy") %>\n' +
						' * Licensed GPLv2+\n' +
						' */\n',
					linebreak: true
				},
				files: {
					src: [
						'css/cmb2.css',
						'css/cmb2-front.css',
						'css/cmb2-display.css',
						'css/cmb2-rtl.css',
						'css/cmb2-front-rtl.css',
						'css/cmb2-display-rtl.css'
					],
				}
			}
		},

		cssmin: {
			options: {
				banner: '/*! <%= pkg.title %> - v<%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd") %>' +
					' | <%= pkg.homepage %>' +
					' | Copyright (c) <%= grunt.template.today("yyyy") %> <%= pkg.author.name %>' +
					' | Licensed <%= pkg.license %>' +
					' */'
			},
			minify: {
				expand: true,
				src: [
					'css/cmb2.css',
					'css/cmb2-front.css',
					'css/cmb2-display.css',
					'css/cmb2-rtl.css',
					'css/cmb2-front-rtl.css',
					'css/cmb2-display-rtl.css'
				],
				// dest: '',
				ext: '.min.css'
			}
		},

		jshint: {
			all: [
				'js/cmb2.js',
				'js/cmb2-wysiwyg.js',
				'js/cmb2-char-counter.js'
			],
			options: {
				curly   : true,
				eqeqeq  : true,
				immed   : true,
				latedef : true,
				newcap  : true,
				noarg   : true,
				sub     : true,
				unused  : true,
				undef   : true,
				boss    : true,
				eqnull  : true,
				globals : {
					exports : true,
					module  : false
				},
				predef  :['document','window','jQuery','cmb2_l10','wp','tinyMCEPreInit','tinyMCE','console','postboxes','pagenow', 'QTags', 'quicktags', '_']
			}
		},

		asciify: {
			banner: {
				text    : 'CMB2',
				options : {
					font : 'univers',
					log  : true
				}
			}
		},

		uglify: {
			all: {
				files: {
					'js/cmb2.min.js': ['js/cmb2.js', 'js/cmb2-wysiwyg.js', 'js/cmb2-char-counter.js']
				},
				options: {
					mangle: false
				}
			}
		},

		watch: {

			css: {
				files: ['css/sass/**/*.scss'],
				tasks: ['styles'],
				options: {
					spawn: false,
				},
			},

			scripts: {
				files: ['js/cmb2.js', 'js/cmb2-wysiwyg.js', 'js/cmb2-char-counter.js'],
				tasks: ['js'],
				options: {
					debounceDelay: 500
				}
			},

			other: {
				files: [ '*.php', '**/*.php', '!node_modules/**', '!tests/**' ],
				tasks: [ 'makepot' ]
			}

		},

		cssjanus: {
			i18n: {
				options: {
					swapLtrRtlInUrl: false
				},
				files: [
					{ src: 'css/cmb2-display.css', dest: 'css/cmb2-display-rtl.css' },
					{ src: 'css/cmb2-front.css', dest: 'css/cmb2-front-rtl.css' },
					{ src: 'css/cmb2.css', dest: 'css/cmb2-rtl.css' }
				]
			}
		},

		exec: {
			apigen: {
				cmd: [
					'rm -r ~/Sites/wpengine/api',
					'echo "Old API docs removed"',
					'apigen generate --config apigen/apigen.neon --debug',
					'echo "Docs regenerated"',
					'php apigen/hook-docs.php'
				].join( '&&' )
			}
		},

	});

	var asciify = ['asciify'];
	var styles  = ['sass', 'csscomb', 'cssjanus', 'cssmin', 'usebanner'];
	var hint    = ['jshint'];
	var js      = ['jshint', 'uglify'];
	var tests   = ['jshint', 'phpunit'];

	grunt.registerTask( 'styles', asciify.concat( styles ) );
	grunt.registerTask( 'css', asciify.concat( styles ) );
	grunt.registerTask( 'hint', asciify.concat( hint ) );
	grunt.registerTask( 'js', asciify.concat( js ) );
	grunt.registerTask( 'tests', asciify.concat( tests ) );
	grunt.registerTask( 'default', asciify.concat( styles, js, tests ) );

	// apigen
	grunt.registerTask( 'apigen', asciify.concat( ['exec:apigen'] ) );

	// Checktextdomain and makepot task(s)
	grunt.registerTask( 'build:i18n', asciify.concat( ['checktextdomain', 'makepot', 'newer:potomo'] ) );
};
