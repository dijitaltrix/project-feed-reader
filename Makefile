watch:
	# refresh css and clear cached views
	(ls public/theme/src/scss/*.scss | entr -c make css &)
	# watch src/js folder
	(ls public/theme/src/js/site.js | entr -c make js &)
	# auto refresh browser
	#(browser-sync start --proxy http://localhost:8000 --host localhost --files "public/theme/css/*.css" &)

make css:
	sass public/theme/src/scss/screen.scss:public/theme/css/screen.min.css && rm -rf storage/cache/views/*

make js:
	uglifyjs public/theme/src/js/site.js --compress --mangle --output public/theme/js/site.min.js
