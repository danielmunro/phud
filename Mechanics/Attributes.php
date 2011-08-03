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
	class Attributes
	{
		private $str = 0;
		private $int = 0;
		private $wis = 0;
		private $dex = 0;
		private $con = 0;
		private $cha = 0;
		
		private $max_str = 0;
		private $max_int = 0;
		private $max_wis = 0;
		private $max_dex = 0;
		private $max_con = 0;
		private $max_cha = 0;
		
		private $hp = 20;
		private $max_hp = 20;
		private $mana = 100;
		private $max_mana = 100;
		private $movement = 100;
		private $max_movement = 100;
		
		private $ac_bash = 100;
		private $ac_slash = 100;
		private $ac_pierce = 100;
		private $ac_magic = 100;
		
		private $hit = 1;
		private $dam = 1;
		
		///////////////////////////////////////////////////////////////////////
		// Attribute getters and setters
		///////////////////////////////////////////////////////////////////////
		
		public function setStr($str)
		{
			$this->str = $str;
		}
		
		public function setInt($int)
		{
			$this->int = $int;
		}
		
		public function setWis($wis)
		{
			$this->wis = $wis;
		}
		
		public function setDex($dex)
		{
			$this->dex = $dex;
		}
		
		public function setCon($con)
		{
			$this->con = $con;
		}
		
		public function setCha($cha)
		{
			$this->cha = $cha;
		}
		
		public function getStr()
		{
			return $this->str;
		}
		
		public function getInt()
		{
			return $this->int;
		}
		
		public function getWis()
		{
			return $this->wis;
		}
		
		public function getDex()
		{
			return $this->dex;
		}
		
		public function getCon()
		{
			return $this->con;
		}
		
		public function getCha()
		{
			return $this->cha;
		}
		
		public function setMaxStr($str)
		{
			$this->max_str = $str;
		}
		
		public function setMaxInt($int)
		{
			$this->max_int = $int;
		}
		
		public function setMaxWis($wis)
		{
			$this->max_wis = $wis;
		}
		
		public function setMaxDex($dex)
		{
			$this->max_dex = $dex;
		}
		
		public function setMaxCon($con)
		{
			$this->max_con = $con;
		}
		
		public function setMaxCha($cha)
		{
			$this->max_cha = $cha;
		}
		
		public function getMaxStr()
		{
			return $this->max_str;
		}
		
		public function getMaxInt()
		{
			return $this->max_int;
		}
		
		public function getMaxWis()
		{
			return $this->max_wis;
		}
		
		public function getMaxDex()
		{
			return $this->max_dex;
		}
		
		public function getMaxCon()
		{
			return $this->max_con;
		}
		
		public function getMaxCha()
		{
			return $this->max_cha;
		}
		
		public function setHp($hp)
		{
			$this->hp = $hp;
		}
		
		public function setMana($mana)
		{
			$this->mana = $mana;
		}
		
		public function setMovement($movement)
		{
			$this->movement = $movement;
		}
		
		public function getHp()
		{
			return $this->hp;
		}
		
		public function getMana()
		{
			return $this->mana;
		}
		
		public function getMovement()
		{
			return $this->movement;
		}
		
		public function setMaxHp($hp)
		{
			$this->max_hp = $hp;
		}
		
		public function setMaxMana($mana)
		{
			$this->max_mana = $mana;
		}
		
		public function setMaxMovement($movement)
		{
			$this->max_movement = $movement;
		}
		
		public function getMaxHp()
		{
			return $this->max_hp;
		}
		
		public function getMaxMana()
		{
			return $this->max_mana;
		}
		
		public function getMaxMovement()
		{
			return $this->max_movement;
		}
		
		public function setAcBash($ac)
		{
			$this->ac_bash = $ac;
		}
		
		public function setAcSlash($ac)
		{
			$this->ac_slash = $ac;
		}
		
		public function setAcPierce($ac)
		{
			$this->ac_pierce = $ac;
		}
		
		public function setAcMagic($ac)
		{
			$this->ac_magic = $ac;
		}
		
		public function getAcBash()
		{
			return $this->ac_bash;
		}
		
		public function getAcPierce()
		{
			return $this->ac_pierce;
		}
		
		public function getAcMagic()
		{
			return $this->ac_magic;
		}
		
		public function getAcSlash()
		{
			return $this->ac_slash;
		}
		
		public function setHit($hit)
		{
			$this->hit = $hit;
		}
		
		public function setDam($dam)
		{
			$this->dam = $dam;
		}
		
		public function getHit()
		{
			return $this->hit;
		}
		
		public function getDam()
		{
			return $this->dam;
		}
	}
?>
