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
	class Item implements \Mechanics\Affectable
	{
	
		protected $short = '';
		protected $long = '';
		protected $nouns = '';
		protected $value = 0;
		protected $weight = 0.0;
		protected $type = 0;
		protected $can_own = true;
		protected $door_unlock_id = null;
		protected $affects = array();
		
		const TYPE_ITEM = 1;
		const TYPE_CONTAINER = 2;
		const TYPE_FOOD = 3;
		const TYPE_DRINK = 4;
		const TYPE_WEAPON = 5;
		const TYPE_ARMOR = 6;
		
		public function __construct($id, $long, $short, $nouns, $value, $weight, $type, $can_own = true, $door_unlock_id = null)
		{
			$this->id = $id;
			$this->long = $long;
			$this->short = $short;
			$this->nouns = $nouns;
			$this->value = $value;
			$this->weight = $weight;
			$this->type = $type;
			$this->can_own = $can_own;
			$this->door_unlock_id = $door_unlock_id;
		}
		
		public function getShort() { return $this->short; }
		public function getLong() { return $this->long; }
		public function getNouns() { return $this->nouns; }
		public function getVerb() { return $this->verb; }
		public function getCanOwn() { return $this->can_own; }
		public function getValue() { return $this->value; }
		public function getType() { return $this->type; }
		public function getDoorUnlockId() { return $this->door_unlock_id; }
		public function addAffect(\Mechanics\Affect $affect) { $this->affects[] = $affect; }
		public function removeAffect(\Mechanics\Affect $affect)
		{
			return;
		}
		public function getAffects() { return $this->affects; }
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
			$class = get_class($this);
			return substr($class, strpos($class, '\\') + 1);
		}
	}

?>
