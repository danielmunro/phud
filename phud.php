<?php

// Ensure that the script doesn't die from timeout
set_time_limit(0);

require_once(__DIR__.'/autoloader.php');
require_once(__DIR__.'/helpers.php');

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
