/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
//import './styles/app.css';

// start the Stimulus application
import './bootstrap';

const $ = require('jquery')

require('bootstrap')
// require('bootstrap/dist/css/bootstrap.css')
require('bootswatch/dist/slate/bootstrap.css')
require('open-iconic/font/css/open-iconic-bootstrap.css')

// any CSS you require will output into a single css file (app.css in this case)
require('./css/app.css')

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})
