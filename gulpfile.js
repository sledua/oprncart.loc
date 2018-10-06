var gulp           = require('gulp'),
		gutil          = require('gulp-util' ),
		sass           = require('gulp-sass'),
		browserSync    = require('browser-sync'),
		cleanCSS       = require('gulp-clean-css'),
		autoprefixer   = require('gulp-autoprefixer'),
		bourbon        = require('node-bourbon'),
		ftp            = require('vinyl-ftp');

gulp.task('browser-sync', function() {
	browserSync({
		proxy: "oprncart.loc",
		notify: false
	});
});
gulp.task('sass', function() {
	return gulp.src('catalog/view/theme/raspilok/stylesheet/stylesheet.sass')
		.pipe(sass({
			includePaths: bourbon.includePaths
		}).on('error', sass.logError))
		.pipe(autoprefixer(['last 15 versions']))
		.pipe(cleanCSS())
		.pipe(gulp.dest('catalog/view/theme/raspilok/stylesheet/'))
		.pipe(browserSync.reload({stream: true}))
});
gulp.task('watch', ['sass', 'browser-sync'], function() {
	gulp.watch('catalog/view/theme/raspilok/stylesheet/stylesheet.sass', ['sass']);
	gulp.watch('catalog/view/theme/raspilok/template/**/*.twig', browserSync.reload);
	gulp.watch('catalog/view/theme/raspilok/js/**/*.js', browserSync.reload);
	gulp.watch('catalog/view/theme/raspilok/libs/**/*', browserSync.reload);
});

// Выгрузка изменений на хостинг
gulp.task('deploy', function() {
	var conn = ftp.create({
		host:      'hostname.com',
		user:      'username',
		password:  'userpassword',
		parallel:  10,
		log: gutil.log
	});
	var globs = [
	'catalog/view/theme/raspilok/**'
	];
	return gulp.src(globs, {buffer: false})
	.pipe(conn.dest('/path/to/folder/on/server'));
});

gulp.task('default', ['watch']);
