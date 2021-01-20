var autoprefixer = require('./autoprefixer.js').autoprefixer;
var data = '';

process.stdin.on('data', function(chunk) {
    data += chunk;
});

process.stdin.on('end', function() {
	data = JSON.parse(data);

	try {
		data.css = autoprefixer.process(data.css, {}, { browsers: data['browsers'], flexbox: data['flex'], grid: data['grid'], remove: data['remove'], supports: data['supports'] }).css;
	} catch (e) {
		data.css = 'Error: ' + e.message;
	}

  process.stdout.write(JSON.stringify(data.css));
});
