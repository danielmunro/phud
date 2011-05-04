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
	class Quest
	{
	
		private static $instances = array();
		private $id = 0;
		private $user_id = 0;
		private $quest_id = 0;
		private $points = 0;
		private $accepted = false;
		private $complete = false;
		private $award_obtained = false;
	
		public function __construct($user_id, $quest_id)
		{
		
			$this->user_id = $user_id;
			$this->quest_id = $quest_id;
		
			$row = Db::getInstance()->query('SELECT * FROM quests WHERE fk_user_id = ? AND fk_quest_id = ?', array($this->user_id, $this->quest_id))->getResult()->fetch_object();
			
			if(empty($row))
				return;
			
			$this->id = $row->id;
			$this->points = $row->points;
			$this->accepted = $row->accepted;
			$this->complete = $row->complete;
			$this->award_obtained = $row->award_obtained;
		}
	
		public static function find($user_id, $quest_id)
		{
		
			if(!isset(self::$instances[$user_id][$quest_id]))
				self::$instances[$user_id][$quest_id] = new self($user_id, $quest_id);
			
			return self::$instances[$user_id][$quest_id];
		}
		
		public function save()
		{
			
			if($this->id)
				Db::getInstance()->query('UPDATE quests SET points = ?, accepted = ?, complete = ?, award_obtained = ? WHERE id = ?', array($this->points, $this->accepted, $this->complete, $this->award_obtained, $this->id);
			else
				$this->id = Db::getInstance()->query('INSERT INTO quests (points, accepted, complete, award_obtained, fk_user_id, fk_quest_id) values (?, ?, ?, ?, ?, ?)', 
														array($this->points, $this->accepted, $this->complete, $this->award_obtained, $this->user_id, $this->quest_id))->insert_id;
		}
		
		public function getUserId() { return $this->user_id; }
		public function getQuestId() { return $this->quest_id; }
		public function getPoints() { return $this->points; }
		public function getComplete() { return $this->complete; }
		public function getAccepted() { return $this->accepted; }
		public function getAwardObtained() { return $this->award_obtained; }
		
		public function addPoint() { $this->points++; $this->save(); }
		public function setComplete($complete) { $this->complete = $complete; $this->save(); }
		public function setAccepted($accepted) { $this->accepted = $accepted; $this->save(); }
		public function setAwardObtained($award) { $this->award_obtained = $award; $this->save(); }
	}
?>
