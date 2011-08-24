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
		private $equipment = array();
		private $inventory = null;
		private $actor = null;
		private static $labels = array
		(
			\Items\Equipment::POSITION_LIGHT =>		'<used as light>      ',
			\Items\Equipment::POSITION_FINGER =>	'<worn on finger>     ',
			\Items\Equipment::POSITION_NECK =>		'<worn around neck>   ',
			\Items\Equipment::POSITION_HEAD => 		'<worn on head>       ',
			\Items\Equipment::POSITION_LEGS => 		'<worn on legs>       ',
			\Items\Equipment::POSITION_FEET => 		'<worn on feet>       ',
			\Items\Equipment::POSITION_HANDS => 	'<worn on hands>      ',
			\Items\Equipment::POSITION_ARMS => 		'<worn on arms>       ',
			\Items\Equipment::POSITION_TORSO => 	'<worn on torso>      ',
			\Items\Equipment::POSITION_BODY => 		'<worn about body>    ',
			\Items\Equipment::POSITION_WAIST => 	'<worn about waist>   ',
			\Items\Equipment::POSITION_WRIST =>		'<worn around wrist>  ',
			\Items\Equipment::POSITION_WIELD => 	'<wielded in hand>    ',
			\Items\Equipment::POSITION_FLOAT => 	'<floating nearby>    '
		);
		
		public function __construct(Actor $actor)
		{
			
			$this->equipment = array
			(
				\Items\Equipment::POSITION_LIGHT => null,
				\Items\Equipment::POSITION_FINGER => null,
				\Items\Equipment::POSITION_FINGER => null,
				\Items\Equipment::POSITION_NECK => null,
				\Items\Equipment::POSITION_NECK => null,
				\Items\Equipment::POSITION_BODY => null,
				\Items\Equipment::POSITION_HEAD => null,
				\Items\Equipment::POSITION_LEGS => null,
				\Items\Equipment::POSITION_FEET => null,
				\Items\Equipment::POSITION_HANDS => null,
				\Items\Equipment::POSITION_ARMS => null,
				\Items\Equipment::POSITION_TORSO => null,
				\Items\Equipment::POSITION_WAIST => null,
				\Items\Equipment::POSITION_WRIST => null,
				\Items\Equipment::POSITION_WRIST => null,
				\Items\Equipment::POSITION_WIELD => null,
				\Items\Equipment::POSITION_WIELD => null,
				\Items\Equipment::POSITION_FLOAT => null
			);
			
			if($actor)
			{
				$this->actor = $actor;
			}
			$this->inventory = new Inventory();
		}
		
		public function getInventory()
		{
			return $this->inventory;
		}
		
		public function equip(\Items\Equipment $item, $display_message = true)
		{
			
			if($item->getEquipmentType() === \Items\Equipment::TYPE_GENERIC)
			{
				if($display_message)
					Server::out($this->actor, "You can't wear that.");
				return false;
			}
			
			$positions = array_keys(self::$types, $item->getEquipmentType());
			
			$equipped = $dequipped = null;
			$i = 0;
			foreach($positions as $position)
			{
				$i++;
				if($this->equipment[$position] === null)
				{
					if($this->actor->getInventory()->remove($item) !== false)
						$this->inventory->add($item);
					$this->equipment[$position] = $item;
					foreach($item->getAffects() as $affect)
						$affect->apply($this->target);
					$equipped = $item;
					$equipped_position = $position;
					break;
				}
				if($this->equipment[$position] !== null && $i == sizeof($positions))
				{
					$item_remove = $this->equipment[$position];
					$this->inventory->remove($item_remove);
					$this->inventory->add($item);
					foreach($item_remove->getAffects() as $affect)
						$this->actor->removeAffect($affect);
					foreach($item->getAffects() as $affect)
						$affect->apply($this->target);
					$this->actor->getInventory()->add($item_remove);
					$this->actor->getInventory()->remove($item);
					$this->equipment[$position] = $item;
					$equipped = $item;
					$dequipped = $item_remove;
					$equipped_position = $position;
					break;
				}
			}
			
			if(!$display_message)
				return;
			
			if($dequipped)
			{
				$msg_you = "You remove " . $dequipped->getShort() . " and "; // . $equipped->getShort() . ' ' . $this->equipPositionLabel($actor, $equipped_position, true) . '.';
				$msg_others = $this->actor->getAlias(true) . " removes " . $dequipped->getShort() . " and "; //wears " . $equipped->getShort() . ' ' . $this->equipPositionLabel($actor, $equipped_position) . '.';
			}
			else
			{
				$msg_you = "You ";
				$msg_others = $this->actor->getAlias(true) . " ";
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
			
			$sex = $this->actor->getSex() == 'm' ? 'his' : 'her';
			
			switch($equipped->getEquipmentType())
			{
				case \Items\Equipment::TYPE_LIGHT:
					$msg_you .= ' as a light.';
					$msg_others .= ' as a light.';
					break;
				case \Items\Equipment::TYPE_FLOAT:
					$msg_you .= ' to float around nearby.';
					$msg_others .= ' to float around nearby.';
					break;
				case \Items\Equipment::TYPE_WIELD:
					$msg_you .= '.';
					$msg_others .= '.';
					break;
				case \Items\Equipment::TYPE_FINGER:
					$msg_you .= ' on your finger.';
					$msg_others .= 'on ' . $sex . ' finger.';
					break;
				case \Items\Equipment::TYPE_ARMS:
					$msg_you .= ' on your arms.';
					$msg_others .= 'on ' . $sex . ' arms.';
					break;
				case \Items\Equipment::TYPE_BODY:
					$msg_you .= ' around your body.';
					$msg_others .= ' around ' . $sex . ' body.';
					break;
				case \Items\Equipment::TYPE_FEET:
					$msg_you .= ' on your feet.';
					$msg_others .= ' on ' . $sex . ' feet.';
					break;
				case \Items\Equipment::TYPE_HEAD:
					$msg_you .= ' on your head.';
					$msg_others .= ' on ' . $sex . ' head.';
					break;
				case \Items\Equipment::TYPE_HANDS:
					$msg_you .= ' on your hands.';
					$msg_others .= ' on ' . $sex . ' hands.';
					break;
				case \Items\Equipment::TYPE_HOLD:
					$msg_you .= ' in your hand.';
					$msg_others .= ' in ' . $sex . ' hand.';
					break;
				case \Items\Equipment::TYPE_TORSO:
					$msg_you .= ' around your torso.';
					$msg_others .= ' around ' . $sex . ' torso.';
					break;
				case \Items\Equipment::TYPE_WAIST:
					$msg_you .= ' around your waist.';
					$msg_others .= ' around ' . $sex . ' waist.';
					break;
				case \Items\Equipment::TYPE_WRIST:
					$msg_you .= ' on your wrist.';
					$msg_others .= ' on ' . $sex . ' wrist.';
					break;
			}
			
			Server::out($this->actor, $msg_you);
			$actors = $this->actor->getRoom()->getActors();
			foreach($actors as $a)
				if($actor->getAlias() != $a->getAlias())
					Server::out($a, $msg_others);
		}
		
		public function removeByPosition($position)
		{
			
			if($this->equipment[$position] instanceof \Items\Equipment)
			{
				$this->getInventory()->remove($item);
				$this->actor->removeAffects($item->getAffects());
				$item = $this->equipment[$position];
				$this->actor->getInventory()->add($item);
				$this->equipment[$position] = null;
			}
			else
				Server::out($this->actor, 'Nothing is there.');
			
		}
		
		public function remove(\Items\Equipment $item)
		{
		
			$i = array_search($item, $this->equipment);
			if($i !== false)
			{
				$this->getInventory()->remove($item);
				$this->actor->getInventory()->add($item);
				foreach($item->getAffects() as $affect)
					$this->actor->removeAffect($affect);
				$this->equipment[$i] = null;
			}
			else
				Server::out($this->actor, 'Nothing is there.');
		
		}
		
		public function getEquipmentByPosition($position)
		{
			return $this->equipment[$position];
		}
		
		public function displayContents()
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
				if($eq instanceof \Items\Equipment)
					$buffer .= '      ' . $eq->getShort() . "\n";
				else
					$buffer .= "      nothing\n";
			}

			return $buffer;
		}
	
	}

?>
