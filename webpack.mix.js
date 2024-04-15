const mix = require("laravel-mix");
require("laravel-mix-serve");

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

mix.js("resources/js/*", "public/js")
    .sass("resources/css/app.scss", "public/css")
    .options({
        postCss: [require("tailwindcss")],
    }); //.bladeReload();



mix.browserSync({
    proxy: {
        target: "localhost", // route to the nginx instance running
        proxyReq: [
            function (proxyReq, req, res) {

                proxyReq.setHeader("Host", req.headers.host); // Allows us to access the hot reload at something like subdomain.bulsca.local:3000 or without the port for non-hot realod
            },
        ],
        
    },
    host: "0.0.0.0",
});

