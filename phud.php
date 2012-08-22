<?php

// Ensure that the script doesn't die from timeout
set_time_limit(0);

require_once(__DIR__.'/autoloader.php');
require_once(__DIR__.'/helpers.php');

// Set default arguments for starting phud, then parse
// through any command line args that were passed
// when starting the game.
$config = [
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

$server = new Phud\Server($config);

// deploy the game environment
$phud_library = 'lib';
$deploy_scripts = 'deploy';
(new Phud\Deploy($phud_library, $deploy_scripts))->deployEnvironment($server);

$server->run();
