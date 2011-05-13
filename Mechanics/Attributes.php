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
		
		public function load($table, $row_id)
		{
			$row = Db::getInstance()->query("SELECT * FROM attributes WHERE fk_table = ? AND fk_id = ?", array($table, $row_id))->getResult()->fetch_object();
			$props = get_object_vars($row);
			foreach($props as $prop => $val)
				if(property_exists($this, $prop))
					$this->$prop = $val;
		}
		
		public function save($table_id, $row_id)
		{
			// on duplicate key update...
			Db::getInstance()->query("INSERT INTO attributes (str, `int`, wis, dex, con, hp, max_hp, mana, max_mana, movement, max_movement, ac_bash, ac_slash, ac_pierce, 
										ac_magic, hit, dam, fk_table, fk_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", array($this->str, $this->int, 
										$this->wis, $this->dex, $this->con, $this->hp, $this->max_hp, $this->mana, $this->max_mana, $this->movement, $this->max_movement, 
										$this->ac_bash, $this->ac_slash, $this->ac_pierce, $this->ac_magic, $this->hit, $this->dam, $table_id, $row_id));
		}
		
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
