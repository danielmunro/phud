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
		use Affectable, Persistable, Broadcaster, Inventory, Usable;
	
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
		protected $race = '';
		protected $room = null;
		protected $equipped = null;
		protected $alignment = 0;
		protected $attributes = null;
		protected $max_attributes = null;
		protected $abilities = [];
		protected $delay = 0;
		protected $target = null;
		protected $_subscriber_delay = null;
		protected $_subscribers_race = [];
		protected $_subscriber_tick = null;
		protected $proficiencies = [
			'stealth' => 15,
			'healing' => 15,
			'one handed weapons' => 15,
			'two handed weapons' => 15,
			'leather armor' => 15,
			'chain armor' => 15,
			'plate armor' => 15,
			'melee' => 15,
			'evasive' => 15,
			'archery' => 15,
			'alchemy' => 15,
			'elemental' => 15,
			'illusion' => 15,
			'transportation' => 15,
			'sorcery' => 15,
			'maladictions' => 15,
			'benedictions' => 15,
			'curative' => 15,
			'beguiling' => 15,
			'speech' => 15
		];
		
		public function __construct()
		{
			$this->attributes = new Attributes([
				'str' => 15,
				'int' => 15,
				'wis' => 15,
				'dex' => 15,
				'con' => 15,
				'cha' => 15,
				'hp' => 20,
				'mana' => 100,
				'movement' => 100,
				'ac_bash' => 100,
				'ac_slash' => 100,
				'ac_pierce' => 100,
				'ac_magic' => 100,
				'hit' => 1,
				'dam' => 1,
				'saves' => 100
			]);
			$this->max_attributes = new Attributes([
				'str' => 19,
				'int' => 19,
				'wis' => 19,
				'dex' => 19,
				'con' => 19,
				'cha' => 19,
				'hp' => 20,
				'mana' => 100,
				'movement' => 100
			]);
			$this->equipped = new Equipped($this);
			$this->setRace(Race::lookup('critter'));
		}

		///////////////////////////////////////////////////////////////////
		// Ability functions
		///////////////////////////////////////////////////////////////////

		public function getAbilities()
		{
			return $this->abilities;
		}

		public function addAbility($ability)
		{
			// Remember what abilities the fighter has
			$this->abilities[] = $ability['alias'];
			if($ability['lookup'] instanceof Skill) {
				// Apply the subscriber to trigger the ability at the right time
				$this->addSubscriber($ability['lookup']->getSubscriber());
			}
		}

		public function removeAbility($ability)
		{
			$alias = $ability['alias'];
			if(isset($this->ability[$alias])) {
				unset($this->ability[$alias]);
				if($ability['lookup'] instanceof Skill) {
					$this->removeSubscriber($ability['lookup']->getSubscriber());
				}
			}
		}

		///////////////////////////////////////////////////////////////////
		// Delay functions
		///////////////////////////////////////////////////////////////////
		
		public function incrementDelay($delay) {
			$this->delay += $delay;
			if(empty($this->_subscriber_delay)) {
				$this->_subscriber_delay = new Subscriber(
					Event::EVENT_PULSE,
					$this,
					function($subscriber, $server, $fighter) {
						if(!$fighter->decrementDelay()) {
							$subscriber->kill();
						}
					}
				);
				Server::instance()->addSubscriber($this->_subscriber_delay);
			}

		}

		public function decrementDelay()
		{
			if($this->delay > 0) {
				$this->delay--;
				return true;
			} 
			unset($this->_subscriber_delay);
			return false;
		}

		public function getDelay()
		{
			return $this->delay;
		}

		///////////////////////////////////////////////////////////////////
		// Attributes functions
		///////////////////////////////////////////////////////////////////
		
		public function getMaxAttribute($key)
		{
			return $this->max_attributes->getAttribute($key);
		}

		public function getUnmodifiedAttribute($key)
		{
			return $this->attributes->getAttribute($key);
		}

		public function getAttribute($key)
		{
			$n = $this->attributes->getAttribute($key);
			foreach($this->affects as $affect) {
				$n += $affect->getAttribute($key);
			}
			foreach($this->equipped->getItems() as $eq) {
				$n += $eq->getAttribute($key);
				$affs = $eq->getAffects();
				foreach($affs as $aff) {
					$n += $aff->getAttribute($key);
				}
			}
			$n += $this->race['lookup']->getAttributes()->getAttribute($key);
			$max = $this->max_attributes->getAttribute($key);
			$n = round($n);
			return $max > 0 ? min($n, $this->max_attributes->getAttribute($key)) : $n;
		}

		public function modifyAttribute($key, $amount)
		{
			$this->attributes->modifyAttribute($key, $amount);
			$this->normalizeAttribute($key);
		}

		public function setAttribute($key, $amount)
		{
			$this->attributes->setAttribute($key, $amount);
			$this->normalizeAttribute($key);
		}

		protected function normalizeAttribute($key)
		{
			$max = $this->max_attributes->getAttribute($key);
			if($max > 0 && $this->attributes->getAttribute($key) > $max) {
				$this->attributes->setAttribute($key, $max);
			}
		}

		///////////////////////////////////////////////////////////////////
		// Tick functions
		///////////////////////////////////////////////////////////////////

		public function getSubscriberTick()
		{
			if(!$this->_subscriber_tick) {
				$this->_subscriber_tick = new Subscriber(
					Event::EVENT_TICK,
					$this,
					function($subscriber, $broadcaster, $actor) {
						$actor->tick();
					}
				);
			}
			return $this->_subscriber_tick;
		}

		public function tick()
		{
			if($this->isAlive()) {
				$max = $this->getMaxAttribute('hp');
				$amount = round(rand($max * 0.05, $max * 0.1));
				$this->modifyAttribute('hp', $amount);

				$max = $this->getMaxAttribute('mana');
				$amount = round(rand($max * 0.05, $max * 0.1));
				$this->modifyAttribute('mana', $amount);

				$max = $this->getMaxAttribute('movement');
				$amount = round(rand($max * 0.05, $max * 0.1));
				$this->modifyAttribute('movement', $amount);
			}
		}

		///////////////////////////////////////////////////////////////////////////
		// Money functions
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

		public function getTarget()
		{
			return $this->target;
		}
		
		public function setTarget(Actor $target = null)
		{
			$this->target = $target;
		}

		public function getProficiencyIn($proficiency)
		{
			if(!isset($this->proficiencies[$proficiency])) {
				Debug::addDebugLine("Error, proficiency not defined: ".$proficiency);
				$this->proficiencies[$proficiency] = 15;
			}
			return $this->proficiencies[$proficiency];
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
		
		public function getEquipped()
		{
			return $this->equipped;
		}
		
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
			if($this->room) {
				$this->room->actorRemove($this);
			}
			$room->actorAdd($this);
			$this->room = $room;
		}
		
		public function getRoom()
		{
			return $this->room;
		}
		
		public function getRace()
		{
			return $this->race;
		}
		
		public function setRace($race)
		{
			if($this->race) {
				// Undo all previous racial subscribers/abilities/stats/proficiencies
				foreach($this->_subscribers_race as $subscriber) {
					$this->removeSubscriber($subscriber);
				}
				foreach($this->race['lookup']->getProficiencies() as $proficiency => $amount) {
					$this->proficiencies[$proficiency] -= $amount;
				}
				foreach($race['lookup']->getAbilities() as $ability_alias) {
					$ability = Ability::lookup($ability_alias);
					$this->removeAbility($ability);
				}
			}

			// Assign all racial subscribers/abilities/stats/proficiencies
			$this->race = $race;
			$this->_subscribers_race = $race['lookup']->getSubscribers();
			foreach($this->_subscribers_race as $subscriber) {
				$this->addSubscriber($subscriber);
			}
			$profs = $race['lookup']->getProficiencies();
			foreach($profs as $name => $value) {
				$this->proficiencies[$name] += $value;
			}
			foreach($race['lookup']->getAbilities() as $ability_alias) {
				$ability = Ability::lookup($ability_alias);
				$this->addAbility($ability);
			}
		}
		
		public function setLevel($level)
		{
			while($this->level < $level)
			{
				$this->experience += $this->getExperiencePerLevel() - ($this->experience - ($this->level*$this->getExperiencePerLevel()));
				$this->levelUp(false);
			}
		}
		
		public function levelUp()
		{
			Debug::addDebugLine($this.' levels up.');
			$this->level++;
			$this->trains++;
			$this->practices += ceil($this->getWis() / 5);
			
			if($display) {
				Server::out($this, 'You LEVELED UP!');
				Server::out($this, 'Congratulations, you are now level ' . $this->level . '!');
			}
		}
		
		public function getLevel()
		{
			return $this->level;
		}
		
		public function setAlias($alias)
		{
			$this->alias = $alias;
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
		
		public function __wakeup()
		{
			$this->room = Room::find($this->room->getID());
			$this->race = Race::lookup($this->race['alias']);
			$this->_subscribers_race = $this->race['lookup']->getSubscribers();
			foreach($this->_subscribers_race as $subscriber) {
				$this->addSubscriber($subscriber);
			}
			foreach($this->affects as $affect) {
				$affect->applyTimeoutSubscriber($this);
			}
			foreach($this->abilities as $user_ab) {
				$ability = Ability::lookup($user_ab);
				if($ability['lookup'] instanceof Skill) {
					$this->addSubscriber($ability['lookup']->getSubscriber());
				}
			}
			Server::instance()->addSubscriber($this->getSubscriberTick());
		}
	}
?>
