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
	
		private $name = '';
		private $clean_name = '';
		private $clean_name_fitted = '';
		private $percent = 0;
		private $actor_id = 0;
		private $actor_type = '';
		private $type = 0;
		
		// Level of the ability: when the actor can use it among other things
		protected static $level = 1;
		protected static $aliases = array();
		private static $abilities = array();
		
		const TYPE_SKILL = 1;
		const TYPE_SPELL = 2;
	
		public function __construct($percent, $type, $actor_id, $actor_type)
		{
		
			$this->name = (string)$this;
			$this->percent = $percent;
			$this->actor_id = $actor_id;
			$this->actor_type = $actor_type;
			$this->type = strpos(get_class($this), 'Skills') === 0 ? self::TYPE_SKILL : self::TYPE_SPELL;
		}
		
		public static function runInstantiation()
		{
		
			$dirs = array('Skills', 'Spells');
			foreach($dirs as $dir)
			{
				$d = dir(dirname(__FILE__) . '/../'.$dir);
				while($ability = $d->read())
					if(strpos($ability, '.php') !== false)
						self::instantiate($dir, substr($ability, 0, strpos($ability, '.')));
			}
		}
	
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
					ON DUPLICATE KEY UPDATE percent = ?', array($this->name, $this->percent, $this->actor_type, $this->actor_id, $this->type, $this->percent), true);
		}
		
		public static function exists($alias)
		{
			return isset(self::$abilities[$alias]) ? self::$abilities[$alias] : false;
		}
	
		public static function getAliases() { return static::$aliases; }
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
