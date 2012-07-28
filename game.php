<?php

// Define the project path for file inclusion
$global_path = __DIR__;

// Ensure that the script doesn't die from timeout
set_time_limit(0);

// Set default arguments for starting phud, then parse
// through any command line args that were passed
// when starting the game.
$dry_run = false;
$deploy = 'deploy';
$lib = 'lib';
$address = '127.0.0.1';
$port = 9000;

foreach($argv as $i => $arg) {
	switch($arg) {
		case '--dry-run':
			$dry_run = true;
			break;
		case '--deploy':
			$deploy = $argv[$i+1];
			array_splice($argv, $i+1, 1);
			break;
		case '--lib':
			$lib = $argv[$i+1];
			array_splice($argv, $i+1, 1);
			break;
		case '--address':
			$address = $argv[$i+1];
			array_splice($argv, $i+1, 1);
			break;
		case '--port':
			$port = $argv[$i+1];
			array_splice($argv, $i+1, 1);
			break;
	}
}

// autoloader
spl_autoload_register(function($class) use ($global_path) {
	$class = str_replace(['Phud\\', '\\'], ['', '/'], $class); // hack for now
	$path = $global_path.'/lib/'.$class.".php";
	if(file_exists($path)) {
		require_once($path);
	}
});

// initiate and run the server
$s = Phud\Server::instance();
if($s->isInitialized()) {
	$s->deployEnvironment($lib, $deploy);
	if(!$dry_run) {
		$s->run();
	}
}

function chance()
{
	return rand(0, 10000) / 10000;
}

function _range($min, $max, $n)
{
	return $min > $n ? $min : ($max < $n ? $max : $n);
}
