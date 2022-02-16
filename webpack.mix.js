const mix = require('laravel-mix');
// const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
// const { cpus } = require('os');
const { join } = require('path');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    //.js('resources/js/main.js', 'public/js/main.js')
    //.js('resources/js/scripts.js', 'public/js/scripts.js')
    //.copy('resources/js/main.js', 'public/js/main.js')
    //.copy('resources/js/scripts.js', 'public/js/scripts.js')
    //.babel('public/js/main.js', 'public/js/main.js')
    //.babel('public/js/scripts.js', 'public/js/scripts.js')
    //.scripts('resources/js/main.js', 'public/js/main.js')
    .sass('resources/sass/main.sass', 'public/css')
    .sass('resources/sass/styles.sass', 'public/css')
    .options({
        autoprefixer: {
            options: {
                browsers: ['last 7 years']
            }
        }
    },
        {
            postCss: [
                require('postcss-css-variables')()
            ]
        })
    .webpackConfig({
    context: __dirname,

    entry: {
        app: './resources/js/app.js',
        vendor: ['react', 'react-dom'] // libs js
    },

    output: {
        filename: '[name].[chunkhash].js',
        chunkFilename: '[name].[chunkhash].js',
        path: join(__dirname, 'public/js')
    },
    mode: 'production',
    target: 'web',
    resolve: {
        extensions: ['.js']
    },

    module: {
        rules: [{
            test: /\.js(x)$/,
            exclude: /node_modules/,
            loader: 'babel-loader'
        }]
    },

});




