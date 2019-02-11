const fs = require('fs')
const Encore = require('@symfony/webpack-encore')
const CopyWebpackPlugin = require('copy-webpack-plugin');

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
     */
    // .createSharedEntry('js/common', ['jquery'])
    .addEntry('app', './assets/js/app.js')
    .addEntry('map', './assets/js/map.js')
    .addEntry('map2', './assets/js/map2.js')
    .addEntry('map3', './assets/js/map3.js')
    .addEntry('export', './assets/js/export.js')
    .addEntry('maxfields', './assets/js/maxfields.js')
    .addEntry('paginator', './assets/js/paginator.js')

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    // .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(false)//Encore.isProduction())

    .autoProvidejQuery()

    .addPlugin(new CopyWebpackPlugin([
        { from: './assets/img', to: 'img' }
    ]))
;

let config = Encore.getWebpackConfig()

if (!Encore.isProduction()) {
    fs.writeFile('fakewebpack.config.js', 'module.exports = ' + JSON.stringify(config), function (err) {
        if (err) {
            return console.log(err)
        }
        console.log('fakewebpack.config.js written')
    })
}

module.exports = config
