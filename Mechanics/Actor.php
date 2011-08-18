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
		const DISPOSITION_STANDING = 0;
		const DISPOSITION_SLEEPING = 1;
		const DISPOSITION_SITTING = 2;
		const DISPOSITION_FIGHTING = 3;
		
		protected $id = null;
		protected $alias = '';
		protected $long = '';
		protected $level = 1;
		protected $gold = 0;
		protected $silver = 0;
		protected $copper = 0;
		protected $sex = '';
		protected $disposition = 0; // sitting, sleeping, standing
		protected $race = '';
		protected $room_id = 0;
		protected $inventory = null;
		protected $equipped = null;
		protected $ability_set = null;
		protected $affects = array();
		protected $alignment = 0;
		
		public function __construct()
		{
			$this->inventory = new Inventory();
			$this->equipped = new Equipped($this);
			$this->ability_set = new Ability_Set($this);
			$this->tick(true);
		}
		
		public function getAlignment()
		{
			return $this->alignment;
		}
		
		public function setAlignment($alignment)
		{
			$this->alignment = $alignment;
		}
		
		public function getDisposition()
		{
			return $this->disposition;
		}
		
		public function setDisposition($disposition)
		{
			if($disposition !== self::DISPOSITION_SITTING && $disposition !== self::DISPOSITION_SLEEPING && $disposition !== self::DISPOSITION_STANDING)
				throw new \Exceptions\Actor("Invalid disposition.", \Exceptions\Actor::INVALID_ATTRIBUTE);
			$this->disposition = $disposition;
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
		
		public function getId()
		{
			return $this->id;
		}
		
		public function tick()
		{
			foreach($this->affects as $i => $affect)
			{
				if(!$affect->getTimeout())
				{
					if($affect->getMessageEnd())
						Server::out($this, $affect->getMessageEnd());
					unset($this->affects[$i]);
					continue;
				}
				$affect->decreaseTime();
			}
			
			$abilities = $this->ability_set->getAbilitiesByHook(Ability::HOOK_TICK);
			foreach($abilities as $ability)
				$ability->perform();
			
			Pulse::instance()->registerTickEvent(function($user) { $user->tick(); }, $this);
		}
		
		public function getAbilitySet()
		{
			return $this->ability_set;
		}
		
		public function perform(Ability $ability, $args = array())
		{
			$learned_ability = $this->ability_set->getLearnedAbility($ability);
			$this->incrementDelay($ability->getDelay());
			if($learned_ability)
				return $learned_ability->perform($args);
			return $ability->getFailMessage();
		}
		
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
		public function getClassStr() { return $this->_class->getClassStr(); }
		public function getLevel() { return $this->level; }
		public function getLong() { return $this->long; }
		public function getInventory() { return $this->inventory; }
		public function getEquipped() { return $this->equipped; }
		public function getRoom()
		{
			return Room::find($this->room_id);
		}
		public function getDescription() { return $this->long; }
		public function getCopper() { return $this->copper; }
		public function getSilver() { return $this->silver; }
		public function addSilver($silver) { $this->silver += $silver; }
		public function getGold() { return $this->gold; }
		public function getSex()
		{
			return $this->sex;
		}
		public function setSex($sex)
		{
			if($sex == 'm' || $sex == 'f')
				$this->sex = $sex;
		}
		public function setRoom(\Mechanics\Room $room)
		{
			if($this->room_id)
				Room::find($this->room_id)->actorRemove($this);
			$room->actorAdd($this);
			$this->room_id = $room->getId();
		}
		public function getRace()
		{
			return Alias::lookup($this->race);
		}
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
			$this->race = $race;
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
		
		public function purchaseFrom(\Items\Item $item, Actor $seller)
		{
			$value = $item->getValue();
			$abilities = $this->getAbilitySet()->getAbilitiesByHook(Ability::HOOK_BUY_ITEM);
			foreach($abilities as $learned_ability)
				$value += $learned_ability->perform($value);
		
			if($this->decreaseFunds($value) === false)
				return false;
			
			$item->copyTo($actor);
			return true;
		}
		
		abstract protected function levelUp();
		
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
