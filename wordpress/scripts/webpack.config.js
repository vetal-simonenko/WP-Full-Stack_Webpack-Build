'use strict'

const webpack = require('webpack')
const autoprefixer = require('autoprefixer')
const AssetsPlugin = require('assets-webpack-plugin')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const Copy = require('copy-webpack-plugin')
const StyleLintPlugin = require('stylelint-webpack-plugin')
const CleanWebpackPlugin = require('clean-webpack-plugin')
const TerserPlugin = require('terser-webpack-plugin')
const FriendlyErrorsPlugin = require('friendly-errors-webpack-plugin')
const WriteFilePlugin = require('write-file-webpack-plugin')
const ImageminPlugin = require('imagemin-webpack')
const path = require('path')
const fs = require('fs')
const folderName = path.basename(path.resolve(process.cwd()))

const appDirectory = fs.realpathSync(process.cwd())

function resolveApp (relativePath) {
  return path.resolve(appDirectory, relativePath)
}

const DEV = process.env.NODE_ENV === 'development'
const DEV_PUBLIC_PATH = `http://localhost:4000/wp-content/themes/${folderName}/build/`

const paths = {
  appSrc: resolveApp('src'),
  appBuild: resolveApp('build'),
  appIndexJs: resolveApp('src/js/main.js'),
  appNodeModules: resolveApp('node_modules'),
  stylelintConfig: resolveApp('stylelint.config.js'),
  appStyles: resolveApp('src/styles/style.scss')
}
module.exports = {
  mode: DEV ? 'development' : 'production',
  // We generate sourcemaps in production. This is slow but gives good results.
  // You can exclude the *.map files from the build during deployment.
  target: 'web',
  watch: DEV,
  devtool: DEV ? 'cheap-eval-source-map' : false,
  entry: [paths.appIndexJs, paths.appStyles],
  output: {
    path: paths.appBuild,
    publicPath: DEV ? DEV_PUBLIC_PATH : '',
    hotUpdateChunkFilename: 'hot/hot-update.js',
    hotUpdateMainFilename: 'hot/hot-update.json',
    filename: DEV ? 'bundle.js' : 'bundle.[hash:8].js'
  },
  module: {
    rules: [
      {
        enforce: 'pre',
        test: /\.js$/,
        exclude: /node_modules/,
        loader: 'eslint-loader',
        include: paths.appSrc
      },
      {
        test: /\.js?$/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: [['@babel/preset-env', {
              useBuiltIns: 'usage',
              corejs: 'core-js@3'
            }]]
          }
        },
        exclude: /node_modules\/(?!(dom7|swiper)\/).*/,
        include: paths.appSrc
      },
      {
        test: /.scss$/,
        use: [{
          loader: DEV ? 'style-loader' : MiniCssExtractPlugin.loader,
          options: {
            hmr: true,
            sourceMap: DEV
          }
        },
        {
          loader: 'css-loader',
          options: {
            sourceMap: DEV
          }
        },
        {
          loader: 'postcss-loader',
          options: {
            sourceMap: DEV,
            ident: 'postcss',
            plugins: () => [
              autoprefixer()
            ]
          }
        },
        {
          loader: 'sass-loader',
          options: {
            sourceMap: DEV
          }
        }
        ]
      },
      {
        test: /\.(woff(2)?|ttf|eot|svg)(\?v=\d+\.\d+\.\d+)?$/,
        use: [{
          loader: 'file-loader',
          options: {
            name: '[name].[ext]',
            outputPath: './fonts/',
            publicPath: DEV ? `${DEV_PUBLIC_PATH}fonts` : './fonts/'
          }
        }]
      },
      {
        test: /.(png|jpe?g|gif|svg)$/i,
        use: [{
          loader: 'file-loader',
          options: {
            name: '[name].[ext]',
            outputPath: './images/',
            publicPath: DEV ? `${DEV_PUBLIC_PATH}images` : './images/'
          }
        }, {
          loader: 'image-webpack-loader'
        }]
      }
    ]
  },
  plugins: [
    DEV && new webpack.HotModuleReplacementPlugin(),
    !DEV && new CleanWebpackPlugin(['build'], {
      root: path.join(__dirname, '..'),
      cleanStaleWebpackAssets: false
    }),
    !DEV && new MiniCssExtractPlugin({
      filename: 'bundle.[hash:8].css'
    }),
    !DEV && new TerserPlugin({
      terserOptions: {
        compress: {
          warnings: false
        },
        output: {
          comments: false
        }
      }
    }),
    DEV && new webpack.NoEmitOnErrorsPlugin(),
    DEV && new WriteFilePlugin(),
    new webpack.EnvironmentPlugin({
      NODE_ENV: 'development',
      DEBUG: false
    }),
    new AssetsPlugin({
      path: paths.appBuild,
      filename: 'assets.json',
      fullPath: false
    }),
    DEV &&
    new FriendlyErrorsPlugin({
      clearConsole: false
    }),
    DEV &&
    new StyleLintPlugin({
      configFile: paths.stylelintConfig,
      context: paths.appStyles,
      syntax: 'scss',
      lintDirtyModulesOnly: true
    }),
    new Copy([{
      from: 'src/images',
      to: `${paths.appBuild}/images`,
      copyUnmodified: true
    }, {
      from: 'src/media',
      to: `${paths.appBuild}/media`,
      copyUnmodified: true
    }]),
    !DEV && new ImageminPlugin({
      bail: false,
      cache: false,
      name: '[name].[ext]',
      imageminOptions: {
        plugins: [
          ['jpegtran', {
            progressive: true
          }],
          ['optipng', {
            optimizationLevel: 5
          }],
          [
            'svgo',
            {
              plugins: [{
                removeViewBox: false
              }]
            }
          ]
        ]
      }
    }),
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery'
    })

  ].filter(Boolean)
}
