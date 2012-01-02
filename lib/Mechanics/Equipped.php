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
		use Inventory, Usable;

		private $equipment = array();
		private $actor = null;
		private static $labels = array
		(
			\Mechanics\Equipment::POSITION_LIGHT =>		'<used as light>      ',
			\Mechanics\Equipment::POSITION_FINGER =>	'<worn on finger>     ',
			\Mechanics\Equipment::POSITION_NECK =>		'<worn around neck>   ',
			\Mechanics\Equipment::POSITION_HEAD => 		'<worn on head>       ',
			\Mechanics\Equipment::POSITION_LEGS => 		'<worn on legs>       ',
			\Mechanics\Equipment::POSITION_FEET => 		'<worn on feet>       ',
			\Mechanics\Equipment::POSITION_HANDS => 	'<worn on hands>      ',
			\Mechanics\Equipment::POSITION_ARMS => 		'<worn on arms>       ',
			\Mechanics\Equipment::POSITION_TORSO => 	'<worn on torso>      ',
			\Mechanics\Equipment::POSITION_BODY => 		'<worn about body>    ',
			\Mechanics\Equipment::POSITION_WAIST => 	'<worn about waist>   ',
			\Mechanics\Equipment::POSITION_WRIST =>		'<worn around wrist>  ',
			\Mechanics\Equipment::POSITION_WIELD => 	'<wielded in hand>    ',
			\Mechanics\Equipment::POSITION_FLOAT => 	'<floating nearby>    '
		);
		
		public function __construct(Actor $actor)
		{
			
			$this->equipment[] = array('position' => \Mechanics\Equipment::POSITION_LIGHT, 'equipped' => null);
			$this->equipment[] = array('position' => \Mechanics\Equipment::POSITION_FINGER, 'equipped' => null);
			$this->equipment[] = array('position' => \Mechanics\Equipment::POSITION_FINGER, 'equipped' => null);
			$this->equipment[] = array('position' => \Mechanics\Equipment::POSITION_NECK, 'equipped' => null);
			$this->equipment[] = array('position' => \Mechanics\Equipment::POSITION_NECK, 'equipped' => null);
			$this->equipment[] = array('position' => \Mechanics\Equipment::POSITION_BODY, 'equipped' => null);
			$this->equipment[] = array('position' => \Mechanics\Equipment::POSITION_HEAD, 'equipped' => null);
			$this->equipment[] = array('position' => \Mechanics\Equipment::POSITION_LEGS, 'equipped' => null);
			$this->equipment[] = array('position' => \Mechanics\Equipment::POSITION_FEET, 'equipped' => null);
			$this->equipment[] = array('position' => \Mechanics\Equipment::POSITION_HANDS, 'equipped' => null);
			$this->equipment[] = array('position' => \Mechanics\Equipment::POSITION_ARMS, 'equipped' => null);
			$this->equipment[] = array('position' => \Mechanics\Equipment::POSITION_TORSO, 'equipped' => null);
			$this->equipment[] = array('position' => \Mechanics\Equipment::POSITION_WAIST, 'equipped' => null);
			$this->equipment[] = array('position' => \Mechanics\Equipment::POSITION_WRIST, 'equipped' => null);
			$this->equipment[] = array('position' => \Mechanics\Equipment::POSITION_WRIST, 'equipped' => null);
			$this->equipment[] = array('position' => \Mechanics\Equipment::POSITION_WIELD, 'equipped' => null);
			$this->equipment[] = array('position' => \Mechanics\Equipment::POSITION_WIELD, 'equipped' => null);
			$this->equipment[] = array('position' => \Mechanics\Equipment::POSITION_FLOAT, 'equipped' => null);
			
			if($actor)
			{
				$this->actor = $actor;
			}
		}
		
		public static function getLabelByPosition($position)
		{
			return self::$labels[$position];
		}
		
		public function equip(\Mechanics\Equipment $item, $display_message = true)
		{
			
			if($item->getPosition() === \Mechanics\Equipment::POSITION_GENERIC)
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
					if($this->actor->removeItem($item) !== false) {
						$this->addItem($item);
					}
					$equipped = $item;
					$equipped_position = $p;
					break;
				}
				if($e !== null && $i == sizeof($positions))
				{
					$item_remove = $e;
					$this->removeItem($item_remove);
					$this->addItem($item);
					$this->actor->addItem($item_remove);
					$this->actor->removeItem($item);
					$equipped = $item;
					$dequipped = $item_remove;
					$equipped_position = $position;
					break;
				}
			}
			
			if($equipped)
			{
				foreach($this->equipment as &$e)
				{
					if($e['position'] === $equipped_position)
					{
						$e['equipped'] = $equipped;
						break;
					}
				}
			}
			
			if(!$display_message)
				return;
			
			if($dequipped)
			{
				$msg_you = "You remove ".$dequipped." and "; // . $equipped->getShort() . ' ' . $this->equipPositionLabel($actor, $equipped_position, true) . '.';
				$msg_others = ucfirst($this->actor)." removes ".$dequipped." and "; //wears " . $equipped->getShort() . ' ' . $this->equipPositionLabel($actor, $equipped_position) . '.';
			}
			else
			{
				$msg_you = "You ";
				$msg_others = ucfirst($this->actor)." ";
			}
			
			if($equipped->getPosition() === \Mechanics\Equipment::POSITION_WIELD)
			{
				$msg_you .= 'wield ';
				$msg_others .= 'wields ';
			}
			else if($equipped->getPosition() == \Mechanics\Equipment::POSITION_FLOAT)
			{
				$msg_you .= 'releases ';
				$msg_others .= 'releases ';
			}
			else if($equipped->getPosition() == \Mechanics\Equipment::POSITION_HOLD)
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
			
			$sex = $this->actor->getDisplaySex();
			
			switch($equipped->getPosition())
			{
				case \Mechanics\Equipment::POSITION_LIGHT:
					$msg_you .= ' as a light.';
					$msg_others .= ' as a light.';
					break;
				case \Mechanics\Equipment::POSITION_FLOAT:
					$msg_you .= ' to float around nearby.';
					$msg_others .= ' to float around nearby.';
					break;
				case \Mechanics\Equipment::POSITION_WIELD:
					$msg_you .= '.';
					$msg_others .= '.';
					break;
				case \Mechanics\Equipment::POSITION_FINGER:
					$msg_you .= ' on your finger.';
					$msg_others .= 'on ' . $sex . ' finger.';
					break;
				case \Mechanics\Equipment::POSITION_ARMS:
					$msg_you .= ' on your arms.';
					$msg_others .= 'on ' . $sex . ' arms.';
					break;
				case \Mechanics\Equipment::POSITION_BODY:
					$msg_you .= ' around your body.';
					$msg_others .= ' around ' . $sex . ' body.';
					break;
				case \Mechanics\Equipment::POSITION_FEET:
					$msg_you .= ' on your feet.';
					$msg_others .= ' on ' . $sex . ' feet.';
					break;
				case \Mechanics\Equipment::POSITION_HEAD:
					$msg_you .= ' on your head.';
					$msg_others .= ' on ' . $sex . ' head.';
					break;
				case \Mechanics\Equipment::POSITION_HANDS:
					$msg_you .= ' on your hands.';
					$msg_others .= ' on ' . $sex . ' hands.';
					break;
				case \Mechanics\Equipment::POSITION_HOLD:
					$msg_you .= ' in your hand.';
					$msg_others .= ' in ' . $sex . ' hand.';
					break;
				case \Mechanics\Equipment::POSITION_TORSO:
					$msg_you .= ' around your torso.';
					$msg_others .= ' around ' . $sex . ' torso.';
					break;
				case \Mechanics\Equipment::POSITION_WAIST:
					$msg_you .= ' around your waist.';
					$msg_others .= ' around ' . $sex . ' waist.';
					break;
				case \Mechanics\Equipment::POSITION_WRIST:
					$msg_you .= ' on your wrist.';
					$msg_others .= ' on ' . $sex . ' wrist.';
					break;
			}
			
			Server::out($this->actor, $msg_you);
			$this->actor->getRoom()->announce($this->actor, $msg_others);
		}
		
		public function removeByPosition($position)
		{
			
			if($this->equipment[$position] instanceof \Mechanics\Equipment)
			{
				$this->removeItem($item);
				$this->actor->removeAffects($item->getAffects());
				$item = $this->equipment[$position];
				$this->actor->addItem($item);
				$this->equipment[$position] = null;
			}
			else
				Server::out($this->actor, 'Nothing is there.');
			
		}
		
		public function remove(\Mechanics\Equipment $item)
		{
			foreach($this->equipment as &$e)
			{
				if($e['equipped'] === $item)
				{
					$this->removeItem($item);
					$this->addItem($item);
					$e['equipped'] = null;
				}
			}
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
			if(sizeof($eq))
				return array_shift($eq);
			return null;
		}
		
		public function getEquipment()
		{
			return $this->equipment;
		}
		
		public function displayContents()
		{
		
			$buffer = '';
			$viewed = false;
			foreach($this->equipment as $eq)
			{
				$key = $eq['position'];
				$buf = self::$labels[$key];
				$len_diff = 22 - strlen($buf);
				for($i = 0; $i < $len_diff; $i++)
					$buf .= ' ';
				$buffer .= $buf;
				if($eq['equipped'] instanceof \Mechanics\Equipment)
					$buffer .= '      ' . $eq['equipped']->getShort() . "\n";
				else
					$buffer .= "      nothing\n";
			}

			return $buffer;
		}
	}
?>
