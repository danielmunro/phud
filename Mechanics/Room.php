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

	class Room
	{
	
		static $instance = array();
		
		private $id = 0;
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
	
		const PURGATORY_ROOM_ID = 7;
	
		private function __construct($id = null)
		{
			
		}
	
		public function loadFrom($row)
		{
			$this->id = $row->id;
			$this->title = $row->title;
			$this->description = $row->description;
			$this->north = $row->north;
			$this->south = $row->south;
			$this->east = $row->east;
			$this->west = $row->west;
			$this->up = $row->up;
			$this->down = $row->down;
			$this->area = $row->area;
		}
	
		public static function find($id)
		{
		
			if(isset(Room::$instance[$id]) === true && Room::$instance[$id] instanceof Room)
				return Room::$instance[$id];
			
			$row = Db::getInstance()->query(
				'SELECT 
					*
				FROM 
					world 
				WHERE world.id = ?', $id)->getResult()->fetch_object();
			Room::$instance[$id] = new Room($id);
			Room::$instance[$id]->loadFrom($row);
			Room::$instance[$id]->setInventory(Inventory::find('room', $id));
			return Room::$instance[$id];
		
		}
		
		public function getId() { return $this->id; }
		public function getTitle() { return $this->title; }
		public function getDescription() { return $this->description; }
		public function getNorth()
		{
			$door = Door::findByRoomAndDirection($this->id, 'north');
			
			if($door instanceof Door && $door->getDisposition() != Door::DISPOSITION_OPEN)
				return 0;
			return $this->north;
		}
		public function getSouth()
		{
			$door = Door::findByRoomAndDirection($this->id, 'south');
			if($door instanceof Door && $door->getDisposition() != Door::DISPOSITION_OPEN)
				return 0;	
			return $this->south;
		}
		public function getEast()
		{
			$door = Door::findByRoomAndDirection($this->id, 'east');
			if($door instanceof Door && $door->getDisposition() != Door::DISPOSITION_OPEN)
				return 0;
			return $this->east;
		}
		public function getWest()
		{
			$door = Door::findByRoomAndDirection($this->id, 'west');
			if($door instanceof Door && $door->getDisposition() != Door::DISPOSITION_OPEN)
				return 0;
			return $this->west;
		}
		public function getUp()
		{
			$door = Door::findByRoomAndDirection($this->id, 'up');
			if($door instanceof Door && $door->getDisposition() != Door::DISPOSITION_OPEN)
				return 0;
			return $this->up;
		}
		public function getDown()
		{
			$door = Door::findByRoomAndDirection($this->id, 'down');
			if($door instanceof Door && $door->getDisposition() != Door::DISPOSITION_OPEN)
				return 0;
			return $this->down;
		}
		public function getInventory() { return $this->inventory; }
		public function setInventory(Inventory $inventory) { $this->inventory = $inventory; }
		public function getArea() { return $this->area; }
	}

?>
