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
	abstract class Perform
	{
		
		protected $level = 0;
		protected $min_mana_cost = 0;
		protected $min_movement_cost = 0;
		protected $min_hp_cost = 0;
		protected $display_name = array();
		protected $improve_by_practice = 0;
		
		private static $instances;

		protected function __construct() {}

		public static function find($skill)
		{
			
			if(class_exists($skill))
			{
				if(empty(self::$instances[$skill]))
					self::$instances[$skill] = new $skill();
				
				return self::$instances[$skill];
			}		
		}
	
		abstract public static function perform(Actor &$actor, Skill $skill, $args = null);
	
		public function getLevel() { return $this->level; }
		public function getMinManaCost() { return $this->min_mana_cost; }
		public function getMinHpCost() { return $this->min_hp_cost; }
		public function getMinMovementCost() { return $this->min_movement_cost; }
		public function getImprovedByPractice() { return $this->improved_by_practice; }
	
		public function getDisplayName($actor)
		{
			return $this->display_name[0];
		}
	
		public function getModifiedManaCost(Actor $caster)
		{
		
			$cost = 100 / ( 2 + $caster->getLevel() - $this->level);
			return $cost > $this->min_mana_cost ? $cost : $this->min_mana_cost;
		}
		
		
		public function checkGain($actor, $skill)
		{
			
			
		}
	}

?>
