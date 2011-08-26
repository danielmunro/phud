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
	namespace Items;
	class Weapon extends Equipment
	{
		const TYPE_SWORD = 1;
		const TYPE_AXE = 2;
		const TYPE_MACE = 3;
		const TYPE_STAFF = 4;
		const TYPE_WHIP = 5;
		const TYPE_DAGGER = 6;
		const TYPE_WAND = 7;
		const TYPE_EXOTIC = 8;
		const TYPE_SPEAR = 9;
		const TYPE_FLAIL = 10;
		
		protected $short = 'a generic weapon';
		protected $long = 'A generic weapon lays here';
		protected $nouns = 'generic weapon';
		protected $weapon_type = 0;
		protected $verb = '';
		protected $damage_type = 0;
		
		public function __construct()
		{
			$this->position = Equipment::POSITION_WIELD;
			parent::__construct();
		}
		
		public function getWeaponType()
		{
			return $this->weapon_type;
		}
		
		public function setWeaponType($weapon_type)
		{
			$this->weapon_type = $weapon_type;
		}
		
		public function getDamageType()
		{
			return $this->damage_type;
		}
		
		public function setDamageType($damage_type)
		{
			$this->damage_type = $damage_type;
		}
		
		public function getVerb()
		{
			return $this->verb;
		}
		
		public function setVerb($verb)
		{
			$this->verb = $verb;
		}
	}

?>
