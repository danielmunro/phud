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

	class Weapon extends Equipment
	{
	
		protected $hit_roll;
		protected $dam_roll;
		protected $weapon_type;
		
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
		
		public function __construct($id, $long, $short, $nouns, $value, $weight, $weapon_type, $hit_roll, $dam_roll, $condition = 100, $can_own = true, $door_unlock_id = null)
		{
			
			parent::__construct($id, $long, $short, $nouns, $value, $weight, Item::TYPE_WEAPON, Equipment::TYPE_WIELD, $condition, $can_own, $door_unlock_id);
			$this->weapon_type = $weapon_type;
			$this->hit_roll = $hit_roll;
			$this->dam_roll = $dam_roll;
		}
		public function save($inv_inside_id)
		{
			if($this->id)
				return Db::getInstance()->query(
					'UPDATE items SET
						short_desc = ?,
						long_desc = ?,
						nouns = ?,
						value = ?,
						weight = ?,
						item_type = ?,
						can_own = ?,
						fk_inv_inside_id = ?,
						fk_door_unlock_id = ?,
						weapon_type = ?,
						hit_roll = ?,
						dam_roll = ?
					WHERE
						id = ?', array($this->short, $this->long, $this->nouns, $this->value,
						$this->weight, $this->type, $this->can_own, $inv_inside_id,
						$this->door_unlock_id, $this->weapon_type, $this->hit_roll, $this->dam_roll, $this->id));
			
			Db::getInstance()->query(
				'INSERT INTO items (
					short_desc,
					long_desc,
					nouns,
					value,
					weight,
					item_type,
					can_own,
					fk_inv_inside_id,
					fk_door_unlock_id,
					weapon_type,
					hit_roll,
					dam_roll)
				VALUES
					(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
				array($this->short, $this->long, $this->nouns, $this->value, $this->weight,
				$this->type, $this->can_own, $inv_inside_id, $this->door_unlock_id,
				$this->weapon_type, $this->hit_roll, $this->dam_roll));
			$this->id = Db::getInstance()->insert_id;
		}
		public function getWeaponType() { return $this->weapon_type; }
		public function getHitRoll() { return $this->hit_roll; }
		public function getDamRoll() { return $this->dam_roll; }
	
	}

?>
