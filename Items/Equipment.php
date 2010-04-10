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

	abstract class Equipment extends Item
	{
	
		protected $equipment_type;
		protected $condition = 100;
		
		const TYPE_LIGHT = 0;
		const TYPE_FINGER = 1;
		const TYPE_NECK = 2;
		const TYPE_BODY = 3;
		const TYPE_HEAD = 4;
		const TYPE_LEGS = 5;
		const TYPE_FEET = 6;
		const TYPE_HANDS = 7;
		const TYPE_ARMS = 8;
		const TYPE_TORSO = 9;
		const TYPE_WAIST = 10;
		const TYPE_WRIST = 11;
		const TYPE_WIELD = 12;
		const TYPE_HOLD = 13;
		const TYPE_FLOAT = 14;
		
		public function __construct($id, $long, $short, $nouns, $value, $weight, $type, $equipment_type, $condition, $can_own, $door_unlock_id)
		{
			
			parent::__construct($id, $long, $short, $nouns, $value, $weight, $type, $can_own, $door_unlock_id);
			$this->condition = $condtion;
			$this->equipment_type = $equipment_type;
		}
		
		public function getCondition() { return $this->condition; }
		public function decreaseCondition($amount) { $this->condition -= $amount; }
		public function increaseCondition($amount) { $this->condition += $amount; }
		public function getEquipmentType() { return $this->position; }
	
	}

?>
