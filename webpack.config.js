var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    .cleanupOutputBeforeBuild()

    .enableSourceMaps(!Encore.isProduction())

    // uncomment to create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())

    // uncomment to define the assets of the project

    .addEntry('asset/template/base', './assets/js/base.js')
    .addEntry('asset/testAjax', './assets/js/testAjax.js')
    .addEntry('asset/question/addQuestion', './assets/js/addQuestion.js')
    .addEntry('asset/answer/addOrEditAnswer', './assets/js/addOrEditAnswer.js')
    .addEntry('asset/login/login', './assets/js/login.js')
    .addEntry('asset/quizz/userList', './assets/js/userQuizzList.js')
    .addEntry('asset/quizz/detailQuizz', './assets/js/detailQuizz.js')
    
    
    //.addStyleEntry('css/app', './assets/css/base.css')

    // uncomment if you use Sass/SCSS files
    // .enableSassLoader()

    // uncomment for legacy applications that require $/jQuery as a global variable
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
