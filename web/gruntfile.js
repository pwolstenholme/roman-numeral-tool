module.exports = function (grunt) {
    grunt.initConfig({
        watch: {
            src: {
                files: ['sass/*.scss', '*.php', '*.html', 'js/*.js'],
                tasks: ['compass:dev', 'uglify:dist']
            },
        },
        compass: {
            dev: {
                options: {
                    sassDir: 'sass',
                    cssDir: 'css',
                    imagesPath: 'img',
                    noLineComments: false,
                    outputStyle: 'compressed'
                }
            }
        },
        uglify: {
            dist: {
                files: {
                'js/main.min.js': ['js/main.js']
            }
          }
        }
    });
    grunt.loadNpmTasks('grunt-contrib-compass');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-uglify');
};