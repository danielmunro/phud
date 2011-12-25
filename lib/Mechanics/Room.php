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
		\Mechanics\Event\Event;

	class Room
	{
		use Usable, Persistable;
	
		const START_ROOM = 1;
	
		static $instances = [];
		
		private $title = 'Generic room';
		private $description = 'A nondescript room.';
		private $north = -1;
		private $south = -1;
		private $east = -1;
		private $west = -1;
		private $up = -1;
		private $down = -1;
		private $doors = [];
		private $inventory = null;
		private $area = '';
		private $visibility = 1;
		private $movement_cost = 0;
		private $actors = [];
	
		const PURGATORY_ROOM_ID = 5;
	
		public function __construct()
		{
			$this->inventory = new Inventory();
			$this->doors = [
				'north' => null,
				'south' => null,
				'east' => null,
				'west' => null,
				'up' => null,
				'down' => null
			];
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
			return $this->getUsableNounByInput($doors, $input);
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
			Debug::addDebugLine($actor.' ('.$actor->getID().') is arriving to '.$this.' ('.$this->getID().')');
			$this->actors[] = $actor;
			$actor->addSubscriber($this->movement_subscriber);
		}
		public function actorRemove(Actor $actor)
		{
			Debug::addDebugLine($actor.' ('.$actor->getID().') is leaving '.$this.' ('.$this->getID().')');
			$key = array_search($actor, $this->actors);
			if($key === false) {
				Debug::addDebugLine($actor.' is not here');
				throw new \Exceptions\Room('Actor is not in room', \Exceptions\Room::ACTOR_NOT_HERE);
			}
			$actor->removeSubscriber($this->movement_subscriber);
			unset($this->actors[$key]);
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
			self::$instances[$id]->initRoom();
			return self::$instances[$id];
		}

		public function initRoom()
		{
			$this->movement_subscriber = $this->getMovementSubscriber();
		}

		private function getMovementSubscriber()
		{
			return new Subscriber(
				Event::EVENT_MOVED,
				$this,
				function($subscriber, $broadcaster, $room, $increase_movement_cost) {
					$increase_movement_cost($room->getMovementCost());
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

		protected function beforeSave()
		{
			$actors = $this->actors;
			$movement_subscriber = $this->movement_subscriber;
			$this->actors = [];
			$this->movement_subscriber = null;
			return [$actors, $movement_subscriber];
		}

		protected function afterSave($after)
		{
			$this->actors = $after[0];
			$this->movement_subscriber = $after[1];
		}

		public function __toString()
		{
			return $this->title;
		}
	}
?>
