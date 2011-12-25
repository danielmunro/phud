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
	use \Mechanics\Alias;
	use \Mechanics\Server;
	use \Mechanics\Room as mRoom;
	use \Mechanics\Command\DM;
	use \Living\User as lUser;

	class Room extends DM
	{
	
		protected function __construct()
		{
			self::addAlias('room', $this);
		}
		
		public function perform(lUser $user, $args = array())
		{
			if($args[1] == 'new' || $args[1] == 'create')
			{
			
				$direction = $this->isValidDirection($args[2]);
				if(!$direction)
					return Server::out($user, "That direction doesn't exist.");
			
				$room = new mRoom();
				$room->save();
				$user->getRoom()->{'set' . ucfirst($direction)}($room->getId());
				$new_direction = mRoom::getReverseDirection($direction);
				$room->{'set' . ucfirst($new_direction)}($user->getRoom()->getId());
				$room->save();
				$user->getRoom()->save();
				
				return Server::out($user, "You've created a new room to the " . $direction . ".");
			}
			
			if($args[1] == 'id')
				return Server::out($user, "ID: " . $user->getRoom()->getId());
			
			$property = $this->isValidProperty($args[1]);
			if($property)
			{
				if(is_numeric($property[0])) {
					$fn = 'set' . ucfirst($property);
				} else {
					$fn = $property[0];
				}
				array_shift($args);
				array_shift($args);
				$value = implode(' ', $args);
				$user->getRoom()->$fn($value);
				$user->getRoom()->save();
				return Server::out($user, 'Property set.');
			}
			
			if($args[1] == 'copy')
			{
			
				$direction = $this->isValidDirection($args[2]);
				if(!$direction)
					return Server::out($user, "That direction doesn't exist.");
			
				$room = new mRoom();
				$room->save();
				$user->getRoom()->{'set' . ucfirst($direction)}($room->getId());
				$user->getRoom()->save();
				$new_direction = mRoom::getReverseDirection($direction);
				$room->setTitle($user->getRoom()->getTitle());
				$room->setDescription($user->getRoom()->getDescription());
				$room->setArea($user->getRoom()->getArea());
				$room->{'set' . ucfirst($new_direction)}($user->getRoom()->getId());
				$room->save();
				return Server::out($user, 'Property set.');
			}
			
			if(strpos('information', $args[1]) === 0)
			{
				return Server::out($user, 
								"Information on room (#".$user->getRoom()->getId()."):\n".
								"title:                  ".$user->getRoom()->getTitle()."\n".
								"area:                   ".$user->getRoom()->getArea()."\n".
								"movement cost:          ".$user->getRoom()->getMovementCost()."\n".
								"description:\n".$user->getRoom()->getDescription());
			}
			
			return Server::out($user, "What was that?");
			
		}
		
		private function isValidProperty($property)
		{
			$dirs = array('title', 'description', 'area', 'north', 'south', 'east', 'west', 'up', 'down', 'setMovementCost' => 'movement_cost');
		
			foreach($dirs as $k => $p)
				if(strpos($p, $property) === 0)
					return [$k, $p];
			
			return false;
		
		}
		
		private function isValidDirection($dir)
		{
			$dirs = array('north', 'south', 'east', 'west', 'up', 'down');
		
			foreach($dirs as $d)
				if(strpos($d, $dir) === 0)
					return $d;
			
			return false;
		}
	}
?>
