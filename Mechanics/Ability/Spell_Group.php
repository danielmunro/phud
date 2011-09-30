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
	class Spell_Group
	{
		
		const GROUP_PROTECTIVE = 1;
		const GROUP_HEALING = 2;
		const GROUP_TRANSPORTATION = 3;
		const GROUP_CURATIVE = 4;
		const GROUP_MALADICTIONS = 5;
		const GROUP_PLAGUE = 6;
		const GROUP_ELEMENTAL = 7;
		const GROUP_ATTACK = 8;
		const GROUP_BEGUILING = 9;
		
		protected static $creation_points = 0;
		protected static $base_class = null;
		protected static $alias = null;
		protected $instance_type = 0;
		protected $spells = array();
		
		protected function __construct($instance_type)
		{
			$this->instance_type = $instance_type;
			$this->spells = $this->getGroupSpells();
		}
		
		public static function getCreationPoints()
		{
			return self::$creation_points;
		}
		
		public static function getAlias()
		{
			return self::$alias;
		}
		
		public static function getBaseClass()
		{
			return self::$base_class;
		}

		public function getInstanceType()
		{
			return $thiss->instance_type;
		}

		public function getSpells()
		{
			return $this->spells;
		}
		
		private function getGroupSpells()
		{
			switch($this->instance_type)
			{
				case self::GROUP_PROTECTIVE:
					return array(
						new \Spells\Armor(),
						new \Spells\Shield()
					);
				case self::GROUP_HEALING:
					return array(
						new \Spells\Cure_Light(),
						new \Spells\Cure_Serious(),
						new \Spells\Cure_Critical(),
						new \Spells\Heal()
					);
				case self::GROUP_ATTACK:
					return array(
						new \Spells\Magic_Missile()
					);
				case self::GROUP_BEGUILING:
					return array(
						new \Spells\Sleep()
					);
				default:
					return array();
			}

		}
	}
?>
