<?php

spl_autoload_register(function($class) {
	$class = str_replace(['Phud\\', '\\'], ['', '/'], $class);
	$path = __DIR__.'/lib/'.$class.".php";
	if(file_exists($path)) {
		require_once($path);
	}
});

require_once(__DIR__.'/vendor/beehive/autoloader.php');
require_once(__DIR__.'/vendor/onit/autoloader.php');
