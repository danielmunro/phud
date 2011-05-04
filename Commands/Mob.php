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
	namespace Commands;
	class Mob extends \Mechanics\Command
	{
	
		protected function __construct()
		{
		
			\Mechanics\Command::addAlias(__CLASS__, 'mob');
		}
	
		public static function perform(&$actor, $args = null)
		{
		
			throw new Exception("NO TOUCH");
		
		
			if($args[1] == 'copy')
			{
				$target = $actor->getRoom()->getActorByInput(array(null, $args[2]));
				if($target)
				{
					//$class_name = get_class($target);
					//$new_instance = new $class_name();
					
					
					$new_instance = clone $target;
					$new_instance->setId(0);
					$new_instance->setRoom($new_instance->getRoom());
					$new_instance->save();
					return \Mechanics\Server::out($actor, $new_instance->getAlias(true)." arrives in a puff of smoke!");
				}
				else
				{
					return \Mechanics\Server::out($actor, "Nothing is there.");
				}
			}
			
			if($args[1] == 'change')
			{
				$target = $actor->getRoom()->getActorByInput(array(null, $args[2]));
				if($target)
				{
					
				}
				else
				{
					return \Mechanics\Server::out($actor, "Nothing is there.");
				}
			}
		}
	
	}
?>
