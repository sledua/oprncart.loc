var gulp = require('gulp');
var sourcemaps = require('gulp-sourcemaps');
var browserSync = require("browser-sync");
var path = require("path");
var concat = require("gulp-concat");
const babel = require('gulp-babel');
var uglify = require('gulp-uglify');
var pump = require('pump');
var rename = require("gulp-rename");
var sassDest = '../../../theme/default/stylesheet/sawmill/';
var scriptDest = '../';

var baseDir = path.resolve(__dirname, "../../../../");
if (typeof process.env.HOST === "undefined") {
	process.env.HOST = 'localhost';
}
var scripts_src = [
	scriptDest + 'main.js',
	scriptDest + 'actions/*.js',
	scriptDest + 'constants/*.js',
	scriptDest + 'components/*.js',
	scriptDest + 'components/**/*.js',
	scriptDest + 'elements/*.js',
	scriptDest + 'elements/**/*.js',
	scriptDest + 'getters/*.js',
	scriptDest + 'mutations/*.js',
	scriptDest + 'mutations/**/*.js',
	scriptDest + 'routes/*.js',
	scriptDest + 'routes/**/*.js',
	scriptDest + 'page/*.js',
	scriptDest + 'page/**/*.js'
];

gulp.task('scripts', function (cb) {
	pump([
		gulp.src(scripts_src),
		concat('sawmill.js'),
		babel({
			presets: ['@babel/env']
		}),
		// uglify(),
		gulp.dest(scriptDest + 'dist')
	], cb);
});
gulp.task('postCSS', function () {
	const postcss = require('gulp-postcss');
	return gulp.src(sassDest + '*.pcss')
		.pipe(sourcemaps.init())
		.pipe(postcss()).on('error', (e)=>console.log(e.message))
		.pipe(require('gulp-autoprefixer')({ browsers : ['last 15 versions'] }))
		.pipe(rename((path)=>{
			path.extname = ".css";
		}))
		.pipe(sourcemaps.write('./'))
		.pipe(gulp.dest(sassDest))
		.pipe(browserSync.reload({stream: true}));
});

gulp.task('sass:watch', function () {
	gulp.watch([sassDest + '*.pcss', sassDest + '**/*.*'], ['postCSS']);
});
gulp.task('scripts:watch', function () {
	gulp.watch(
		scripts_src, ['scripts']
	);
	gulp.watch(scriptDest + 'dist/**/*.js', browserSync.reload);
});
gulp.task("browser_sync_init", function () {
	if (typeof process.env.HOST !== "undefined") {
		browserSync({
			proxy: process.env.HOST,
			port:3006
		});
	}
});

gulp.task('default', ["browser_sync_init"], function () {
	if (typeof process.env.HOST !== "undefined") {

		gulp.watch([
			baseDir + "/controller/extension/**/**/*.php",
			baseDir + "/view/theme/default/template/extension/**/**/*.vue",
			baseDir + "/view/theme/defaulttemplate/extension/**/**/*.twig"
		], browserSync.reload);
	}
	gulp.start(["postCSS", "sass:watch", "scripts", "scripts:watch"]);
});