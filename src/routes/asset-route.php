<?php
/** @var \Base $app */

/**
*	Minify file(s) route
* 	Use: /minify/css?files=file1.js,file2.js
*	@param $type file type (css, js)
*	@param $files array of all files path
**/
$app->route('GET /minify/@type', // @type will make `PARAMS.type` variable base point to the correct path
	function($app, $args) {
		$type = $args['type'];
		if (!in_array($type, ['js', 'css'])) { // Validate type
            $app->error(404);
            return;
        }

		$files = $_GET['files'] ?? '';
		if (preg_match('/(?<!\.' . $type . ')(,|$)/', $files)) { // Regex validate file extension
            $app->error(403); return;
        }

		$files = preg_replace('/(\.+\/)/','',$files); // close potential hacking attempts
		$path = $app->get('ROOT') . '/';
		echo Web::instance()->minify($files, null, true, $path); // minify will grab each file specified in the querystring var named 'files' and combine into 1 output
	},
	// 3600*24 // Save the minified file in F3 cache for 24 hours. future requests for this route will use cached version
);

/**
*	Load file route
* 	Use: /load/js?file=file.js
*	@param $type file type (css, js)
*	@param $src file path
**/
$app->route('GET /load/@type',
	function($app, $args) {
		$type = $args['type'];
		if (!in_array($type, ['js', 'css'])) { // Validate type
            $app->error(404);
            return;
        }

		$file = $_GET['src'] ?? '';
		if (pathinfo($file, PATHINFO_EXTENSION) !== $type) { // Validate file extension
            $app->error(403);
            return;
        }

		$file = preg_replace('/(\.+\/)/','',$file); // close potential hacking attempts
		$path = $app->get('ROOT') . '/' . $file;
		if (!file_exists($path)) { // Check file exist
            $app->error(404);
        }
		
		Web::instance()->send($path, null, 0, false); // Send file with no force attachment header
	},
	// 3600*24
);
