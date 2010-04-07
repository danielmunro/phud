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

	class Equipment
	{
	
		const HEAD = 'head';
		const NECK = 'neck';
		const TORSO = 'torso';
		const LEGS = 'legs';
		const ARMS = 'arms';
		const RING = 'ring';
		const HANDS = 'hands';
		const FEET = 'feet';
		const WEAPON = 'weapon';
		const HELD = 'held';
		
		private $can_dual_wield = false;
		private $equipment;
	
		public function __construct()
		{
		
			$this->equipment = array
			(
				self::HEAD => null,
				self::NECK => null,
				self::TORSO => null,
				self::LEGS => null,
				self::ARMS => null,
				self::RING => null,
				self::HANDS => null,
				self::FEET => null,
				self::WEAPON => null,
				self::HELD => null
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
				{
					if(strpos($noun, $input[1]) === 0)
					{
						$eq[] = $equipment;
					}
				}
			}
			return $eq;
			
		}
		
		public function equip(Actor &$actor, Item $item)
		{
			
			if($this->equipment[$item->getEquipmentPosition()] instanceof Item)
			{
				$this->removeByPosition($actor, $item->getEquipmentPosition());
			}
			
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
			{
				Server::out($actor, 'Nothing is there.');
			}
			
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
			{
				Server::out($actor, 'Nothing is there.');
			}
		
		}
		
		public function getEquipmentByPosition($position) { return $this->equipment[$position]; }
		
		public function displayContents($actor)
		{
		
			$buffer = '';
			$viewed = false;
			foreach($this->equipment as $key => $eq)
			{
				if(!($eq instanceof Item))
				{
					continue;
				}
				$viewed = true;
				$buffer .= 'Worn on ' . $key;
				if($eq instanceof Item)
				{
					$buffer .= '      ' . $eq->getShort() . "\n";
				}
				else
				{
					$buffer .= "      nothing\n";
				}
			}
			if(!$viewed)
			{
				$buffer = 'Not wearing anything.';
			}
			return $buffer;
		}
	
	}

?>
