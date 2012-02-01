<?php

	// Autoloader
	function autoload($class)
	{
		list($namespace, $class) = explode("\\", $class);
		if(file_exists($namespace . '/' . $class . '.php'))
			require_once($namespace . '/' . $class . '.php');
	}
	spl_autoload_register('autoload');
	
?>
