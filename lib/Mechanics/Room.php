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
	use \Mechanics\Event\Subscriber,
		\Mechanics\Event\Event,
		\Exception;

	class Room
	{
		use Usable, Inventory;
	
		protected static $instances = [];
		protected $id = '';
		protected $title = 'Generic room';
		protected $description = 'A nondescript room.';
		protected $north = null;
		protected $south = null;
		protected $east = null;
		protected $west = null;
		protected $up = null;
		protected $down = null;
		protected $doors = ['north' => null, 'south' => null, 'east' => null, 'west' => null, 'up' => null, 'down' => null];
		protected $area = '';
		protected $visibility = 1;
		protected $movement_cost = 0;
		protected $_subscriber_movement = null;
		protected $persistable_list = 'rooms';
		protected $actors = [];
		protected static $start_room = 0;
	
		const PURGATORY_ROOM_ID = 5;
	
		public function __construct($properties = [])
		{
			$this->_subscriber_movement = $this->getMovementSubscriber();
			foreach($properties as $property => $value) {
				if(property_exists($this, $property)) {
					if($property === 'actors' && is_array($value)) {
						foreach($value as $actor) {
							$actor->setRoom($this);
						}
					} else {
						$this->$property = $value;
					}
				} else {
					throw new Exception($this.' does not have any such property: '.$property);
				}
			}
			if(empty($this->id) || isset(self::$instances[$this->id])) {
				throw new Exception('Room id is empty or already used: '.$this->id);
			}
			self::$instances[$this->id] = $this;
		}

		public static function setStartRoom($room_id)
		{
			self::$start_room = $room_id;
		}

		public static function getStartRoom()
		{
			return self::$start_room;
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

		public function getMovementCost()
		{
			return $this->movement_cost;
		}

		public function setMovementCost($movement_cost)
		{
			$this->movement_cost = $movement_cost;
		}
		
		private function getDirection($direction_str, $direction)
		{
			$door = $this->getDoor($direction_str);
			if($door instanceof Door && $door->getDisposition() !== Door::DISPOSITION_OPEN)
				return -1;
			if(is_numeric($direction) && $direction > -1 || $direction) {
				$direction = self::find($direction);
			}
			return $direction;
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
			return $this->getUsableNounByInput($doors, $input);
		}
		
		public function getNorth() { return $this->getDirection('north', $this->north); }
		public function getSouth() { return $this->getDirection('south', $this->south); }
		public function getEast() { return $this->getDirection('east', $this->east); }
		public function getWest() { return $this->getDirection('west', $this->west); }
		public function getUp() { return $this->getDirection('up', $this->up); }
		public function getDown() { return $this->getDirection('down', $this->down); }
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
			Debug::addDebugLine($actor.' is arriving to '.$this.' ('.$this->getID().')');
			$this->actors[] = $actor;
			$actor->addSubscriber($this->_subscriber_movement);
		}
		public function actorRemove(Actor $actor)
		{
			Debug::addDebugLine($actor.' is leaving '.$this.' ('.$this->getID().')');
			$key = array_search($actor, $this->actors);
			if($key === false) {
				Debug::addDebugLine($actor.' is not here');
				throw new Exception('Actor is not in room');
			}
			$actor->removeSubscriber($this->_subscriber_movement);
			unset($this->actors[$key]);
		}
		public function getActors()
		{
			return $this->actors;
		}
		
		public function announce(Actor $actor, $message)
		{
			foreach($this->actors as $a)
				if($a !== $actor && $a->getDisposition() !== Actor::DISPOSITION_SLEEPING)
					Server::out($a, $message);
		}

		public function announce2($announcements)
		{
			$actors_announced = [];
			$general_announcment = '';
			foreach($announcements as $announcement) {
				if($announcement['actor'] === '*') {
					$general_announcement = $announcement['message'];
				} else {
					$actors_announced[] = $announcement['actor'];
					Server::out($announcement['actor'], $announcement['message']);
				}
			}
			if($general_announcement) {
				foreach($this->actors as $actor) {
					if(!in_array($actor, $actors_announced)) {
						Server::out($actor, $general_announcement);
					}
				}
			}
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
			if(isset(self::$instances[$id]) && self::$instances[$id] instanceof self) {
				return self::$instances[$id];
			}
			$dbr = Dbr::instance();
			$properties = unserialize($dbr->get($id));
			if(empty($properties)) {
				$properties = ['id' => $id];
			}
			return new self($properties);
		}

		private function getMovementSubscriber()
		{
			return new Subscriber(
				Event::EVENT_MOVED,
				$this,
				function($subscriber, $broadcaster, $room, &$movement_cost) {
					$movement_cost += $room->getMovementCost();
				}
			);
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
			if(strpos('north', $direction) === 0)
				return 'south';
			if(strpos('south', $direction) === 0)
				return 'north';
			if(strpos('east', $direction) === 0)
				return 'west';
			if(strpos('west', $direction) === 0)
				return 'east';
			if(strpos('up', $direction) === 0)
				return 'down';
			if(strpos('down', $direction) === 0)
				return 'up';
		}

		public function __toString()
		{
			return $this->title;
		}

		public function __sleep()
		{
			return [
				'id'
			];
		}
	}
?>
