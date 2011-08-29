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
			
			$this->equipment[] = array('position' => \Items\Equipment::POSITION_LIGHT, 'equipped' => null);
			$this->equipment[] = array('position' => \Items\Equipment::POSITION_FINGER, 'equipped' => null);
			$this->equipment[] = array('position' => \Items\Equipment::POSITION_FINGER, 'equipped' => null);
			$this->equipment[] = array('position' => \Items\Equipment::POSITION_NECK, 'equipped' => null);
			$this->equipment[] = array('position' => \Items\Equipment::POSITION_NECK, 'equipped' => null);
			$this->equipment[] = array('position' => \Items\Equipment::POSITION_BODY, 'equipped' => null);
			$this->equipment[] = array('position' => \Items\Equipment::POSITION_HEAD, 'equipped' => null);
			$this->equipment[] = array('position' => \Items\Equipment::POSITION_LEGS, 'equipped' => null);
			$this->equipment[] = array('position' => \Items\Equipment::POSITION_FEET, 'equipped' => null);
			$this->equipment[] = array('position' => \Items\Equipment::POSITION_HANDS, 'equipped' => null);
			$this->equipment[] = array('position' => \Items\Equipment::POSITION_ARMS, 'equipped' => null);
			$this->equipment[] = array('position' => \Items\Equipment::POSITION_TORSO, 'equipped' => null);
			$this->equipment[] = array('position' => \Items\Equipment::POSITION_WAIST, 'equipped' => null);
			$this->equipment[] = array('position' => \Items\Equipment::POSITION_WRIST, 'equipped' => null);
			$this->equipment[] = array('position' => \Items\Equipment::POSITION_WRIST, 'equipped' => null);
			$this->equipment[] = array('position' => \Items\Equipment::POSITION_WIELD, 'equipped' => null);
			$this->equipment[] = array('position' => \Items\Equipment::POSITION_WIELD, 'equipped' => null);
			$this->equipment[] = array('position' => \Items\Equipment::POSITION_FLOAT, 'equipped' => null);
			
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
			
			if($item->getPosition() === \Items\Equipment::POSITION_GENERIC)
			{
				if($display_message)
					Server::out($this->actor, "You can't wear that.");
				return false;
			}
			
			$positions = array_filter(
								$this->equipment,
								function($e) use ($item) { return $e['position'] === $item->getPosition(); }
							);
			
			$equipped = $dequipped = null;
			$i = 0;
			foreach($positions as $position)
			{
				$i++;
				$p = $position['position'];
				$e = $position['equipped'];
				if($e === null)
				{
					if($this->actor->getInventory()->remove($item) !== false)
						$this->inventory->add($item);
					foreach($item->getAffects() as $affect)
						$affect->apply($this->target);
					$equipped = $item;
					$equipped_position = $p;
					break;
				}
				if($e !== null && $i == sizeof($positions))
				{
					$item_remove = $e;
					$this->inventory->remove($item_remove);
					$this->inventory->add($item);
					foreach($item_remove->getAffects() as $affect)
						$this->actor->removeAffect($affect);
					foreach($item->getAffects() as $affect)
						$affect->apply($this->target);
					$this->actor->getInventory()->add($item_remove);
					$this->actor->getInventory()->remove($item);
					$equipped = $item;
					$dequipped = $item_remove;
					$equipped_position = $position;
					break;
				}
			}
			
			if($equipped)
			{
				array_walk(
					$this->equipment,
					function(&$e) use ($equipped_position, $equipped)
					{
						if($e['position'] === $equipped_position)
							$e['equipped'] = $equipped;
					}
				);
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
			
			if($equipped->getPosition() === \Items\Equipment::POSITION_WIELD)
			{
				$msg_you .= 'wield ';
				$msg_others .= 'wields ';
			}
			else if($equipped->getPosition() == \Items\Equipment::POSITION_FLOAT)
			{
				$msg_you .= 'releases ';
				$msg_others .= 'releases ';
			}
			else if($equipped->getPosition() == \Items\Equipment::POSITION_HOLD)
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
			
			switch($equipped->getPosition())
			{
				case \Items\Equipment::POSITION_LIGHT:
					$msg_you .= ' as a light.';
					$msg_others .= ' as a light.';
					break;
				case \Items\Equipment::POSITION_FLOAT:
					$msg_you .= ' to float around nearby.';
					$msg_others .= ' to float around nearby.';
					break;
				case \Items\Equipment::POSITION_WIELD:
					$msg_you .= '.';
					$msg_others .= '.';
					break;
				case \Items\Equipment::POSITION_FINGER:
					$msg_you .= ' on your finger.';
					$msg_others .= 'on ' . $sex . ' finger.';
					break;
				case \Items\Equipment::POSITION_ARMS:
					$msg_you .= ' on your arms.';
					$msg_others .= 'on ' . $sex . ' arms.';
					break;
				case \Items\Equipment::POSITION_BODY:
					$msg_you .= ' around your body.';
					$msg_others .= ' around ' . $sex . ' body.';
					break;
				case \Items\Equipment::POSITION_FEET:
					$msg_you .= ' on your feet.';
					$msg_others .= ' on ' . $sex . ' feet.';
					break;
				case \Items\Equipment::POSITION_HEAD:
					$msg_you .= ' on your head.';
					$msg_others .= ' on ' . $sex . ' head.';
					break;
				case \Items\Equipment::POSITION_HANDS:
					$msg_you .= ' on your hands.';
					$msg_others .= ' on ' . $sex . ' hands.';
					break;
				case \Items\Equipment::POSITION_HOLD:
					$msg_you .= ' in your hand.';
					$msg_others .= ' in ' . $sex . ' hand.';
					break;
				case \Items\Equipment::POSITION_TORSO:
					$msg_you .= ' around your torso.';
					$msg_others .= ' around ' . $sex . ' torso.';
					break;
				case \Items\Equipment::POSITION_WAIST:
					$msg_you .= ' around your waist.';
					$msg_others .= ' around ' . $sex . ' waist.';
					break;
				case \Items\Equipment::POSITION_WRIST:
					$msg_you .= ' on your wrist.';
					$msg_others .= ' on ' . $sex . ' wrist.';
					break;
			}
			
			Server::out($this->actor, $msg_you);
			$this->actor->getRoom()->announce($this->actor, $msg_others);
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
			$eq = array_filter(
							$this->equipment,
							function($e) use ($position)
							{
								return $e['position'] === $position;
							}
						);
			if(isset($eq[0]))
				return $eq[0];
			return null;
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
