import gulp from 'gulp';
import sass from 'gulp-dart-sass';
import compiler from 'sass';
import cssnano from 'cssnano';
import autoprefixer from 'autoprefixer';
import inlineSVG from 'postcss-inline-svg'; // https://github.com/TrySound/postcss-inline-svg
import rtlcss from 'rtlcss';
import log from 'fancy-log';
import plumber from 'gulp-plumber';
import postcss from 'gulp-postcss';
import rename from 'gulp-rename';
import livereload from 'gulp-livereload';
import sassUnicode from 'gulp-sass-unicode';
import sassVariables from 'gulp-sass-variables'; // https://github.com/osaton/gulp-sass-variables
import { deleteSync } from 'del';
// import { readFile } from 'fs/promises';

// @REF: https://www.stefanjudis.com/snippets/how-to-import-json-files-in-es-modules-node-js/
import { createRequire } from 'module';
const require = createRequire(import.meta.url);

const { src, dest, watch, series, parallel, task } = gulp;

// @REF: https://www.stefanjudis.com/snippets/how-to-import-json-files-in-es-modules-node-js/
// const conf = JSON.parse(await readFile(new URL('./gulp.config.json', import.meta.url))); // eslint-disable-line
// const pkg = JSON.parse(await readFile(new URL('./package.json', import.meta.url))); // eslint-disable-line

const conf = require('./gulp.config.json');
// const pkg = require('./package.json');

function clean (done) {
  deleteSync(conf.input.clean);
  done();
}

function devSass () {
  return src(conf.input.sass)
    .pipe(plumber())
    .pipe(sassVariables(conf.theme.variables))
    .pipe(sass(conf.sass).on('error', sass.logError))
    .pipe(sassUnicode())
    .pipe(postcss([
      inlineSVG(),
      cssnano(conf.cssnano.dev),
      autoprefixer(conf.autoprefixer.dev)
    ]))
    .pipe(dest(conf.output.css))
    .pipe(postcss([
      rtlcss()
    ]))
    .pipe(rename({ suffix: '-rtl' }))
    .pipe(dest(conf.output.css)).on('error', log.error);
}

function devReload (done) {
  livereload.reload();
  done();
}

function devWatch () {
  livereload.listen();
  return watch(conf.input.watch, { ignoreInitial: false }, series(devSass, devReload));
}

function buildSass () {
  return src(conf.input.sass)
    .pipe(sassVariables(conf.theme.variables))
    .pipe(sass(conf.sass).on('error', sass.logError))
    .pipe(sassUnicode())
    .pipe(postcss([
      inlineSVG(),
      cssnano(conf.cssnano.build),
      autoprefixer(conf.autoprefixer.build)
    ]))
    .pipe(dest(conf.output.css)).on('error', log.error);
}

function buildSassRTL () {
  return src(conf.input.sass)
    .pipe(sassVariables(conf.theme.variables))
    .pipe(sass(conf.sass).on('error', sass.logError))
    .pipe(sassUnicode())
    .pipe(postcss([
      rtlcss(),
      inlineSVG(),
      cssnano(conf.cssnano.build),
      autoprefixer(conf.autoprefixer.build)
    ]))
    .pipe(rename({ suffix: '-rtl' }))
    .pipe(dest(conf.output.css)).on('error', log.error);
}

task('default', function (done) {
  log.info('Hi, I\'m Gulp!');
  log.info('Sass is:\n' + compiler.info);
  done();
});

task('sass', devSass);
task('watch', devWatch);
task('build', parallel(buildSass, buildSassRTL));
task('clean', clean);
