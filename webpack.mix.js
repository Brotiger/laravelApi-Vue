const mix = require('laravel-mix');
let productionSourceMaps = false;
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
mix.webpackConfig({
    module: {
       rules: [
          {
             test: /\.pug$/,
             oneOf: [
                {
                   resourceQuery: /^\?vue/,
                   use: ['pug-plain-loader']
                },
                {
                   use: ['raw-loader', 'pug-plain-loader']
                }
             ]
          }
       ]
    }
 });

mix.js('resources/js/app.js', 'public/js')
.sass('resources/sass/app.scss', 'public/css').sourceMaps(productionSourceMaps).vue(); //sourceMaps(false) - false - не делать карту ресурсов при сборке под прод