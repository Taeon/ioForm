module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        concat: {
            js: {
                options: {
                    separator: ';\n'
                },
                src: [
                    'src/js/ioForm.js',
                    'src/js/Field.js',
                    'src/js/Fields/*.js'
                ],
                dest: 'dist/js/ioform.js'
            },
        },
        watch: {
            js:{
                files: ['<%= concat.js.src %>'],
                tasks: ['concat:js','uglify']
            },
            options: {
                spawn: false,
            }
        },
        uglify: {
            js: {
                files:{
                    'dist/js/ioform.min.js': 'dist/js/ioform.js'
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-copy');
    
    grunt.registerTask(
        'default',
        [
            'concat:js',
            'uglify'
        ]
    );

};
