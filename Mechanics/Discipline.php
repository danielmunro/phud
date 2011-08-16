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
	abstract class Discipline
	{
		
		protected $ability_set = null;
		protected static $instances = array();
		protected $alias = null;
		
		protected function __construct()
		{
		}
		
		public static function runInstantiation()
		{
			$d = dir(dirname(__FILE__) . '/../Disciplines');
			while($discipline = $d->read())
				if(strpos($discipline, '.php') !== false)
				{
					$class = 'Disciplines\\'.substr($discipline, 0, strpos($discipline, '.'));
					$class::instance();
				}
		}
		
		public static function instance()
		{
			$class = get_called_class();
			if(!isset(self::$instances[$class]))
				self::$instances[$class] = new $class();
			return self::$instances[$class];
		}
		
		public function getAbilitySet()
		{
			if(!$this->ability_set)
				$this->initAbilitySet();
			return $this->ability_set;
		}
		
		abstract protected function initAbilitySet();
		
		abstract protected function initDisciplines();
		
		public function getExperienceCost(\Mechanics\Ability $ability)
		{
			if($ability->getBaseClass() == $this)
				return $ability->getCreationCost() - 1;
			else
				return $ability->getCreationCost() + 2;
		}
		
		public function getAlias()
		{
			return $this->alias;
		}
		
		public function __toString()
		{
			$class = get_class($this);
			return substr($class, strpos($class, '\\') + 1);
		}
	}
?>
