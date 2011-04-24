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
	
		public static $instances = array();
	
		public static $aliases = array();
		
		public static function addAlias($command, $alias)
		{
			
			if(is_array($alias))
			{
				foreach($alias as $a)
					self::addAlias($command, $a);
				return;
			}
			
			if(isset(self::$aliases[$alias]))
				throw new \Exceptions\Command(
								'Cannot redeclare aliases.',
								\Exceptions\Command::ALIAS_ALREADY_EXISTS);
			self::$aliases[$alias] = $command;
		}
		
		public function runInstantiation()
		{
		
			$d = dir(dirname(__FILE__) . '/../Commands');
			while($command = $d->read())
				if(strpos($command, '.php') !== false)
					self::instantiate(substr($command, 0, strpos($command, '.')));
		}
	
		private static function instantiate($command)
		{
			
			$class = 'Commands\\' . $command;
			
			if(isset(self::$instances[$command]))
				throw new \Exceptions\Command(
								$command . ' already instantiated, trying to do so again.',
								\Exceptions\Command::ALREADY_INSTANTIATED);
			
			self::$instances[$class] = new $class();
		}
	
		public static function find($input)
		{
			
			$input = strtolower($input);
			if(!isset(self::$aliases[$input]))
				return false;
			
			$alias = self::$aliases[$input];
			
			return self::$instances[$alias];
		}
		
		public static function findObjectByArgs($objects, $args)
		{
			$args = strtolower($args);
			foreach($objects as $object)
			{
				$nouns = explode(' ', $object->getNouns());
				foreach($nouns as $noun)
					if(strpos(strtolower($noun), $args) === 0)
						return $object;
			}
			return null;
		}
	
		abstract public static function perform(&$actor, $args = null);
	}
?>
