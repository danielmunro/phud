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
	namespace Mechanics\Ability;
	use Mechanics\Debug;
	use Mechanics\Actor;
	abstract class Ability
	{
	
		protected static $set = null;
		protected static $alias = '';
		protected static $level = 1;
		protected static $creation_points = 0;
		protected static $hook = 0;
		protected $percent = 0;
		
		const TARGET_FIGHTING = 1;
		const TARGET_ARGS = 2;
		const TARGET_SELF = 3;
		
		const HOOK_TICK = 1;
		const HOOK_HIT_EVADE = 2;
		const HOOK_BUY_ITEM = 3;
		const HOOK_HIT_ATTACK_ROUND = 4;
        const HOOK_HIT_DAMAGE_REDUCTION = 5;
	
		protected function __construct($percent = 0)
		{
			$this->percent = $percent;
		}	

		abstract public function perform(Actor $actor, $args = array());
	
		public function getPercent()
		{
			return $this->percent;
		}

		public static function getHook()
		{
			return self::$hook;
		}
		
		public static function getCreationPoints()
		{
			return self::$creation_points;
		}
		
		public static function getAlias()
		{
			return self::$alias;
		}
		
		public static function getLevel()
		{
			return self::$level;
		}
		
		public function __toString()
		{
			return $this->alias;
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
					return -(rand(1, 4) / 100);
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
