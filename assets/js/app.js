/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

const $ = require('jquery')

require('bootstrap')
require('bootstrap/dist/css/bootstrap.css')

require('open-iconic/font/css/open-iconic-bootstrap.css')

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css')

const g11n = require('g11n-js/dist/mloader-g11n')

$(function() {
    g11n.loadJsonData($('#g11n-setup').attr('data-g11n-setup'))
});
