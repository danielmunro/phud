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

	abstract class Command
	{
	
		public static $instances = array();
	
		public static $aliases =
		array
		(
			'Command_N' => 'Command_North',
			'Command_S' => 'Command_South',
			'Command_E' => 'Command_East',
			'Command_W' => 'Command_West',
			'Command_U' => 'Command_Up',
			'Command_D' => 'Command_Down',
			'Command_L' => 'Command_Look',
			'Command_Inv' => 'Command_Inventory',
			'Command_Eq' => 'Command_Equipment',
			'Command_Equip' => 'Command_Equipment',
			'Command_Sc' => 'Command_Score',
			'Command_C' => 'Command_Cast'
		);
	
		public static function find($command)
		{
			
			if(empty(self::$instances[$command]) && class_exists($command))
				self::$instances[$command] = new $command();
			
			if(!empty(self::$aliases[$command]))
				return self::find(self::$aliases[$command]);
			
			if(!empty(self::$instances[$command]) && self::$instances[$command] instanceof Command)
				return self::$instances[$command];
			
			return null;
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
