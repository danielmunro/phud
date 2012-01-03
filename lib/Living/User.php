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
	namespace Living;
	use \Mechanics\Server,
		\Mechanics\Dbr,
		\Mechanics\Client,
		\Mechanics\Fighter,
		\Mechanics\Alias,
		\Mechanics\Room,
		\Mechanics\Quest\Quest,
		\Mechanics\Event\Broadcaster,
		\Mechanics\Quest\Log as QuestLog;

	class User extends Fighter
	{
		protected $hunger = 2;
		protected $thirst = 2;
		protected $full = 4;
		protected $trains = 0;
		protected $practices = 0;
		protected $password = '';
		private $client = null;
		protected $date_created = null;
		protected $is_dm = false;
		protected $quest_log = null;
		protected $persistable_list = 'users';
		protected static $instances = array();
		
		public function __construct()
		{
			$this->date_created = date('Y-m-d H:i:s');
			
			parent::__construct();
			
			$this->quest_log = new QuestLog($this);
		}
		
		public static function getInstances()
		{
			return self::$instances;
		}

		public static function addInstance(self $user)
		{
			self::$instances[] = $user;
		}
		
		public function getClient()
		{
			return $this->client;
		}
		
		public function setClient(Client $client)
		{
			$this->client = $client;
		}
		
		public function getDateCreated()
		{
			return $this->date_created;
		}
		
		public function getQuestLog()
		{
			return $this->quest_log;
		}
		
		public function prompt()
		{
			return 'hp:' . $this->getAttribute('hp') . '/' . $this->getMaxAttribute('hp') . ' mana: ' . $this->getAttribute('mana') . '/' . $this->getMaxAttribute('mana') . ' mv: ' . $this->getAttribute('movement') . '/' . $this->getMaxAttribute('movement') . ' >';
		}
		
		public function setPassword($password)
		{
			$this->password = $password;
		}
		
		public function getPassword()
		{
			return $this->password;
		}
		
		public function isDM()
		{
			return $this->is_dm;
		}
		
		public function setDM($is_dm)
		{
			$this->is_dm = $is_dm;
		}
		
		public function tick($init = false)
		{
			parent::tick();
			if(!$init)
			{
				$this->hunger > 0 ? $this->hunger-- : null;
				$this->thirst > 0 ? $this->thirst-- : null;
				$this->full -= 2;
				if($this->full < 0) {
					$this->full = 0;
				}
				if($this->hunger === 0) {
					Server::out($this, "You are hungry.");
				}
				if($this->thirst === 0) {
					Server::out($this, "You are thirsty.");
				}
				$this->save();
			}
			Server::out($this, "\n" . $this->prompt(), false);
		}
		
		// Food and nourishment
		
		public function getHunger()
		{
			return $this->hunger;
		}
		
		public function getThirst()
		{
			return $this->thirst;
		}
		
		public function increaseHunger($hunger)
		{
			if($this->full + 1 > $this->getRace()['lookup']->getFull()) {
				return Server::out($this, "You are too full.");
			}
			$this->full++;
			$this->hunger += $hunger;
			$max = $this->getRace()['lookup']->getHunger();
			if($this->hunger > $max) {
				$this->hunger = $max;
			}
			return true;
		}
		
		public function increaseThirst($thirst)
		{
			if($this->full + 1 > $this->getRace()['lookup']->getFull() || $this->thirst > $this->getRace()['lookup']->getThirst()) {
				return Server::out($this, "You are too full.");
			}
			if($this->thirst < 0) {
				$this->thirst = 0;
			}
			if($this->full < 0) {
				$this->full = 0;
			}
			$this->full++;
			$this->thirst += $thirst;
			return true;
		}
		
		public function handleDeath()
		{
			parent::handleDeath();
			$this->setHp(1);
			$command = Command::lookup('look');
			$command['lookup']->perform($this);
		}
		
		public function addTrains($trains)
		{
			$this->trains += $trains;
		}
		
		public function decreaseTrains()
		{
			$this->trains--;
		}
		
		public function getTrains()
		{
			return $this->trains;
		}
		
		public static function validateAlias($alias)
		{
			return preg_match('/^[A-Za-z]{2,12}$/i', $alias);
		}

		public function save()
		{
			return parent::save($this->alias);
		}

		public function __sleep()
		{
			return [
				'id',
				'hunger',
				'thirst',
				'full',
				'trains',
				'practices',
				'password',
				'date_created',
				'is_dm',
				'quest_log',
				'experience',
				'experience_per_level',
				'alias',
				'long',
				'level',
				'gold',
				'silver',
				'copper',
				'sex',
				'disposition',
				'race',
				'room',
				'equipped',
				'alignment',
				'attributes',
				'max_attributes',
				'abilities',
				'delay',
				'proficiencies'
			];
		}
	}
?>
