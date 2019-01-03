const gulp = require("gulp");
const sass = require("gulp-sass");
const webpack = require("webpack");
const browserSync = require("browser-sync").create();
const urlToPreview = "http://bickle.test";

function reload(done) {
  browserSync.reload();
  done();
}

function css() {
  return gulp
    .src("./assets/scss/main.scss")
    .pipe(sass({ outputStyle: "compressed" }))
    .on("error", sass.logError)
    .pipe(gulp.dest("./assets/css"))
    .pipe(browserSync.stream());
}

function js(done) {
  webpack(require("./webpack.config.js"), (err, stats) => {
    if (err) {
      console.log(err.toString());
    }

    // console.log(stats.toString());
    reload(done);
  });
}

gulp.task("watch", () => {
  browserSync.init({
    notify: false,
    proxy: urlToPreview
  });

  gulp.watch("./assets/scss/**/*.scss", css);

  gulp.watch(["./assets/js/modules/**/*.js", "./assets/js/main.js"], js);

  gulp.watch("./**/*.php", reload);
});

gulp.task("build", gulp.parallel(css, js));
