const mix = require('laravel-mix');
//require('laravel-mix-blade-reload');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

 mix.js('resources/js/*', 'public/js')
 .sass('resources/css/app.scss', 'public/css').options({
     postCss: [require("tailwindcss")],
 });//.bladeReload();

mix.browserSync('127.0.0.1:8000')
