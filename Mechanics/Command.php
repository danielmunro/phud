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
	namespace Mechanics;
	abstract class Command
	{
	
		//public static $instances = array();
		protected $dispositions = array();
		
		protected function __construct() {}
		
		/**
		public static function instance()
		{
			$class = get_called_class();
			if(!isset(self::$instances[$class]))
				self::$instances[$class] = new $class();
			return self::$instances[$class];
		}
		*/
		
		public function runInstantiation()
		{
			$namespace = 'Commands';
			$d = dir(dirname(__FILE__) . '/../'.$namespace);
			while($command = $d->read())
				if(strpos($command, '.php') !== false)
				{
					$class = substr($command, 0, strpos($command, '.'));
					$called_class = $namespace.'\\'.$class;
					new $called_class();
				}
		}
	
		public function getDispositions()
		{
			return $this->dispositions;
		}
	
		abstract public function perform(\Mechanics\Actor $actor, $args = array());
	}
?>
