module.exports = function(grunt){
	
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
		htmlhint: {
			build: {
				options: {
					'tag-pair': true,
					'tagname-lowercase': true,
					'attr-lowercase': true,
					'attr-value-double-quotes': true,
					'doctype-first': true,
					'spec-char-escape': true,
					'id-unique': true,
					'head-script-disabled': true,
					'style-disabled': true
				},
				src: ['index.html', 'app/views/*.html']
			}
		},
                uglify: {
                      default: {
                          files: [{
                              expand: true,
                              cwd: 'app/',
                              src: '**/*.js',
                              dest : 'app-minify/'
                          }]
                        },
                }, 
                cssmin: {
                    target: {
                      files: [{
                        expand: true,
                        cwd: 'css',
                        src: ['style.css', '!font-awesome.css'],
                        dest: 'css/',
                        ext: '.min.css'
                      }]
                    }
                },
                htmlmin: { // Task
                    dist: { // Target
                      cwd: 'app/',  
                      options: { // Target options
                        removeComments: true,
                        collapseWhitespace: true
                      },
                      files: [{
                            expand: true,
                            cwd: 'app/views',
                            src: '**/*.html',
                            dest: 'app-minify/views/'
                      }]
                    },                    
                 },
                 concat: {
                    dist: {
                      options: {
                        // Replace all 'use strict' statements in the code with a single one at the top
                        banner: "'use strict';\n",
                        process: function(src, filepath) {
                          return '// Source: ' + filepath + '\n' +
                            src.replace(/(^|\n)[ \t]*('use strict'|"use strict");?\s*/g, '$1');
                        },
                      },
                      src: "app-minify/**/*.js",            
                      dest: "app-minify/app.min.js"
                    },
                  },
                  clean: {
                        start:[
                            "app-minify/"  
                        ],
                        end: [
                            "app-minify/app.js",
                            "app-minify/controllers/", 
                            "app-minify/directives/",
                            "app-minify/factories/",
                            "app-minify/filter/",                           
                            "app-minify/services/",
                            "app-minify/vendor/"                            
                        ],                       
                  },
                
                watch: {
                    scripts: {
                      files: ['app/**', 'app/view/**/*.html'],
                      tasks: ['default'],
                      options: {
                        spawn: false,
                        livereload: true
                      },
                    },
                },
    });

    
    require("matchdep").filterDev("grunt-*").forEach(grunt.loadNpmTasks);
    grunt.registerTask('default', ['clean:start', 'uglify:default', 'cssmin', 'htmlmin:dist', 'concat:dist', 'clean:end']);   

};

// grunt 
