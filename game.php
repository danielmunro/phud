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

$dry_run = isset($argv[1]) && $argv[1] === '--dry-run';

// initiate and run the server
$s = new Server($address, $port);
$s->deployEnvironment('deploy');
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
