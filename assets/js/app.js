/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
import $ from 'jquery';

// create global $ and jQuery variables
global.$ = global.jQuery = $;
import 'popper.js';
import 'bootstrap';
import 'cropperjs/dist/cropper.css';
require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');
import './crop_avatar';
import './app_old';

console.log('Hello Webpack Encore! Bolok est mortel !!!');
