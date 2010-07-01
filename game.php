#!/usr/bin/php5

<?php

	/**
	 *
	 * Phud - a PHP implementation of the popular multi-user dungeon game paradigm.
     * Copyright (C) 2009 Dan Munro
	 * 
     * This program is free software; you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation; either version 2 of the License, or
     * (at your option) any later version.
	 * 
     * This program is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     * GNU General Public License for more details.
	 * 
     * You should have received a copy of the GNU General Public License along
     * with this program; if not, write to the Free Software Foundation, Inc.,
     * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
	 *
	 * Contact Dan Munro at dan@danmunro.com
	 * @author Dan Munro
	 * @package Phud
	 *
	 */

	date_default_timezone_set('America/Los_Angeles');

	\Mechanics\Debug::newLog();
	
	// Load all commands
	$d = dir(dirname(__FILE__) . '/Commands');
	while($command = $d->read())
		if(strpos($command, '.php') !== false)
			\Mechanics\Command::instantiate(substr($command, 0, strpos($command, '.')));

	// Autoloader
	function __autoload($class)
	{
		if(strpos($class, "\\"))
		{
			list($namespace, $class) = explode("\\", $class);
			if(file_exists($namespace . '/' . $class . '.php'))
				return require_once($namespace . '/' . $class . '.php');
		}
		$dirs = array
		(
			'Mechanics/',
			'Living/',
			'Races/',
			'Items/',
			'',
			'Interfaces/',
			'Skills/',
			'Spells/',
			'Disciplines/'
		);
	
		foreach($dirs as $dir)
			if(file_exists($dir . $class . '.php'))
				return require_once($dir . $class . '.php');
	
	}

	\Mechanics\Server::start();

?>
