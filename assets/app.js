/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// start the Stimulus application
import './bootstrap';

const $ = require('jquery')

require('bootstrap')
require('bootswatch/dist/slate/bootstrap.css')
require('open-iconic/font/css/open-iconic-bootstrap.css')

require('./css/app.css')

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})
