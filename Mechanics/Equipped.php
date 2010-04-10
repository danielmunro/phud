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

	class Equipped
	{
	
		const POSITION_LIGHT = 0;
		const POSITION_FINGER_L = 1;
		const POSITION_FINGER_R = 2;
		const POSITION_NECK_1 = 3;
		const POSITION_NECK_2 = 4;
		const POSITION_BODY = 5;
		const POSITION_HEAD = 6;
		const POSITION_LEGS = 7;
		const POSITION_FEET = 8;
		const POSITION_HANDS = 9;
		const POSITION_ARMS = 10;
		const POSITION_TORSO = 11;
		const POSITION_WAIST = 12;
		const POSITION_WRIST_L = 13;
		const POSITION_WRIST_R = 14;
		const POSITION_WIELD_L = 15;
		const POSITION_WIELD_R = 16;
		const POSITION_HOLD = 17;
		const POSITION_FLOAT = 18;
		
		private $equipment;
		private $types;
		
		public function __construct()
		{
		
			$this->types = array
			(
				self::POSITION_LIGHT => Equipment::TYPE_LIGHT,
				self::POSITION_FINGER_L => Equipment::TYPE_FINGER,
				self::POSITION_FINGER_R => Equipment::TYPE_FINGER,
				self::POSITION_NECK_1 => Equipment::TYPE_NECK,
				self::POSITION_NECK_2 => Equipment::TYPE_NECK,
				self::POSITION_BODY => Equipment::TYPE_BODY,
				self::POSITION_HEAD => Equipment::TYPE_HEAD,
				self::POSITION_LEGS => Equipment::TYPE_LEGS,
				self::POSITION_FEET => Equipment::TYPE_FEET,
				self::POSITION_HANDS => Equipment::TYPE_HANDS,
				self::POSITION_ARMS => Equipment::TYPE_ARMS,
				self::POSITION_TORSO => Equipment::TYPE_TORSO,
				self::POSITION_WAIST => Equipment::TYPE_WAIST,
				self::POSITION_WRIST_L => Equipment::TYPE_WRIST,
				self::POSITION_WRIST_R => Equipment::TYPE_WRIST,
				self::POSITION_WIELD_L => Equipment::TYPE_WIELD,
				self::POSITION_WIELD_R => Equipment::TYPE_WIELD,
				self::POSITION_HOLD => Equipment::TYPE_HOLD,
				self::POSITION_FLOAT => Equipment::TYPE_FLOAT
			);
			
			$this->equipment = array
			(
				self::POSITION_LIGHT => null,
				self::POSITION_FINGER_L => null,
				self::POSITION_FINGER_R => null,
				self::POSITION_NECK_1 => null,
				self::POSITION_NECK_2 => null,
				self::POSITION_BODY => null,
				self::POSITION_HEAD => null,
				self::POSITION_LEGS => null,
				self::POSITION_FEET => null,
				self::POSITION_HANDS => null,
				self::POSITION_ARMS => null,
				self::POSITION_TORSO => null,
				self::POSITION_WAIST => null,
				self::POSITION_WRIST_L => null,
				self::POSITION_WRIST_R => null,
				self::POSITION_WIELD_L => null,
				self::POSITION_WIELD_R => null,
				self::POSITION_HOLD => null,
				self::POSITION_FLOAT => null
			);
		
		}
		
		public function getEquipmentByInput($input)
		{
			
			$eq = array();
			foreach($this->equipment as $equipment)
			{
				
				if(!($equipment instanceof Item))
					continue;
				$nouns = $equipment->getNouns();
				if(!is_array($nouns))
					$nouns = explode(' ', $nouns);
				foreach($nouns as $noun)
					if(strpos($noun, $input[1]) === 0)
						$eq[] = $equipment;
			}
			return $eq;
			
		}
		
		public function equip(Actor &$actor, Item $item)
		{
			
			if($this->equipment[$item->getEquipmentPosition()] instanceof Item)
				$this->removeByPosition($actor, $item->getEquipmentPosition());
			
			$this->equipment[$item->getEquipmentPosition()] = $item;
			$this->equipment[$item->getEquipmentPosition()]->equipAction($actor);
			$actor->getInventory()->remove($item);
			
		}
		
		public function removeByPosition(Actor &$actor, $position)
		{
			
			if($this->equipment[$position] instanceof Item)
			{
				$item = $this->equipment[$position];
				$this->equipment[$position]->dequipAction($actor);
				$actor->getInventory()->add($item);
				$this->equipment[$position] = null;
			}
			else
				Server::out($actor, 'Nothing is there.');
			
		}
		
		public function remove(Actor &$actor, Item $item)
		{
		
			if(in_array($item, $this->equipment))
			{
				$this->equipment[$item->getEquipmentPosition()]->dequipAction($actor);
				$actor->getInventory()->add($item);
				$this->equipment[$item->getEquipmentPosition()] = null;
			}
			else
				Server::out($actor, 'Nothing is there.');
		
		}
		
		public function getEquipmentByPosition($position) { return $this->equipment[$position]; }
		
		public function displayContents($actor)
		{
		
			$buffer = '';
			$viewed = false;
			foreach($this->equipment as $key => $eq)
			{
				if(!($eq instanceof Item))
					continue;
				
				$viewed = true;
				$buffer .= 'Worn on ' . $key;
				if($eq instanceof Item)
					$buffer .= '      ' . $eq->getShort() . "\n";
				else
					$buffer .= "      nothing\n";
			}
			if(!$viewed)
				$buffer = 'Not wearing anything.';
			return $buffer;
		}
	
	}

?>
