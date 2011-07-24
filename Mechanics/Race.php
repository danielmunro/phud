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
	abstract class Race
	{
	
		const SIZE_TINY = 0;
		const SIZE_SMALL = 1;
		const SIZE_NORMAL = 2;
		const SIZE_LARGE = 3;
		
		const PART_HEAD = 0;
		const PART_ARM = 1;
		const PART_LEG = 2;
		const PART_HEART = 3;
		const PART_BRAIN = 4;
		const PART_GUTS = 5;
		const PART_HAND = 6;
		const PART_FEET = 7;
		const PART_FINGER = 8;
		const PART_EAR = 9;
		const PART_EYE = 10;
		const PART_LONG_TONGUE = 11;
		const PART_TENTACLES = 12;
		const PART_FINS = 13;
		const PART_WINGS = 14;
		const PART_TAIL = 15;

		private static $instances = array();
		protected $attributes = null;
		protected $affects = array();
		protected $max_str;
		protected $max_int;
		protected $max_wis;
		protected $max_dex;
		protected $max_con;
		protected $movement_cost;
		protected $move_verb;
		protected $decrease_thirst = 0;
		protected $decrease_nourishment = 0;
		protected $full = 0;
		protected $weapons;
		protected $armor;
		protected $unarmed_verb = 'punch';
		protected $size = 2;
		protected $effects_resist = array();
		protected $effects_vuln = array();
		protected $materials_vuln = array();
		protected $damages_vuln = array();
		protected $available_disciplines = array();
		protected $playable = false;
		
		protected function __construct() {}
		
		public static function getInstance($race)
		{
			$race = ucfirst($race);
			
			if(!empty(self::$instances[$race]) && self::$instances[$race] instanceof self)
				return self::$instances[$race];
			
			$class = 'Races\\' . $race;

			$instance = new $class();
		
			if(!empty($instance) && $instance instanceof self)
				return self::$instances[$race] = $instance;
			
			throw new Exception('Race not found');
		}
		
		public function applyRacialAttributeModifiers(&$actor)
		{
			
			$actor->setStr($this->attributes->getStr());
			$actor->setInt($this->attributes->getInt());
			$actor->setWis($this->attributes->getWis());
			$actor->setDex($this->attributes->getDex());
			$actor->setCon($this->attributes->getCon());
			$actor->setHit($this->attributes->getHit());
			$actor->setDam($this->attributes->getDam());
			$actor->setAcSlash($this->attributes->getAcSlash());
			$actor->setAcBash($this->attributes->getAcBash());
			$actor->setAcPierce($this->attributes->getAcPierce());
			$actor->setAcMagic($this->attributes->getAcMagic());
			
			// Add affects/vulns/resists/etc
		}
		
		public static function getParts(\Mechanics\Actor $actor)
		{
			// @todo finish parts... this can wait for other more important things
			$parts = array
			(
				self::PART_HEAD => "'s severed head rolls to the floor",
				self::PART_ARM => "'s arm is sliced from ",
				self::PART_LEG => 'leg',
				self::PART_HEART => 'heart'
			);
		}
		
		public function getSize() { return $this->size; }
		public function getRaceStr() { return get_class($this); }
		public function getMaxStr($base = false) { return $base ? $this->max_str : $this->max_str + 4; }
		public function getMaxInt($base = false) { return $base ? $this->max_str : $this->max_str + 4; }
		public function getMaxWis($base = false) { return $base ? $this->max_str : $this->max_str + 4; }
		public function getMaxDex($base = false) { return $base ? $this->max_str : $this->max_str + 4; }
		public function getMaxCon($base = false) { return $base ? $this->max_str : $this->max_str + 4; }
		public function getMovementCost() { return $this->movement_cost; }
		public function getUnarmedVerb() { return $this->unarmed_verb; }
		public function getMoveVerb() { return $this->move_verb; }
		public function getDecreaseNourishment() { return $this->decrease_nourishment; }
		public function getDecreaseThirst() { return $this->decrease_thirst; }
		public function getFull() { return $this->full; }
		public function isPlayable() { return $this->playable; }
		public function __toString()
		{
			$class = get_class($this);
			return substr($class, strpos($class, '\\') + 1);
		}
	}
?>
