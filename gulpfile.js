const gulp      = require('gulp');
const { src, dest, series, parallel, watch } = gulp;
const del       = require('del');
const cleanCSS  = require('gulp-clean-css');
const concat    = require('gulp-concat');
const terser    = require('gulp-terser');
const sourcemaps = require('gulp-sourcemaps');

// --- Clean dist folder ---
function clean() {
  return del(['dist/**', '!dist']);
}

// --- Minify & concatenate CSS into dist/plugin-starter/assets/css ---
function minifyAndConcatCSS() {
  return src('assets/css/**/*.css')
    .pipe(sourcemaps.init())
    .pipe(concat('style.min.css'))
    .pipe(cleanCSS({ level: 2 }))
    .pipe(sourcemaps.write('.'))
    .pipe(dest('dist/plugin-starter/assets/css'));
}

// --- Minify & concatenate JS into dist/plugin-starter/assets/js ---
function minifyAndConcatJS() {
  return src('assets/js/**/*.js')
    .pipe(sourcemaps.init())
    .pipe(concat('scripts.min.js'))
    .pipe(terser())
    .pipe(sourcemaps.write('.'))
    .pipe(dest('dist/plugin-starter/assets/js'));
}

// --- Copy everything else (PHP, vendor, images, etc.) ---
function copyOtherFiles() {
  return src([
    '**/*',
    '!node_modules/**',
    '!tests/**',
    '!dist/**',
    '!.git/**',
    '!.github/**',
    '!gulpfile.js',
    '!rollup.config.js',
    '!package*.json',
    '!composer.*',
    '!phpunit.xml.dist',
    '!assets/css/**',   // we already process CSS separately
    '!assets/js/**'     // we already process JS separately
  ], { dot: true })
  .pipe(dest('dist/plugin-starter'));
}

// --- Watch for changes during development ---
function watchFiles() {
  watch('assets/css/**/*.css', minifyAndConcatCSS);
  watch('assets/js/**/*.js',  minifyAndConcatJS);
}

// --- Build task for production ---
exports.build = series(
  clean,
  parallel(minifyAndConcatCSS, minifyAndConcatJS, copyOtherFiles)
);

// --- Default task: build then watch (good for local dev) ---
exports.default = series(
  clean,
  parallel(minifyAndConcatCSS, minifyAndConcatJS, copyOtherFiles),
  watchFiles
);