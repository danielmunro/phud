<?php

///////////////////////////////////////////////////////
// ADMIN CONFIG
///////////////////////////////////////////////////////

// Define a relative project path for file inclusion
$global_path = dirname(__FILE__);

// Server settings
$address = '127.0.0.1';
$port = 9000;

$config = $global_path.'/config.php';
if(file_exists($config)) {
	require_once($config);
}

// Misc
date_default_timezone_set('America/Los_Angeles');

///////////////////////////////////////////////////////
// END admin config - nothing below here needs changing
// in order to run
///////////////////////////////////////////////////////


// Ensure that the script doesn't die from timeout
set_time_limit(0);

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
$s = new Phud\Server($address, $port);
if($s->isInitialized()) {
	$s->deployEnvironment($deploy);
	if(!$dry_run) {
		$s->run();
	}
}

// autoloader
function __autoload($class) {
	global $global_path;
	$class = str_replace(['Phud\\', '\\'], ['', '/'], $class); // hack for now
	$path = $global_path.'/lib/'.$class.".php";
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
