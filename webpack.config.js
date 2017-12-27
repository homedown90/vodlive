const webpack = require('webpack');
module.exports = {
    entry: __dirname + "/web/static-dist/app/js/index.js",
    output: {
        path: __dirname +"/web",
        filename: "bundle.js"
    },
    module:
        {
            rules:
                [
                    {
                        test: /(\.scss|\.css)$/,
                        use: [{
                            loader: 'style-loader', // inject CSS to page
                        }, {
                            loader: 'css-loader', // translates CSS into CommonJS modules
                        }, {
                            loader: 'postcss-loader', // Run post css actions
                            options: {
                                plugins: function () { // post css plugins, can be exported to postcss.config.js
                                    return [
                                        require('precss'),
                                        require('autoprefixer')
                                    ];
                                }
                            }
                        }, {
                            loader: 'sass-loader' // compiles SASS to CSS
                        }]
                    },
                ]
        },
    plugins: [
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
            Popper: ['popper.js', 'default'],
            Util: "exports-loader?Util!bootstrap/js/dist/util",
            Dropdown: "exports-loader?Dropdown!bootstrap/js/dist/dropdown",
        }),
    ]
}