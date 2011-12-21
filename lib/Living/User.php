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
		\Mechanics\Quest\Quest,
		\Mechanics\Persistable,
		\Mechanics\Event\Broadcaster,
		\Mechanics\Quest\Log as QuestLog;

	class User extends Fighter
	{
		use Broadcaster;

		protected $nourishment = 5;
		protected $thirst = 5;
		protected $trains = 0;
		protected $practices = 0;
		protected $password = '';
		private $client = null;
		protected $discipline_primary = null;
		protected $discipline_focus = null;
		protected $date_created = null;
		protected $is_dm = false;
		protected $quest_log = null;
		protected static $instances = array();
		
		public function __construct()
		{
			$this->date_created = date('Y-m-d H:i:s');
			
			parent::__construct();
			
			$this->attributes->setHp(20);
			$this->attributes->setMana(100);
			$this->attributes->setMovement(100);
			
			$this->max_attributes->setHp(20);
			$this->max_attributes->setMana(100);
			$this->max_attributes->setMovement(100);
			
			$this->quest_log = new QuestLog($this);
			//$this->quest_log->add(Quest::findByHook(Quest::HOOK_CREATE, $this));
			
			self::$instances[] = $this;
		}
		
		public static function getInstances()
		{
			return self::$instances;
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
			return 'hp:' . $this->getHp() . '/' . $this->getMaxHp() . ' mana: ' . $this->getMana() . '/' . $this->getMaxMana() . ' mv: ' . $this->getMovement() . '/' . $this->getMaxMovement() . ' >';
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
				$this->decreaseRacialNourishmentAndThirst();
				if($this->nourishment < 0)
					Server::out($this, "You are hungry.");
				if($this->thirst < 0)
					Server::out($this, "You are thirsty.");
				$this->save();
			}
			Server::out($this, "\n" . $this->prompt(), false);
		}
		
		// Food and nourishment
		
		public function decreaseRacialNourishmentAndThirst()
		{
			$this->nourishment -= $this->getRace()->getDecreaseNourishment();
			$this->thirst -= $this->getRace()->getDecreaseThirst();
		}
		
		public function getNourishment()
		{
			return $this->nourishment;
		}
		
		public function getThirst()
		{
			return $this->thirst;
		}
		
		public function increaseNourishment($nourishment)
		{
			if($this->nourishment < 0)
				$this->nourishment = $nourishment;
			else
				$this->nourishment += $nourishment;
			
			if($this->nourishment > $this->getRace()->getNourishment())
				$this->nourishment = $this->getRace()->getNourishment();
		}
		
		public function increaseThirst($thirst)
		{
			if($this->thirst < 0)
				$this->thirst = $thirst;
			else
				$this->thirst += $thirst;
			
			if($this->thirst > $this->getRace()->getThirst())
				$this->thirst = $this->getRace()->getThirst();
		}
		
		public function isNourishmentFull()
		{
			return $this->nourishment == $this->getRace()->getNourishment();
		}
		
		public function isThirstFull()
		{
			return $this->nourishment == $this->getRace()->getThirst();
		}
		
		public function handleDeath()
		{
			parent::handleDeath();
			\Commands\Look::perform($this);
		}
		
		public function getDisciplinePrimary()
		{
			return Alias::lookup($this->discipline_primary);
		}
		
		public function setDisciplinePrimary(\Mechanics\DisciplinePrimary $discipline)
		{
			$this->discipline_primary = $discipline->getAlias()->getAliasName();
		}
		
		public function getDisciplineFocus()
		{
			return Alias::lookup($this->discipline_focus);
		}
		
		public function setDisciplineFocus(\Mechanics\DisciplineFocus $discipline)
		{
			if(!$this->discipline_primary)
				throw new \Exceptions\User(
										'Primary discipline must be set before focus can be set.',
										\Exceptions\User::BAD_CONFIG
									);
			
			$this->discipline_focus = $discipline->getAlias()->getAliasName();
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
	}
?>
