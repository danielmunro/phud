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
	class Room extends \Mechanics\Command implements \Mechanics\Command_DM
	{
	
		protected function __construct()
		{
			new \Mechanics\Alias('room', $this);
		}
		
		public function perform(\Mechanics\Actor $actor, $args = array())
		{
		
			// Technically yes...
			//if($actor->getLevel() < \Mechanics\Actor::MAX_LEVEL)
			//	return \Mechanics\Server::out($actor, "You cannot do that.");
			
			if($args[1] == 'new' || $args[1] == 'create')
			{
			
				$direction = $this->isValidDirection($args[2]);
				if(!$direction)
					return \Mechanics\Server::out($actor, "That direction doesn't exist.");
			
				$room = new \Mechanics\Room();
				$room->save();
				$actor->getRoom()->{'set' . ucfirst($direction)}($room->getId());
				$new_direction = self::getReverseDirection($direction);
				$room->{'set' . ucfirst($new_direction)}($actor->getRoom()->getId());
				$room->save();
				$actor->getRoom()->save();
				
				return \Mechanics\Server::out($actor, "You've created a new room to the " . $direction . ".");
			}
			
			if($args[1] == 'id')
				return \Mechanics\Server::out($actor, "ID: " . $actor->getRoom()->getId());
			
			$property = $this->isValidProperty($args[1]);
			if($property)
			{
				$fn = 'set' . ucfirst($property);
				array_shift($args);
				array_shift($args);
				$value = implode(' ', $args);
				$actor->getRoom()->$fn($value);
				$actor->getRoom()->save();
				return \Mechanics\Server::out($actor, 'Property set.');
			}
			
			if($args[1] == 'copy')
			{
			
				$direction = $this->isValidDirection($args[2]);
				if(!$direction)
					return \Mechanics\Server::out($actor, "That direction doesn't exist.");
			
				$room = new \Mechanics\Room();
				$room->save();
				$room->setInventory(\Mechanics\Inventory::find('room', $room->getId()));
				$actor->getRoom()->{'set' . ucfirst($direction)}($room->getId());
				$actor->getRoom()->save();
				$new_direction = self::getReverseDirection($direction);
				$room->setTitle($actor->getRoom()->getTitle());
				$room->setDescription($actor->getRoom()->getDescription());
				$room->setArea($actor->getRoom()->getArea());
				$room->{'set' . ucfirst($new_direction)}($actor->getRoom()->getId());
				$room->save();
				return \Mechanics\Server::out($actor, 'Property set.');
			}
			
			if(strpos('information', $args[1]) === 0)
			{
				return \Mechanics\Server::out($actor, 
								"Information on room (#".$actor->getRoom()->getId()."):\n".
								"title:                  ".$actor->getRoom()->getTitle()."\n".
								"area:                   ".$actor->getRoom()->getArea()."\n".
								"description:\n".$actor->getRoom()->getDescription());
			}
			
			return \Mechanics\Server::out($actor, "What was that?");
			
		}
		
		private function isValidProperty($property)
		{
			$dirs = array('title', 'description', 'area', 'north', 'south', 'east', 'west', 'up', 'down');
		
			foreach($dirs as $p)
				if(strpos($p, $property) === 0)
					return $p;
			
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
		
		private static function getReverseDirection($direction)
		{
			if($direction == 'north')
				return 'south';
			if($direction == 'south')
				return 'north';
			if($direction == 'east')
				return 'west';
			if($direction == 'west')
				return 'east';
			if($direction =='up')
				return 'down';
			if($direction == 'down')
				return 'up';
		}
	}
?>
