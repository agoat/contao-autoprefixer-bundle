var autoprefixer = require('./autoprefixer.js');
var data = '';

process.stdin.on('data', function(chunk) {
    data += chunk;
});

process.stdin.on('end', function() {
	data = JSON.parse(data);
	
	try {
		data.css = autoprefixer.process(data.css, { browsers: data.browsers }).css;
	} catch (e) {
		data.css = 'Error: ' + e.message;
	}

    process.stdout.write(JSON.stringify(data.css));
});
