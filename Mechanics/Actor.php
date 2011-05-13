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
	abstract class Actor implements Affectable
	{
	
		const MAX_LEVEL = 51;
		
		protected $id = 0;
		protected $alias = '';
		protected $password = '';
		protected $long = '';
		protected $level = 1;
		protected $gold = 0;
		protected $silver = 0;
		protected $copper = 0;
		protected $sex = null;
		protected $disposition; // sitting, sleeping, standing
		protected $race = null;
		protected $room = null;
		protected $inventory = null;
		protected $equipped = null;
		protected $ability_set = null;
		protected $unique_id = 0;
		protected $affects = array();
		
		public function __construct($room_id)
		{
		
			$this->unique_id = sha1($this->alias.microtime().get_class($this).rand(0, 10000000));
			$this->setRoom(Room::find($room_id));
			$this->loadInventory();
			$this->ability_set = Ability_Set::findByActor($this);
			$this->tick(true);
		}
		
		
		/**
		 * AFFECTS - array of affects (object type Affect) currently applied to the actor
		 */
		public function addAffect(Affect $affect)
		{
			$i = $affect->getAffect();
			$this->affects[$i] = $affect;
		}
		public function removeAffect(Affect $affect)
		{
			$i = array_search($affect, $this->affects);
			if($i !== false)
				unset($this->affects[$i]);
		}
		public function getAffects() { return $this->affects; }
		
		public function getId() { return $this->id; }
		public function setId($id)
		{
			if(!is_numeric($id))
				throw new \Exceptions\Actor("Cannot assign non-numeric ID to actor (".$id.")", \Exceptions\Actor::NON_NUMERIC_ID);
			$this->id = $id;
		}
		
		protected function loadInventory()
		{
			$this->inventory = Inventory::find($this->getTable(), $this->id);
			$this->equipped = new Equipped($this);
		}
		
		public function tick()
		{
			foreach($this->affects as $i => $affect)
			{
				if(!$affect->getTimeout())
				{
					if($affect->getMessageEnd())
						Server::out($this, $affect->getMessageEnd());
					$affect->removeFromDb($this->getTable(), $this->getId());
					unset($this->affects[$i]);
					continue;
				}
				$affect->decreaseTime();
				$affect->save($this->getTable(), $this->getId());
			}
			Pulse::instance()->registerTickEvent(function($user) { $user->tick(); }, $this);
		}
		
		public function getAbilitySet() { return $this->ability_set; }
		public function getType()
		{
			return array_pop(explode("\\", get_class($this)));
		}
		public function getAlias($upper = null)
		{
			if($upper === null)
				return $this instanceof \Living\User ? ucfirst($this->alias) : $this->alias;
			else
				return $upper ? ucfirst($this->alias) : $this->alias;
		}
		public function getRaceStr()
		{
			$class = get_class($this->race);
			return substr($class, strpos($class, '\\') + 1);
		}
		public function getUniqueId() { return $this->unique_id; }
		public function getClassStr() { return $this->_class->getClassStr(); }
		public function getDiscipline() { return $this->discipline; }
		public function getLevel() { return $this->level; }
		public function getLong() { return $this->long; }
		public function getInventory() { return $this->inventory; }
		public function getEquipped() { return $this->equipped; }
		public function getRoomId() { return $this->room->getId(); }
		public function getRoom() { return $this->room; }
		public function getDescription() { return $this->long; }
		public function getCopper() { return $this->copper; }
		public function getSilver() { return $this->silver; }
		public function addSilver($silver) { $this->silver += $silver; }
		public function getGold() { return $this->gold; }
		public function getSex() { return $this->sex; }
		public function setRoom(\Mechanics\Room &$room)
		{
			if($this->room)
				$this->room->actorRemove($this);
			$room->actorAdd($this);
			$this->room = $room;
		}
		public function getRace() { return $this->race; }
		public function increaseCopper($amount) { $this->copper += $amount; }
		public function decreaseCopper($amount) { $this->copper -= $amount; }
		public function increaseSilver($amount) { $this->silver += $amount; }
		public function decreaseSilver($amount) { $this->silver -= $amount; }
		public function increaseGold($amount) { $this->gold += $amount; }
		public function decreaseGold($amount) { $this->gold -= $amount; }
		public function setLevel($level)
		{
			while($this->level < $level)
				$this->levelUp(false);
		}
		public function setAlias($alias) { $this->alias = $alias; }
		public function setRace($race)
		{
			$race = Race::getInstance($race);
			if($race instanceof Race && $race->getPlayable())
				$this->race = $race;
			else
				throw new \Exceptions\Actor("Trying to instantiate race with bad value.", \Exceptions\Actor::INVALID_RACE);
		}
		public function decreaseFunds($value)
		{
			$copper = $this->copper;
			$silver = $this->silver;
			$gold = $this->gold;
			
			if($copper > $value)
				return $this->copper -= $value;
			else
			{
				$value -= $copper;
				$copper = 0;
			}
			$value = $value / 100;
			if($silver > $value)
			{
				$silver -= $value;
				$value = 0;
			}			
			else
			{
				$value -= $silver;
				$silver = 0;
			}
			$value = $value / 100;
			if($gold > $value)
			{
				$gold -= $value;
				$value = 0;
			}
			else
			{
				$value -= $gold;
				$gold = 0;
			}
			
			if($value > 0)
				return false;
			
			$this->copper = $copper;
			$this->silver = $silver;
			$this->gold = $gold;
			
			return true;
		}
		
		public function isSafe()
		{
			// Checks for safe rooms, imms, non-mobs, etc
			return false;
		}
		
		public function getDisplaySex()
		{
			if($this->getSex() == 'm')
				return 'his';
			else if($this->getSex() == 'f')
				return 'her';
			else
				return 'its';
		}
		
		public function lookDescribe()
		{
		
			if($this->sex === 'm')
				$sex = 'him';
			else if($this->sex === 'f')
				$sex = 'her';
			
			if(!isset($sex))
				$sex = 'it';
			
			if(!$this->long)
				$this->long = 'You see nothing special about ' . $sex . '.';
			
			return  $this->long . "\r\n" . 
					$this->getAlias(true).'.';
		
		}
		
		abstract protected function levelUp();
		
		abstract public function getTable();
		public function getNoun()
		{
			return $this->alias;
		}
		public function __toString()
		{
			$class = get_class($this);
			return substr($class, strpos($class, '\\') + 1);
		}
	}
?>
