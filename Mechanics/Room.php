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
	class Room
	{
	
		static $instances = array();
		
		private $id = null;
		private $title = '';
		private $description = '';
		private $north = 0;
		private $south = 0;
		private $east = 0;
		private $west = 0;
		private $up = 0;
		private $down = 0;
		private $door = null;
		private $inventory = null;
		private $area = '';
		private $visibility = 1;
		private $actors = array();
	
		const PURGATORY_ROOM_ID = 7;
	
		public function __construct()
		{
			$this->inventory = new Inventory();
		}
	
		public function loadFrom($row)
		{
			$this->title = $row->title;
			$this->description = $row->description;
			$this->north = $row->north;
			$this->south = $row->south;
			$this->east = $row->east;
			$this->west = $row->west;
			$this->up = $row->up;
			$this->down = $row->down;
			$this->area = $row->area;
			$this->visibility = $row->visibility;
		}
		public function getVisibility() { return $this->visibility; }
		public function getId() { return $this->id; }
		public function getTitle() { return $this->title; }
		public function getDescription() { return $this->description; }
		private function getDirection($direction_str, $direction_id)
		{
			$door = Door::findByRoomAndDirection($this->id, $direction_str);
			if($door instanceof Door && $door->getDisposition() != Door::DISPOSITION_OPEN)
				return 0;
			return $direction_id;
		}
		public function getNorth() { return $this->getDirection('north', $this->north); }
		public function getSouth() { return $this->getDirection('south', $this->south); }
		public function getEast() { return $this->getDirection('east', $this->east); }
		public function getWest() { return $this->getDirection('west', $this->west); }
		public function getUp() { return $this->getDirection('up', $this->up); }
		public function getDown() { return $this->getDirection('down', $this->down); }
		public function getInventory() { return $this->inventory; }
		public function setArea($area) { $this->area = $area; }
		public function getArea() { return $this->area; }
		
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
			if(empty($input[1]))
				return;
			
			if(is_array($input))
				$input = array_pop($input);
			
			$person = strtolower($input);
			foreach($this->actors as $actor)
			{
				$look_for = property_exists($actor, 'noun') ? explode(' ', $actor->getNoun()) : array($actor->getAlias());
				foreach($look_for as $look)
					if(stripos($look, $person) === 0)
						return $actor;
			}
			return null;
		}
		
		public static function find($id)
		{
			if(is_numeric($id))
			{
				if(self::$instances[$id] instanceof self)
					return self::$instances[$id];
				$db = \Mechanics\Dbr::instance();
				$room_serialized = $db->lGet('rooms', $id);
				return self::$instances[$id] = unserialize($room_serialized);
			}
		}
		
		public function save()
		{
			$actors = $this->actors;
			$this->actors = array();
			$db = \Mechanics\Dbr::instance();
			if(is_numeric($this->id))
				$db->lSet('rooms', $this->id, serialize($this));
			else
				$this->id = $db->rPush('rooms', serialize($this)) - 1;
			$this->actors = $actors;
		}
	}

?>
