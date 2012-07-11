<?php

function getDirectoryList ($directory)
{

	// create an array to hold directory list
	$results = array();

	// create a handler for the directory
	$handler = opendir($directory);

	// open directory and walk through the filenames
	while ($file = readdir($handler)) {

		// if file isn't this directory or its parent, add it to the results
		if ($file != "." && $file != "..") {
			$results[] = $directory . '/' . $file;
		}

	}

	// tidy up: close the handler
	closedir($handler);

	// done!
	return $results;

}

if($argc > 1){
	$files = array();
	array_shift($argv);
	foreach($argv as $file){
		$files[] = dirname(__FILE__) . '/benchs/' . $file;
	}
}
else{
	$files = getDirectoryList(realpath(dirname(__FILE__) . '/benchs'));
}


$results_dir = dirname(__FILE__) . '/results/';
foreach($files as $file){
	$_file = basename($file);
	$result = $results_dir . rtrim($_file, '.php');
	$cmd = "time php $file > $result";
	echo $cmd, PHP_EOL;
	`$cmd`;
}