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
	abstract class Ability
	{
	
		protected $level = 1;
		protected $aliases = array();
		private static $alias_ref = array();
		protected static $instance = null;
		private $creation_points = 0;
		private $type = 0;
		protected $fail_message = '';
		protected $base_class = null;
		
		const TYPE_SKILL = 1;
		const TYPE_SPELL = 2;
		
		const TARGET_FIGHTING = 1;
		const TARGET_ARGS = 2;
		const TARGET_SELF = 3;
	
		protected function __construct($type)
		{
		
			if(!is_array($this->aliases) || !sizeof($this->aliases))
				throw new \Exceptions\Ability("Cannot instantiate class (".__CLASS__.") without aliases", \Exceptions\Ability::MISSING_ARGUMENTS);
		
			foreach($this->aliases as $alias)
				if(self::lookup($alias))
					throw new \Exceptions\Ability("Cannot overwrite alias (".$alias.")", \Exceptions\Ability::ALIAS_CONFLICT);
				else
					self::$alias_ref[$alias] = $this;
			
			$this->type = $type;
		}
		
		public static function instance()
		{
			if(!isset(static::$instance))
				static::$instance = new static();
			return static::$instance;
		}
		
		public static function runInstantiation()
		{
		
			$dirs = array('Skills', 'Spells');
			foreach($dirs as $dir)
			{
				$d = dir(dirname(__FILE__) . '/../'.$dir);
				while($ability = $d->read())
					if(strpos($ability, '.php') !== false)
					{
						$class = $dir.'\\'.substr($ability, 0, strpos($ability, '.'));
						$class::instance();
					}
			}
		}
		
		public function getFailMessage()
		{
			return $this->fail_message;
		}
		
		public function getCreationPoints()
		{
			return $this->creation_points;
		}
	
		/**
		protected static function instantiate($dir, $ability)
		{
			
			$class = $dir.'\\'.$ability;
			$aliases = $class::getAliases();
			foreach($aliases as $alias)
				self::$abilities[$alias] = $class;
			if(method_exists($class, 'extraInstantiate'))
				$class::extraInstantiate();
		}
		
		public function save()
		{
			if($this->actor_id)
				Db::getInstance()->query('
					INSERT INTO abilities (`name`, percent, actor_type, fk_actor_id, `type`) VALUES (?, ?, ?, ?, ?)
					ON DUPLICATE KEY UPDATE percent = ?', array($this->name, $this->percent, $this->actor_type, $this->actor_id, $this->type, $this->percent));
		}
		
		public function remove()
		{
			Db::getInstance()->query('DELETE FROM abilities WHERE fk_actor_id = ? AND actor_type = ? AND `name` = ?', array($this->actor_id, $this->actor_type, $this->name));
		}
		*/
		
		public static final function lookup($alias)
		{
			return isset(self::$alias_ref[$alias]) ? self::$alias_ref[$alias] : false;
		}
		
		public function getBaseClass()
		{
			return $this->base_class;
		}
	
		public function getAliases() { return $this->aliases; }
		public function getType() { return $this->type; }
		public function getName() { return $this->name; }
		public function getCleanName($space = false, $strtolower = true)
		{
			if(!$this->clean_name)
			{
				$this->clean_name = $this->clean_name_fitted = str_replace('_', ' ', $this->name);
				$clean_name_len = strlen($this->clean_name);
				for($i = 0; $i < 40 - $clean_name_len; $i++)
					$this->clean_name_fitted .= ' ';
			}
			
			if($strtolower)
			{
				return $space ? strtolower($this->clean_name_fitted) : strtolower($this->clean_name);
			}
			
			return $space ? $this->clean_name_fitted : $this->clean_name;
		}
		public function getPercent() { return $this->percent; }
		public function setPercent($percent) { $this->percent = $percent; }
		public static function getLevel() { return self::$level; }
		
		public function __toString()
		{
			$class = get_called_class();
			return substr($class, strpos($class, '\\') + 1);
		}
	}

?>
