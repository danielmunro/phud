<?php

// Define the project path for file inclusion
$global_path = __DIR__;

// Ensure that the script doesn't die from timeout
set_time_limit(0);

// autoloader
spl_autoload_register(function($class) use ($global_path) {
	$class = str_replace(['Phud\\', '\\'], ['', '/'], $class); // hack for now
	$path = $global_path.'/lib/'.$class.".php";
	if(file_exists($path)) {
		require_once($path);
	}
});

require_once(__DIR__.'/vendor/beehive/autoloader.php');

// Set default arguments for starting phud, then parse
// through any command line args that were passed
// when starting the game.
$config = [
	'deploy' => 'deploy',
	'lib' => 'lib',
	'host' => '127.0.0.1',
	'port' => '9000'
];

$arg_count = count($argv);
for($i = 1; $i < $arg_count; $i++) {
	if(isset($config[$argv[$i]])) {
		$config[$argv[$i]] = $argv[$i+1];
		$i++;
	}
}

(new Phud\Server($config))->run();

function chance() {
	return rand(0, 10000) / 10000;
}

function _range($min, $max, $n) {
	return $min > $n ? $min : ($max < $n ? $max : $n);
}
