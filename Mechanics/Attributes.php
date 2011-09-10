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
		private $str = 10;
		private $int = 10;
		private $wis = 10;
		private $dex = 10;
		private $con = 10;
		private $cha = 10;
		
		private $hp = 1;
		private $mana = 1;
		private $movement = 1;
		
		private $ac_bash = 0;
		private $ac_slash = 0;
		private $ac_pierce = 0;
		private $ac_magic = 0;
		
		private $hit = 0;
		private $dam = 0;
		
		private $saves = 0;
		
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
		
		public function setSaves($saves)
		{
			$this->saves = $saves;
		}
		
		public function getSaves()
		{
			return $this->saves;
		}
		
		public function getAttributeLabels()
		{
			$msg = '';
			if($this->str)
				$msg .= 'Affects str by '.$this->str."\n";
			if($this->int)
				$msg .= 'Affects int by '.$this->int."\n";
			if($this->wis)
				$msg .= 'Affects wis by '.$this->wis."\n";
			if($this->dex)
				$msg .= 'Affects dex by '.$this->dex."\n";
			if($this->con)
				$msg .= 'Affects con by '.$this->con."\n";
			if($this->cha)
				$msg .= 'Affects cha by '.$this->cha."\n";
			if($this->hp)
				$msg .= 'Affects hp by '.$this->hp."\n";
			if($this->mana)
				$msg .= 'Affects mana by '.$this->mana."\n";
			if($this->movement)
				$msg .= 'Affects movements by '.$this->movement."\n";
			if($this->ac_bash)
				$msg .= 'Affects bash ac by '.$this->ac_bash."\n";
			if($this->ac_slash)
				$msg .= 'Affects slash ac by '.$this->ac_slash."\n";
			if($this->ac_pierce)
				$msg .= 'Affects pierce ac by '.$this->ac_pierce."\n";
			if($this->ac_magic)
				$msg .= 'Affects magic ac by '.$this->ac_magic."\n";
			if($this->hit)
				$msg .= 'Affects hit roll by '.$this->hit."\n";
			if($this->dam)
				$msg .= 'Affects dam roll by '.$this->dam."\n";
			if($this->saves)
				$msg .= 'Affects saves by '.$this->saves."\n";
			return $msg;
		}
	}
?>
