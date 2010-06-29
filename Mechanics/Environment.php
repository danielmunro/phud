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
	class Environment
	{
		private $id = 0;
		private $type = 0;
		private $command = '';
		private $table = '';
		private $table_id = 0;
		private $room_id = 0;
		private $message = '';
		private $look_describe = '';
		private $disposition = false;
		
		private static $instances = array();
		
		const CONCEAL_DOOR = 1;
		
		private function __construct($id, $type, $command, $table, $table_id, $room_id, $message, $look_describe)
		{
			$this->id = $id;
			$this->type = $type;
			$this->command = $command;
			$this->table = $table;
			$this->table_id = $table_id;
			$this->room_id = $room_id,
			$this->message = $message;
			$this->look_describe = $look_describe;
		}
		
		public static function findByRoomId($room_id)
		{
			if(isset(self::$instances[$room_id]))
				return self::$instances[$room_id];
			
			$instances = array();
			$rows = Db::getInstance()->query('SELECT * FROM environment WHERE fk_room_id = ?', $room_id);
			if(empty($rows))
			{
				self::$instances[$room_id] = null;
				return $instances;
			}
			$rows = $rows->fetch_objects();
			foreach($rows as $row)
				$instances[] = self::$instances[$room_id][] = new Environment(
																		$row->id,
																		$row->environment_type,
																		$row->command,
																		$row->fk_table,
																		$row->fk_table_id,
																		$row->fk_room_id,
																		$row->message,
																		$row->look_describe);
			return $instances;
		}
		
		public static function concealDoors($doors, $room_id)
		{
			if(!isset(self::$instances[$room_id]) ||
				(isset(self::$instances[$room_id]) && self::$instances[$room_id] === null))
				return;
			
			
			foreach(self::$instances[$room_id] as $env)
				$env->conceal
		}
		
		public function concealDoorsByRoomId()
	}
?>
