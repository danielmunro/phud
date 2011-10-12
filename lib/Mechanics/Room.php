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
	use JsonSerializable;

	class Room implements JsonSerializable
	{
	
		const START_ROOM = 1;
	
		static $instances = array();
		
		private $id = null;
		private $title = 'Generic room';
		private $description = 'A nondescript room.';
		private $north = -1;
		private $south = -1;
		private $east = -1;
		private $west = -1;
		private $up = -1;
		private $down = -1;
		private $doors = array();
		private $inventory = null;
		private $area = '';
		private $visibility = 1;
		private $actors = array();
		private $bg_image = '';
	
		const PURGATORY_ROOM_ID = 5;
	
		public function __construct()
		{
			$this->inventory = new Inventory();
			$this->doors = array(
								'north' => null,
								'south' => null,
								'east' => null,
								'west' => null,
								'up' => null,
								'down' => null
							);
		}

		public function getBGImage()
		{
			return $this->bg_image;
		}

		public function setBGImage($bg_image)
		{
			$this->bg_image = $bg_image;
		}
	
		public function getVisibility()
		{
			return $this->visibility;
		}
		
		public function getId()
		{
			return $this->id;
		}
		
		public function setId($id)
		{
			$this->id = $id;
		}
		
		public function getTitle()
		{
			return $this->title;
		}
		
		public function getDescription()
		{
			return $this->description;
		}
		
		private function getDirection($direction_str, $direction_id)
		{
			$door = $this->getDoor($direction_str);
			if($door instanceof Door && $door->getDisposition() !== Door::DISPOSITION_OPEN)
				return -1;
			return $direction_id;
		}
		
		public function setDoor($direction, Door $door)
		{
			$dir = self::getDirectionStr($direction);
			if($dir)
			{
				$this->doors[$direction] = $door;
			}
		}
		
		public function removeDoor(Door $door)
		{
			$key = array_search($door, $this->doors);
			if($key !== false)
			{
				unset($this->doors[$key]);
			}
		}
		
		public function getDoor($direction)
		{
			if(isset($this->doors[$direction]))
				return $this->doors[$direction];
			return null;
		}
		
		public function getDoors()
		{
			return $this->doors;
		}
		
		public function getDoorByInput($input)
		{
			$doors = array_filter(
				$this->doors,
				function($d) use ($input)
				{
					if($d)
					{
						$nouns = explode(' ', $d->getNouns());
						foreach($nouns as $n)
							if(strpos($n, $input) === 0)
								return true;
					}
					return false;
				}
			);
			return array_shift($doors);
		}
		
		public function getNorth() { return $this->getDirection('north', $this->north); }
		public function getSouth() { return $this->getDirection('south', $this->south); }
		public function getEast() { return $this->getDirection('east', $this->east); }
		public function getWest() { return $this->getDirection('west', $this->west); }
		public function getUp() { return $this->getDirection('up', $this->up); }
		public function getDown() { return $this->getDirection('down', $this->down); }
		
		public function getInventory()
		{
			return $this->inventory;
		}
		
		public function getArea() { return $this->area; }
		public function setArea($area) { $this->area = $area; }
		public function setTitle($title) { $this->title = $title; }
		public function setDescription($description) { $this->description = $description; }
		public function setNorth($north) { $this->north = $north; }
		public function setSouth($south) { $this->south = $south; }
		public function setEast($east) { $this->east = $east; }
		public function setWest($west) { $this->west = $west; }
		public function setUp($up) { $this->up = $up; }
		public function setDown($down) { $this->down = $down; }
		
		public function actorAdd(Actor $actor)
		{
			$this->actors[] = $actor;
		}
		public function actorRemove(Actor $actor)
		{
			$key = array_search($actor, $this->actors);
			if($key === false)
				throw new \Exceptions\Room('Actor is not in room', \Exceptions\Room::ACTOR_NOT_HERE);
			unset($this->actors[$key]);
			$this->actors = array_values($this->actors);
			
		}
		public function getActors()
		{
			return $this->actors;
		}
		
		public function announce(Actor $actor, $message)
		{
			foreach($this->actors as $a)
				if($a != $actor && $a->getDisposition() !== Actor::DISPOSITION_SLEEPING)
					Server::out($a, $message);
		}
		
		public function getActorByInput($input)
		{
			if(is_array($input) && empty($input[1]))
				return;
			
			if(is_array($input))
				$input = array_pop($input);
			
			$person = strtolower($input);
			foreach($this->actors as $actor)
			{
				$look_for = property_exists($actor, 'nouns') ? explode(' ', $actor->getNouns()) : array($actor->getAlias());
				foreach($look_for as $look)
					if(stripos($look, $person) === 0)
						return $actor;
			}
			return null;
		}
		
		public static function find($id)
		{
			if(isset(self::$instances[$id]) && self::$instances[$id] instanceof self)
				return self::$instances[$id];
			$db = \Mechanics\Dbr::instance();
			$room_serialized = $db->get($id);
			if($room_serialized)
			{
				self::$instances[$id] = unserialize($room_serialized);
			}
			else
			{
				$r = new Room();
				$r->setId($id);
				$r->save();
				self::$instances[$id] = $r;
			}
			return self::$instances[$id];
		}
		
		public static function getDirectionStr($dir)
		{
			switch($dir)
			{
				case strpos('north', $dir) === 0: return 'north';
				case strpos('south', $dir) === 0: return 'south';
				case strpos('east', $dir) === 0: return 'east';
				case strpos('west', $dir) === 0: return 'west';
				case strpos('up', $dir) === 0: return 'up';
				case strpos('down', $dir) === 0: return 'down';
				default: return false;
			}
		}
		
		public static function getReverseDirection($direction)
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
		
		public function save()
		{
			$actors = $this->actors;
			$this->actors = array();
			if(!$this->id)
				$this->id = microtime();
			$db = Dbr::instance();
			$db->set($this->id, serialize($this));
			$this->actors = $actors;
		}

		public function jsonSerialize()
		{
			return array(
				'id' => $this->id,
				'title' => $this->title,
				'description' => $this->description,
				'area' => $this->area,
				'north' => $this->north,
				'south' => $this->south,
				'east' => $this->east,
				'west' => $this->west,
				'up' => $this->up,
				'down' => $this->down,
				'actors' => $this->actors
			);
		}
	}

?>
