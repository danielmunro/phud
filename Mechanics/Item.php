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
	abstract class Item implements Affectable
	{
	
		protected $short = 'a generic item';
		protected $long = 'A generic item lays here';
		protected $nouns = 'generic';
		protected $value = 0;
		protected $weight = 0.0;
		protected $can_own = true;
		protected $attributes = null;
		protected $affects = array();
		
		public function __construct()
		{
			$this->attributes = new Attributes();
		}
		
		public function setShort($short)
		{
			$this->short = $short;
		}
		
		public function setLong($long)
		{
			$this->long = $long;
		}
		
		public function setNouns($nouns)
		{
			$this->nouns = $nouns;
		}
		
		public function setValue($value)
		{
			$this->value = $value;
		}
		
		public function setWeight($weight)
		{
			$this->weight = $weight;
		}
		
		public function setCanOwn($can_own)
		{
			$this->can_own = $can_own;
		}
		
		public function getShort()
		{
			return $this->short;
		}
		
		public function getLong()
		{
			return $this->long;
		}
		
		public function getNouns()
		{
			return $this->nouns;
		}
		
		public function getCanOwn()
		{
			return $this->can_own;
		}
		
		public function getValue()
		{
			return $this->value;
		}
		
		public function addAffect(\Mechanics\Affect $affect)
		{
			$this->affects[] = $affect;
		}
		
		public function removeAffect(\Mechanics\Affect $affect)
		{
			$key = array_search($affect, $this->affects);
			if($key !== false)
			{
				unset($this->affects[$key]);
				$this->affects = array_values($this->affects);
			}
		}
		
		public function getAffects()
		{
			return $this->affects;
		}
		
		public function lookDescribe()
		{
			return $this->long;
		}
		
		public function transferOwnership(\Mechanics\Actor $from, \Mechanics\Actor $to)
		{
			$from->getInventory()->remove($this);
			$to->getInventory()->add($this);
		}
		
		public function __toString()
		{
			return $this->short;
		}
	}

?>
