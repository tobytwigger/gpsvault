const mix = require('laravel-mix');
const VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin');

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
    .css('resources/css/app.css', 'dist/css')
    .webpackConfig(require('./webpack.config'))
    .sourceMaps()
    .copy('resources/images', 'public/dist/images');

mix.webpackConfig({
    plugins: [
        new VuetifyLoaderPlugin()
    ]
})

if (mix.inProduction()) {
    mix.version();
}
