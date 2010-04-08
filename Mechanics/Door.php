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

	class Door
	{
	
		private $id = 0;
		private $short = '';
		private $long_room1 = '';
		private $long_room2 = '';
		private $unlock_item_id = 0;
		private $room1_id = 0;
		private $room2_id = 0;
		private $direction1 = '';
		private $direction2 = '';
		private $disposition = '';
		private $default_disposition = '';
		private $nouns = '';
		private $hidden = false;
		private $hidden_show_command = '';
		private $hidden_action = '';
		private $hidden_item_id = 0;
		private $reload_ticks = 0;
	
		const DISPOSITION_LOCKED = 'locked';
		const DISPOSITION_OPEN = 'open';
		const DISPOSITION_CLOSED = 'closed';
		
		private static $instances = array();
		
		private function __construct($row)
		{
			$this->id = $row->id;
			$this->short = $row->short_desc;
			$this->long_room1 = $row->long_desc_room1;
			$this->long_room2 = $row->long_desc_room2;
			$this->room1_id = $row->fk_room1_id;
			$this->room2_id = $row->fk_room2_id;
			$this->direction1 = $row->direction1;
			$this->direction2 = $row->direction2;
			$this->disposition = $row->disposition;
			$this->default_disposition = $row->default_disposition;
			$this->nouns = $row->nouns;
			$this->hidden = $row->hidden;
			$this->hidden_show_command = $row->hidden_show_command;
			$this->hidden_action = $row->hidden_action;
			$this->hidden_item_id = $row->fk_hidden_item_id;
			$this->reload_ticks = $row->reload_ticks;
		}
		
		public static function getInstances()
		{
			return self::$instances;
		}
		
		public function decreaseReloadTick()
		{
			if($this->disposition != $this->default_disposition)
				$this->reload_ticks--;
			return $this->reload_ticks;
		}
		
		public function reload()
		{
			unset(self::$instances[$this->id]);
			self::getInstance($this->id);
		}
		
		public static function getInstance($id)
		{
			if(isset(self::$instances[$id]))
				return self::$instances[$id];
			
			$row = Db::getInstance()->query('SELECT * FROM doors WHERE id = ?', $id)->getResult()->fetch_object();
			self::$instances[$id] = new self($row);
		}
		
		public static function findByRoomId($room_id)
		{
			$instances = array();
			
			foreach(self::$instances as $instance)
				if($instance->getRoom1Id() == $room_id || $instance->getRoom2Id() == $room_id)
					$instances[] = $instance;
			
			if(sizeof($instances) > 0)
				return $instances;
			
			$doors = Db::getInstance()->query('SELECT * FROM doors WHERE fk_room1_id = ? OR fk_room2_id = ?', array($room_id, $room_id))->fetch_objects();
			
			if(empty($doors))
				return $instances;
			
			foreach($doors as $door)
				if(isset(self::$instances[$door->id]))
					$instances[] = self::$instances[$door->id];
				else
					$instances[] = self::$instances[$door->id] = new self($door);
			
			return $instances;
		}
		
		public static function findByRoomAndDirection($room_id, $direction)
		{

			$doors = self::findByRoomId($room_id);
			foreach($doors as $door)
				if($door->getRoom1Id() == $room_id && $door->getDirection1() == $direction)
					return $door;
				else if($door->getRoom2Id() == $room_id && $door->getDirection2() == $direction)
					return $door;
		}
		
		public function setDisposition($disposition) { $this->disposition = $disposition; }
		public function getDisposition() { return $this->disposition; }
		public function getRoom1Id() { return $this->room1_id; }
		public function getRoom2Id() { return $this->room2_id; }
		public function getDirection1() { return $this->direction1; }
		public function getDirection2() { return $this->direction2; }
		public function getId() { return $this->id; }
		public function getShort() { return $this->short; }
		public function getLong($room_id)
		{
			if($room_id == $this->room1_id)
				return $this->long_room1;
			else if($room_id == $this->room2_id)
				return $this->long_room2;
		}
		public function getNouns() { return $this->nouns; }
		public function getHidden($room_id = null)
		{
			if($room_id === null)
				return $this->hidden;
			else if($room_id == $this->room1_id && $this->disposition == self::DISPOSITION_OPEN)
				return false;
			else if($room_id == $this->room1_id)
				return $this->hidden;
			return false;
		}
		public function getHiddenMessage() { return $this->hidden_message; }
		public function getHiddenAction() { return $this->hidden_action; }
		public function setHidden($hidden) { $this->hidden = $hidden; }
		public function getHiddenItemId() { return $this->hidden_item_id; }
		public function getHiddenShowCommand() { return $this->hidden_show_command; }
	}
?>
