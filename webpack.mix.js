const mix = require('laravel-mix');

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
mix.setPublicPath('public');

mix.js('resources/js/app.js', 'dist/js')
    .options({
        fileLoaderDirs: {
            images: 'dist/images',
            fonts: 'dist/fonts'
        }
    })
    .vue()
    .postCss('resources/css/app.css', 'dist/css', [
        require('postcss-import'),
        require('tailwindcss'),
    ])
    .webpackConfig(require('./webpack.config'))
    .sourceMaps();

if (mix.inProduction()) {
    mix.version();
}
