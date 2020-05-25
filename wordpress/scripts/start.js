'use strict'

const SERVER_ADDRESS = 'http://localhost:9009'

const webpack = require('webpack')
const config = require('./webpack.config')
const browserSync = require('browser-sync').create()
const webpackDevMiddleware = require('webpack-dev-middleware')
const webpackHotMiddleware = require('webpack-hot-middleware')
const clientCompiler = webpack(config)
config.entry.push('webpack-hot-middleware/client')
browserSync.init({
  notify: false,
  host: 'localhost',
  port: 4000,
  files: ['**/*.php', 'src/js/**/*.js'],
  proxy: {
    target: SERVER_ADDRESS,
    middleware: [
      // converts browsersync into a webpack-dev-server
      webpackDevMiddleware(clientCompiler, {
        path: config.output.path,
        noInfo: true
      }),

      // hot update js && css
      webpackHotMiddleware(clientCompiler)
    ]
  }
})
