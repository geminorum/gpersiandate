const { src, dest, watch, series, parallel } = require('gulp');
const $ = require('gulp-load-plugins')();
const cssnano = require('cssnano');
const autoprefixer = require('autoprefixer');
const rtlcss = require('rtlcss');
const log = require('fancy-log');
const del = require('del');

const conf = require('./gulp.config.json');
// const pkg = require('./package.json');

function clean (done) {
  del.sync(conf.input.clean);
  done();
}

function devSass () {
  return src(conf.input.sass)
    .pipe($.plumber())
    .pipe($.sassVariables(conf.theme.variables))
    .pipe($.sass(conf.sass).on('error', $.sass.logError))
    .pipe($.sassUnicode())
    .pipe($.postcss([
      cssnano(conf.cssnano.dev),
      autoprefixer(conf.autoprefixer.dev)
    ]))
    .pipe(dest(conf.output.css))
    .pipe($.postcss([
      rtlcss()
    ]))
    .pipe($.rename({ suffix: '-rtl' }))
    .pipe(dest(conf.output.css)).on('error', log.error);
}

function devReload (done) {
  $.livereload.reload();
  done();
}

function devWatch () {
  $.livereload.listen();
  return watch(conf.input.watch, { ignoreInitial: false }, series(devSass, devReload));
}

function buildSass () {
  return src(conf.input.sass)
    .pipe($.sassVariables(conf.theme.variables))
    .pipe($.sass(conf.sass).on('error', $.sass.logError))
    .pipe($.sassUnicode())
    .pipe($.postcss([
      cssnano(conf.cssnano.build),
      autoprefixer(conf.autoprefixer.build)
    ]))
    .pipe(dest(conf.output.css)).on('error', log.error);
}

function buildSassRTL () {
  return src(conf.input.sass)
    .pipe($.sassVariables(conf.theme.variables))
    .pipe($.sass(conf.sass).on('error', $.sass.logError))
    .pipe($.sassUnicode())
    .pipe($.postcss([
      rtlcss(),
      cssnano(conf.cssnano.build),
      autoprefixer(conf.autoprefixer.build)
    ]))
    .pipe($.rename({ suffix: '-rtl' }))
    .pipe(dest(conf.output.css)).on('error', log.error);
}

exports.default = function (done) {
  log.info('Hi, I\'m Gulp!');
  log.info('Sass is:\n' + require('node-sass').info);
  done();
};

exports.sass = devSass;
exports.watch = devWatch;
exports.build = parallel(buildSass, buildSassRTL);
exports.clean = clean;
