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
	use \Mechanics\Ability\Ability,
		\Mechanics\Event\Subscriber,
		\Mechanics\Event\Broadcaster,
		\Mechanics\Event\Event,
		\Living\User;

	abstract class Actor
	{
		use Affectable, Persistable, Broadcaster;
	
		const MAX_LEVEL = 51;
		
		const DISPOSITION_STANDING = 'standing';
		const DISPOSITION_SITTING = 'sitting';
		const DISPOSITION_SLEEPING = 'sleeping';
		
		const SEX_NEUTRAL = 1;
		const SEX_FEMALE = 2;
		const SEX_MALE = 3;
		
		protected $alias = '';
		protected $long = '';
		protected $level = 0;
		protected $gold = 0;
		protected $silver = 0;
		protected $copper = 0;
		protected $sex = self::SEX_NEUTRAL;
		protected $disposition = self::DISPOSITION_STANDING;
		protected $race = 'critter';
		protected $room_id = -1;
		protected $inventory = null;
		protected $equipped = null;
		protected $alignment = 0;
		protected $_subscribers_race = [];
		
		public function __construct()
		{
			$this->inventory = new Inventory();
			$this->equipped = new Equipped($this);
			$this->initActor();
		}

		public function initActor()
		{
			$race = $this->getRace();
			if($race) {
				$subscribers = $race['lookup']->getSubscribers();
				foreach($subscribers as $subscriber) {
					$this->addSubscriber($subscriber);
				}
			}
			foreach($this->affects as $affect) {
				$affect->applyTimeoutSubscriber($this);
			}
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
		
		public function getAlias($upper = null)
		{
			if($upper === null)
				return $this instanceof User ? ucfirst($this->alias) : $this->alias;
			else
				return $upper ? ucfirst($this->alias) : $this->alias;
		}
		
		public function getLong()
		{
			return $this->long;
		}
		
		public function setLong($long)
		{
			$this->long = $long;
		}
		
		public function getInventory()
		{
			return $this->inventory;
		}
		
		public function getEquipped()
		{
			return $this->equipped;
		}
		
		///////////////////////////////////////////////////////////////////////////
		// Money stuff
		///////////////////////////////////////////////////////////////////////////
		
		public function getCopper()
		{
			return $this->copper;
		}
		
		public function getSilver()
		{
			return $this->silver;
		}
		
		public function getGold()
		{
			return $this->gold;
		}

		public function getWorth()
		{
			return $this->copper + ($this->silver * 100) + ($this->gold * 1000);
		}

		public function addCopper($amount)
		{
			$this->copper += abs($amount);
		}

		public function addSilver($amount)
		{
			$this->silver += abs($amount);
		}
		
		public function addGold($amount)
		{
			$this->gold += abs($amount);
		}

		public function decreaseFunds($copper)
		{
			$copper = abs($copper);
			if($this->getWorth() < $copper) {
				return false; // Not enough money
			}

			$this->copper -= $copper; // Remove the money
			
			// ensure that copper amount stays above zero
			while($this->copper < 0) {
				if($this->silver > 0) {
					$this->silver--;
					$this->copper += 100;
					continue;
				}
				if($this->gold > 0) {
					$this->gold--;
					$this->copper += 1000;
				}
			}
		}
		// End money
		
		public function getSex()
		{
			return $this->sex;
		}
		
		public function getDisplaySex($set = [])
		{
			if(empty($set)) {
				$set = [self::SEX_MALE=>'his', self::SEX_FEMALE=>'her', self::SEX_NEUTRAL=>'its'];
			}
			if(isset($set[$this->sex])) {
				return $set[$this->sex];
			}
			return 'its';
		}
		
		public function setSex($sex)
		{
			if($sex === self::SEX_FEMALE || $sex === self::SEX_MALE || $sex === self::SEX_NEUTRAL) {
				$this->sex = $sex;
				return true;
			}
			return false;
		}
		
		public function setRoom(Room $room)
		{
			if($this->room_id > -1)
				$this->getRoom()->actorRemove($this);
			$room->actorAdd($this);
			$this->room_id = $room->getId();
		}
		
		public function getRoom()
		{
			return Room::find($this->room_id);
		}
		
		public function getRace()
		{
			return Race::lookup($this->race);
		}
		
		public function setRace($race)
		{
			if($this->race) {
				foreach($this->_subscribers_race as $subscriber) {
					$this->removeSubscriber($subscriber);
				}
			}
			$this->race = $race['alias'];
			$this->_subscribers_race = $race['lookup']->getSubscribers();
			foreach($this->_subscribers_race as $subscriber) {
				$this->addSubscriber($subscriber);
			}
		}
		
		///////////////////////////////////////////////////////////////////////////
		// Leveling
		///////////////////////////////////////////////////////////////////////////
		
		public function setLevel($level)
		{
			while($this->level < $level)
			{
				$this->experience += $this->getExperiencePerLevel() - ($this->experience - ($this->level*$this->getExperiencePerLevel()));
				$this->levelUp(false);
			}
		}
		
		abstract protected function levelUp();
		
		public function getLevel()
		{
			return $this->level;
		}
		
		public function setAlias($alias)
		{
			$this->alias = $alias;
		}
		
		public function isSafe()
		{
			// Checks for safe rooms, imms, non-mobs, etc
			return false;
		}
		
		public function lookDescribe()
		{
			$sexes = [Actor::SEX_MALE=>'him',Actor::SEX_FEMALE=>'her',Actor::SEX_NEUTRAL=>'it'];

			if(!$this->long)
				return 'You see nothing special about '.$this->getDisplaySex($sexes).'.';
			
			return  $this->long . "\r\n" . 
					ucfirst($this).'.';
		}
		
		public function __toString()
		{
			return $this->getAlias();
		}
	}
?>
