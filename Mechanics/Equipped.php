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
		const POSITION_FLOAT = 18;
		
		private $equipment;
		private static $types = array
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
			self::POSITION_FLOAT => Equipment::TYPE_FLOAT
		);
		private static $labels = array
		(
			self::POSITION_LIGHT =>		'<used as light>      ',
			self::POSITION_FINGER_L => '<worn on finger>     ',
			self::POSITION_FINGER_R => '<worn on finger>     ',
			self::POSITION_NECK_1 => 	'<worn around neck>   ',
			self::POSITION_NECK_2 => 	'<worn around neck>   ',
			self::POSITION_HEAD => 		'<worn on head>       ',
			self::POSITION_LEGS => 		'<worn on legs>       ',
			self::POSITION_FEET => 		'<worn on feet>       ',
			self::POSITION_HANDS => 	'<worn on hands>      ',
			self::POSITION_ARMS => 		'<worn on arms>       ',
			self::POSITION_TORSO => 	'<worn on torso>      ',
			self::POSITION_BODY => 		'<worn about body>    ',
			self::POSITION_WAIST => 	'<worn about waist>   ',
			self::POSITION_WRIST_L => 	'<worn around wrist>  ',
			self::POSITION_WRIST_R => 	'<worn around wrist>  ',
			self::POSITION_WIELD_L => 	'<wielded in hand>    ',
			self::POSITION_WIELD_R => 	'<wielded in hand>    ',
			self::POSITION_FLOAT => 	'<floating nearby>    '
		);
		
		public function __construct()
		{
			
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
				self::POSITION_FLOAT => null
			);
		
		}
		
		public function equip(Actor &$actor, \Items\Equipment $item)
		{
			
			$positions = array_keys(self::$types, $item->getEquipmentType());
			
			$equipped = $dequipped = null;
			$i = 0;
			foreach($positions as $position)
			{
				$i++;
				if($this->equipment[$position] === null)
				{
					$actor->getInventory()->remove($item);
					$this->equipment[$position] = $item;
					$equipped = $item;
					$equipped_position = $position;
					break;
				}
				if($this->equipment[$position] !== null && $i == sizeof($positions))
				{
					$item_remove = $this->equipment[$position];
					$actor->getInventory()->add($item_remove);
					$actor->getInventory()->remove($item);
					$this->equipment[$position] = $item;
					$equipped = $item;
					$dequipped = $item_remove;
					$equipped_position = $position;
					break;
				}
			}
			
			if($dequipped)
			{
				$msg_you = "You remove " . $dequipped->getShort() . " and "; // . $equipped->getShort() . ' ' . $this->equipPositionLabel($actor, $equipped_position, true) . '.';
				$msg_others = $actor->getAlias(true) . " removes " . $dequipped->getShort() . " and "; //wears " . $equipped->getShort() . ' ' . $this->equipPositionLabel($actor, $equipped_position) . '.';
			}
			else
			{
				$msg_you = "You ";
				$msg_others = $actor->getAlias(true) . " ";
			}
			
			if($equipped->getEquipmentType() == \Items\Equipment::TYPE_WIELD)
			{
				$msg_you .= 'wield ';
				$msg_others .= 'wields ';
			}
			else if($equipped->getEquipmentType() == \Items\Equipment::TYPE_FLOAT)
			{
				$msg_you .= 'releases ';
				$msg_others .= 'releases ';
			}
			else if($equipped->getEquipmentType() == \Items\Equipment::TYPE_HOLD)
			{
				$msg_you .= 'hold ';
				$msg_others .= 'holds ';
			}
			else
			{
				$msg_you .= 'wear ';
				$msg_others .= 'wears ';
			}
			
			$msg_you .= $item->getShort();
			$msg_others .= $item->getShort();
			
			$sex = $actor->getSex() == 'm' ? 'his' : 'her';
			
			switch($equipped->getEquipmentType())
			{
				case Equipment::TYPE_LIGHT:
					$msg_you .= ' as a light.';
					$msg_others .= ' as a light.';
					break;
				case Equipment::TYPE_FLOAT:
					$msg_you .= ' to float around nearby.';
					$msg_others .= ' to float around nearby.';
					break;
				case Equipment::TYPE_WIELD:
					$msg_you .= '.';
					$msg_others .= '.';
					break;
				case Equipment::TYPE_FINGER:
					$msg_you .= ' on your finger.';
					$msg_others .= 'on ' . $sex . ' finger.';
					break;
				case Equipment::TYPE_ARMS:
					$msg_you .= ' on your arms.';
					$msg_others .= 'on ' . $sex . ' arms.';
					break;
				case Equipment::TYPE_BODY:
					$msg_you .= ' around your body.';
					$msg_others .= ' around ' . $sex . ' body.';
					break;
				case Equipment::TYPE_FEET:
					$msg_you .= ' on your feet.';
					$msg_others .= ' on ' . $sex . ' feet.';
					break;
				case Equipment::TYPE_HEAD:
					$msg_you .= ' on your head.';
					$msg_others .= ' on ' . $sex . ' head.';
					break;
				case Equipment::TYPE_HANDS:
					$msg_you .= ' on your hands.';
					$msg_others .= ' on ' . $sex . ' hands.';
					break;
				case Equipment::TYPE_HOLD:
					$msg_you .= ' in your hand.';
					$msg_others .= ' in ' . $sex . ' hand.';
					break;
				case Equipment::TYPE_TORSO:
					$msg_you .= ' around your torso.';
					$msg_others .= ' around ' . $sex . ' torso.';
					break;
				case Equipment::TYPE_WAIST:
					$msg_you .= ' around your waist.';
					$msg_others .= ' around ' . $sex . ' waist.';
					break;
				case Equipment::TYPE_WRIST:
					$msg_you .= ' on your wrist.';
					$msg_others .= ' on ' . $sex . ' wrist.';
					break;
			}
			
			Server::out($actor, $msg_you);
			foreach(ActorObserver::instance()->getActorsInRoom($actor->getRoom()->getId()) as $a)
				if($actor->getAlias() != $a->getAlias())
					Server::out($a, $msg_others);
		}
		
		public function removeByPosition(Actor &$actor, $position)
		{
			
			if($this->equipment[$position] instanceof Equipment)
			{
				$item = $this->equipment[$position];
				$actor->getInventory()->add($item);
				$this->equipment[$position] = null;
			}
			else
				Server::out($actor, 'Nothing is there.');
			
		}
		
		public function remove(Actor &$actor, Equipment $item)
		{
		
			if(in_array($item, $this->equipment))
			{
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
				$buf = self::$labels[$key];
				$len_diff = 22 - strlen($buf);
				for($i = 0; $i < $len_diff; $i++)
					$buf .= ' ';
				$buffer .= $buf;
				if($eq instanceof Equipment)
					$buffer .= '      ' . $eq->getShort() . "\n";
				else
					$buffer .= "      nothing\n";
			}

			return $buffer;
		}
	
	}

?>
