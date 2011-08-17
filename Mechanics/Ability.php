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
	
		protected static $instances = array();
		protected $level = 1;
		private $creation_points = 0;
		private $type = 0;
		protected $fail_message = '';
		protected $base_class = null;
		protected $delay = 0;
		protected $clean_name = '';
		protected $clean_name_fitted = '';
		protected $alias = null;
		protected $hook = 0;
		
		const TYPE_SKILL = 1;
		const TYPE_SPELL = 2;
		
		const TARGET_FIGHTING = 1;
		const TARGET_ARGS = 2;
		const TARGET_SELF = 3;
		
		const HOOK_TICK = 1;
		const HOOK_HIT_DEFEND = 2;
	
		protected function __construct($type)
		{
			$this->type = $type;
		}
		
		public static function instance()
		{
			$class = get_called_class();
			if(!isset(self::$instances[$class]))
				self::$instances[$class] = new $class();
			return self::$instances[$class];
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
		
		public function getHook()
		{
			return $this->hook;
		}
		
		public function getFailMessage()
		{
			return $this->fail_message;
		}
		
		public function getCreationPoints()
		{
			return $this->creation_points;
		}
		
		public function getDelay()
		{
			return $this->delay;
		}
	
		abstract public function perform(Actor $actor, $percent = 0, $args = array());
		
		public function getBaseClass()
		{
			return $this->base_class;
		}
	
		public function getType() { return $this->type; }
		public function getAlias()
		{
			return $this->alias;
		}
		/**
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
		*/
		public static function getLevel() { return self::$level; }
		
		public function __toString()
		{
			$class = get_called_class();
			return substr($class, strpos($class, '\\') + 1);
		}
		
		protected function getEasyAttributeModifier($attribute)
		{
			switch($attribute)
			{
				case ($attribute < 15):
					return rand(12, 17) / 100;
				case ($attribute < 17):
					return rand(8, 12) / 100;
				case ($attribute < 20):
					return rand(0, 6) / 100;
				case ($attribute < 22):
					return 0;
				case ($attribute < 25):
					return -(rand(0, 5) / 100);
				default:
					return -(rand(0, 10) / 100);
			}
		}
		
		protected function getNormalAttributeModifier($attribute)
		{
			switch($attribute)
			{
				case ($attribute < 15):
					return rand(18, 25) / 100;
				case ($attribute < 17):
					return rand(10, 18) / 100;
				case ($attribute < 20):
					return rand(4, 10) / 100;
				case ($attribute < 22):
					return rand(0, 4) / 100;
				case ($attribute < 25):
					return -(rand(0, 3) / 100);
				default:
					return -(rand(0, 4) / 100);
			}
		}
		
		protected function getHardAttributeModifier($attribute)
		{
			switch($attribute)
			{
				case ($attribute < 15):
					return rand(30, 40) / 100;
				case ($attribute < 17):
					return rand(20, 30) / 100;
				case ($attribute < 20):
					return rand(10, 20) / 100;
				case ($attribute < 22):
					return rand(0, 10) / 100;
				case ($attribute < 25):
					return 0;
				default:
					return rand(0, 5) / 100;
			}
		}
	}

?>
