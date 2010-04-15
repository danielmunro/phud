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

	abstract class Race
	{
	
		protected $str;
		protected $int;
		protected $wis;
		protected $dex;
		protected $con;
		
		protected $max_str;
		protected $max_int;
		protected $max_wis;
		protected $max_dex;
		protected $max_con;
		
		protected $movement_cost;
		protected $move_verb;
		
		private static $instances = array();
		
		protected $hit_roll = 0;
		protected $dam_roll = 0;
		
		protected $ac_slash = 0;
		protected $ac_bash = 0;
		protected $ac_pierce = 0;
		protected $ac_magic = 0;
		
		protected $decrease_thirst = 0;
		protected $decrease_nourishment = 0;
		protected $full = 0;
		
		protected $weapons;
		protected $armor;
		
		protected $unarmed_verb = 'punch';
		
		private $actor;
		
		protected $size = 2;
		
		protected $effects_resist = array();
		protected $effects_vuln = array();
		
		protected $materials_vuln = array();
		
		protected $damages_vuln = array();
		
		const SIZE_TINY = 0;
		const SIZE_SMALL = 1;
		const SIZE_NORMAL = 2;
		const SIZE_LARGE = 3;
		
		protected function __construct()
		{
			
		}
		
		public function getInstance($race)
		{
			$race = ucfirst($race);
			
			if(!empty(self::$instances[$race]) && self::$instances[$race] instanceof Race)
				return self::$instances[$race];
			
			if(class_exists($race))
			{
				$instance = new $race();
			
				if(!empty($instance) && $instance instanceof Race)
					return self::$instances[$race] = $instance;
			}
		}
		
		public function applyRacialAttributeModifiers(&$actor)
		{
			
			$actor->setStr($this->str);
			$actor->setInt($this->int);
			$actor->setWis($this->wis);
			$actor->setDex($this->dex);
			$actor->setCon($this->con);
			
			$actor->setHitRoll($this->hit_roll);
			$actor->setDamRoll($this->dam_roll);
			
			$actor->setAcSlash($this->ac_slash);
			$actor->setAcBash($this->ac_bash);
			$actor->setAcPierce($this->ac_pierce);
			$actor->setAcMagic($this->ac_magic);
			
		}
		
		public function getRaceStr() { return get_class($this); }
		public function getMaxStr() { return $this->max_str; }
		public function getMaxInt() { return $this->max_int; }
		public function getMaxWis() { return $this->max_wis; }
		public function getMaxDex() { return $this->max_dex; }
		public function getMaxCon() { return $this->max_con; }
		public function getMovementCost() { return $this->movement_cost; }
		public function getUnarmedVerb() { return $this->unarmed_verb; }
		public function getMoveVerb() { return $this->move_verb; }
		public function getDecreaseNourishment() { return $this->decrease_nourishment; }
		public function getDecreaseThirst() { return $this->decrease_thirst; }
		public function getFull() { return $this->full; }
		public function getHitRoll() { return $this->hit_roll; }
		public function getDamRoll() { return $this->dam_roll; }
		public function getAcSlash() { return $this->ac_slash; }
		public function getAcBash() { return $this->ac_bash; }
		public function getAcPierce() { return $this->ac_pierce; }
		public function getAcMagic() { return $this->ac_magic; }
	}
?>
