#!/usr/bin/php5
<?php
///////////////////////////////////////////////////////
// ADMIN CONFIG
///////////////////////////////////////////////////////

$address = '192.168.0.106';
$port = 9000;

///////////////////////////////////////////////////////
// END admin config - nothing below here needs changing
// in order to run
///////////////////////////////////////////////////////

date_default_timezone_set('America/Los_Angeles');
gc_enable();
set_time_limit(0);
use \Mechanics\Debug;
use \Mechanics\Server;
Debug::clearLog();

///////////////////////////////////////////////////////
// Set default arguments for starting phud, then parse
// through any command line args that were passed
// when starting the game.
///////////////////////////////////////////////////////

$dry_run = false;
$deploy = 'deploy';

foreach($argv as $i => $arg) {
	switch($arg) {
		case '--dry-run':
			$dry_run = true;
			break;
		case '--deploy':
			$deploy = $argv[$i+1];
			array_splice($argv, $i+1, 1);
			break;
	}
}

// initiate and run the server
$s = new Server($address, $port);
$s->deployEnvironment($deploy);
if(!$dry_run) {
	$s->run();
}

// autoloader
function __autoload($class) {
	$path = 'lib/'.str_replace("\\", "/", $class).".php";
	if(file_exists($path)) {
		require_once($path);
	}
}
	
function chance()
{
	return rand(0, 10000) / 100;
}

function _range($min, $max, $n)
{
	return $min > $n ? $min : ($max < $n ? $max : $n);
}
?>
