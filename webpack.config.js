// https://www.npmjs.com/package/webpack-ftp-upload-plugin
// base path /home/priadigi/

const path = require("path");
const webpack = require("webpack");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const argv = require("yargs").argv;
const WebpackOnBuildPlugin = require("on-build-webpack");
const glob = require("glob");
const WebpackBuildNotifierPlugin = require("webpack-build-notifier");
const fs = require("fs");

const UglifyJsPlugin = require("uglifyjs-webpack-plugin");
const BundleAnalyzerPlugin = require("webpack-bundle-analyzer")
    .BundleAnalyzerPlugin;
const SpeedMeasurePlugin = require("speed-measure-webpack-plugin");
const WebpackFtpUpload = require("webpack-ftp-upload-plugin");
const smp = new SpeedMeasurePlugin();
var colors = require("colors/safe");

let count_sass_file = 0;
let count_js_file = 0;
const isDev = argv.mode === "development";
const isProd = !isDev;

console.log(isProd);

let file_obj = {
    // "js/_inventarization": "./resources/js/inventarization.js",
    // "css/_inventarization": "./resources/styles/sass/inventarization.sass",

    // "js/profile": "./resources/js/profile.js",
    // "css/profile.min": "./resources/styles/sass/profile.sass",

    "js/index": "./resources/js/index.js",
    "css/home.min": "./resources/styles/sass/home.sass",
    "css/home_critical.min": "./resources/styles/sass/home_critical.sass",

    // "js/user_active": "./resources/js/user_active.js",

    // "js/products": "./resources/js/products.js",
    // "css/products.min": "./resources/styles/sass/products.sass",

    // "js/contact": "./resources/js/contact.js",
    // "css/contact.min": "./resources/styles/sass/contact.sass",

    // "js/our_pharmacies": "./resources/js/our_pharmacies.js",
    // "css/our_pharmacies.min": "./resources/styles/sass/our_pharmacies.sass",

    // "js/blog": "./resources/js/blog.js",
    // "css/blog.min": "./resources/styles/sass/blog.sass",

    // "js/discount_products": "./resources/js/discount_products.js",
    // "css/discount_products.min": "./resources/styles/sass/discount_products.sass",

    // "js/cart_single_page": "./resources/js/cart_single_page.js",
    // "css/cart.min": "./resources/styles/sass/cart.sass",

    // "js/search": "./resources/js/search.js",
    // "css/search.min": "./resources/styles/sass/search.sass",

    // "js/single_product": "./resources/js/single_product.js",
    // "css/single_product.min": "./resources/styles/sass/single_product.sass"

    // "js/404": "./resources/js/404.js",
    // "css/404.min": "./resources/styles/sass/404.sass"
};

const entry_d = false;

function add_file_entry(path_prod, msg) {
    glob(path_prod[0] + "/*.sass", function(er, files) {
        if (count_sass_file < files.length) {
            count_sass_file = files.length;
            files.forEach(file => {
                let path_buf = file.split("/"),
                    extension = path_buf[path_buf.length - 1].split("."),
                    key_buf = `css/${extension[0]}.min`;
                file_obj[
                    key_buf
                ] = `${path_prod[0]}/${extension[0]}.${extension[1]}`;
            });
        } else {
            console.log("----Not new sass file----");
        }
    });

    glob(path_prod[1] + "/*.js", function(er, files) {
        if (count_js_file < files.length) {
            count_js_file = files.length;
            files.forEach(file => {
                let path_buf = file.split("/"),
                    extension = path_buf[path_buf.length - 1].split("."),
                    key_buf = `js/${extension[0]}`;
                file_obj[
                    key_buf
                ] = `${path_prod[1]}/${extension[0]}.${extension[1]}`;
            });
        } else {
            console.log("----Not new js file----");
        }
    });

    console.log("=====file=======");
    console.log(file_obj);
    console.log("================");
}

function del_file_folder(path_prod, extension) {
    let unlinked = [];
    glob(`${path_prod}/*.${extension}`, function(er, files) {
        files.forEach(file => {
            fs.unlinkSync(path.resolve(file));
            unlinked.push(file);
        });
    });
    if (unlinked.length > 0) {
        console.log("================");
        console.log("Removed files: ", unlinked);
        console.log("================");
    }
}

