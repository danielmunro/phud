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

	class Skill
	{
	
		private $id = 0;
		private $name = '';
		private $proficiency = 0;
		private $user_id = 0;
	
		private static $instances = array();
	
		public function __construct($id, $name, $proficiency, $alias, $user_id = null)
		{
		
			$alias = strtolower($alias);
			$name = strtolower($name);
			
			$this->id = $id;
			$this->name = $name;
			$this->proficiency = $proficiency;
			$this->user_id = $user_id;
			
			self::$instances[$alias][$name] = $this;
		}
	
		public static function findByActorAndInput($alias, $input)
		{
		
			$skills = Skill::findByAlias($alias);
			if(!empty($skills[$input]))
				return $skills[$input];
		}
		
		public static function findByAliasAndName($alias, $name)
		{

			if(!isset(self::$instances[$alias]))
				self::findByAlias($alias);

			if(isset(self::$instances[$alias][$name]))
				return self::$instances[$alias][$name];

			return null;
		}
	
		public static function findByAlias($alias)
		{
			
			if(!empty(self::$instances[$alias]))
				return self::$instances[$alias];
			
			$rows = Db::getInstance()->query('SELECT skills.* FROM skills INNER JOIN users ON skills.fk_user_id = users.id WHERE users.alias = ?', $alias)->fetch_objects();
			
			foreach($rows as $row)
				self::$instances[$alias][$row->skill] = new Skill($row->id, $row->skill, $row->percent, $row->fk_user_id);
			
			return self::$instances[$alias];
		}
	
		public function getName() { return $this->name; }
		public function getProficiency() { return $this->proficiency; }
		public function increaseProficiency($proficiency) { $this->proficiency = $proficiency; }
		
		public function checkGain()
		{
			
			
		}
		
		public function save()
		{
		
			if(!$this->user_id)
				return false;
			
			if($this->id)
				Db::getInstance()->query('UPDATE skills SET skill = ?, percent = ? WHERE id = ?', array($this->name, $this->proficiency, $this->id));
			else
				$this->id = Db::getInstance()->query('INSERT INTO skills (skill, percent, fk_user_id) VALUES (?, ?, ?)', array($this->name, $this->proficiency, $this->user_id));
		}
	
	}

?>
