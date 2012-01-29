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
	use \Mechanics\Equipment,
		\Mechanics\Damage;

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
		
		public function __construct($properties = [])
		{
			$this->position = Equipment::POSITION_WIELD;
			parent::__construct($properties);
		}
		
		public function getWeaponType()
		{
			return $this->weapon_type;
		}
		
		public function getWeaponTypeLabel()
		{
			switch($this->weapon_type)
			{
				case self::TYPE_SWORD:
					return 'sword';
				case self::TYPE_AXE:
					return 'axe';
				case self::TYPE_MACE:
					return 'mace';
				case self::TYPE_STAFF:
					return 'staff';
				case self::TYPE_WHIP:
					return 'whip';
				case self::TYPE_DAGGER:
					return 'dagger';
				case self::TYPE_WAND:
					return 'wand';
				case self::TYPE_EXOTIC:
					return 'exotic';
				case self::TYPE_SPEAR:
					return 'spear';
				case self::TYPE_FLAIL:
					return 'flail';
			}
			return false;
		}
		
		public static function getWeaponTypeFromLabel($label)
		{
			switch($label)
			{
				case strpos('sword', $label) === 0:
					return self::TYPE_SWORD;
				case strpos('axe', $label) === 0:
					return self::TYPE_AXE;
				case strpos('mace', $label) === 0:
					return self::TYPE_MACE;
				case strpos('staff', $label) === 0:
					return self::TYPE_STAFF;
				case strpos('whip', $label) === 0:
					return self::TYPE_WHIP;
				case strpos('dagger', $label) === 0:
					return self::TYPE_DAGGER;
				case strpos('wand', $label) === 0:
					return self::TYPE_WAND;
				case strpos('exotic', $label) === 0:
					return self::TYPE_EXOTIC;
				case strpos('spear', $label) === 0:
					return self::TYPE_SPEAR;
				case strpos('flail', $label) === 0:
					return self::TYPE_FLAIL;
			}
			return false;
		}
		
		public function setWeaponType($input)
		{
			$weapon_type = self::getWeaponTypeFromLabel($input);
			if($weapon_type !== false)
				$this->weapon_type = $weapon_type;
		}
		
		public function getDamageType()
		{
			return $this->damage_type;
		}
		
		public function setDamageType($input)
		{
			$damage_type = Damage::getDamageTypeFromLabel($input);
			if($damage_type !== false)
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
		
		public function getInformation()
		{
			return
				"=====================\n".
				"==Weapon Attributes==\n".
				"=====================\n".
				"weapon type:         ".$this->getWeaponTypeLabel()."\n".
				"verb:                ".$this->getVerb()."\n".
				"damage type:         ".$this->damage_type."\n".
				parent::getInformation();
		}
	}

?>