const optimization = () => {
    const config = {
        splitChunks: {
            cacheGroups: {
                vendor: {
                    chunks: "initial",
                    name: "vendor",
                    test: "vendor",
                    enforce: true
                }
            }
        },
        runtimeChunk: true
    };

    if (isProd) {
        config.minimizer = [
            //new OptimizeCssAssetWebpackPlugin(),
            //new TerserWebpackPlugin(),
            new UglifyJsPlugin({
                test: /\.js($|\?)/i
            })
        ];
    }

    return config;
};

const plugins = () => {
    const plag = [
        new MiniCssExtractPlugin({
            filename: "[name].css"
        }),

        new WebpackOnBuildPlugin(async function(stats) {
            if (entry_d === true) {
                await add_file_entry(
                    [`./resources/styles/sass`, `./resources/js`],
                    "Search file: "
                );
                //const newlyCreatedAssets = stats.compilation.assets;
            }
            await del_file_folder("./public/css", "min.js");
            let date = new Date();
            console.log("=====================Time=====================");
            console.log(
                "Cборка окончена в:",
                colors.green(
                    `${date.getHours()}:${date.getMinutes()}:${date.getSeconds()}`
                )
            );
            console.log("==============================================");
        }),

        new WebpackBuildNotifierPlugin({
            title: "My Project Webpack Build",
            suppressSuccess: true
        })
    ];

    // new WebpackFtpUpload({
    //   host: '',
    //   port: '21',
    //   username: 'root',
    //   password: '123456',
    //   local: path.join(__dirname, 'dist'),
    //   path: '/home/priadigital/',
    // })

    if (argv.tests === "test") {
        plag.push(new BundleAnalyzerPlugin());
    }

    return plag;
};

const babelOptions = preset => {
    const opts = {
        presets: ["@babel/preset-env"],
        plugins: [
            "@babel/proposal-class-properties",
            "@babel/plugin-proposal-object-rest-spread",
            "@babel/plugin-syntax-dynamic-import"
        ]
    };

    if (preset) {
        opts.presets.push(preset);
    }

    return opts;
};

module.exports = smp.wrap({
    entry: () => file_obj,
    mode: argv.mode,
    watch: true,
    output: {
        path: __dirname + "/public/",
        filename: "[name].js"
    },
    optimization: optimization(),
    plugins: plugins(),
    module: {
        rules: [
            {
                enforce: "pre",
                test: /\.(js|jsx|ts)$/,
                exclude: /(node_modules|bower_components)/,
                loader: "eslint-loader"
            },
            {
                test: /\.(js|jsx)$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: "babel-loader",
                    //options: babelOptions('@babel/preset-react')
                    options: {
                        presets: ["@babel/preset-env", "@babel/react"],
                        plugins: [
                            "@babel/proposal-class-properties",
                            "@babel/plugin-proposal-object-rest-spread",
                            "@babel/plugin-syntax-dynamic-import"
                        ]
                    }
                }
            },
            {
                test: /\.(jsx)$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: "babel-loader",
                    options: babelOptions("@babel/preset-react")
                }
            },
            {
                test: /\.ts$/,
                exclude: /node_modules/,
                loader: {
                    loader: "babel-loader",
                    options: babelOptions("@babel/preset-typescript")
                }
            },
            {
                test: /\.s[ac]ss$/,
                use: [
                    "style-loader",
                    MiniCssExtractPlugin.loader,
                    // ExtractTextPlugin.loader,
                    {
                        loader: "css-loader",
                        options: { sourceMap: true }
                    },
                    {
                        loader: "postcss-loader",
                        options: {
                            sourceMap: true,
                            config: { path: `./postcss.config.js` }
                        }
                    },
                    {
                        loader: "sass-loader",
                        options: { sourceMap: true }
                    }
                ]
            },
            {
                test: /\.css$/,
                use: [
                    "style-loader",
                    MiniCssExtractPlugin.loader,
                    // ExtractTextPlugin.loader,
                    {
                        loader: "css-loader",
                        options: { sourceMap: true }
                    },
                    {
                        loader: "postcss-loader",
                        options: {
                            sourceMap: true,
                            config: { path: `./postcss.config.js` }
                        }
                    }
                ]
            },
            {
                test: /\.(png|jpe?g|gif|svg)$/i,
                use: [
                    {
                        loader: "file-loader"
                    }
                ]
            }
        ]
    }
});
