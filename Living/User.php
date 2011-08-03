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
	class User extends \Mechanics\Fighter
	{
		protected $nourishment = 0;
		protected $thirst = 0;
		protected $trains = 0;
		protected $practices = 0;
		protected $password = '';
		private $client = null;
		protected $discipline_primary = null;
		protected $discipline_focus = null;
		protected static $instances = array();
		
		public function __construct()
		{
			parent::__construct();
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
		
		public function setClient(\Mechanics\Client $client)
		{
			$this->client = $client;
		}
		
		public function prompt()
		{
			return 'hp:' . $this->getHp() . '/' . $this->getMaxHp() . ' mana: ' . $this->getMana() . '/' . $this->getMaxMana() . ' mv: ' . $this->getMovement() . '/' . $this->getMaxMovement() . ' >';
		}
		
		public function setPassword($password)
		{
			$this->password = sha1('mud password salt!'.$password);
		}
		
		public function getPassword()
		{
			return $this->password;
		}
		
		public function tick($init = false)
		{
			parent::tick();
			if(!$init)
			{
				$this->decreaseRacialNourishmentAndThirst();
				if($this->nourishment < 0)
					\Mechanics\Server::out($this, "You are hungry.");
				if($this->thirst < 0)
					\Mechanics\Server::out($this, "You are thirsty.");
				$this->save();
			}
			\Mechanics\Server::out($this, "\n" . $this->prompt(), false);
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
		}
		public function increaseThirst($thirst)
		{
			if($this->thirst < 0)
				$this->thirst = $thirst;
			else
				$this->thirst += $thirst;
		}
		
		public function save()
		{
			$client = $this->client;
			$this->client = null;
			$db = \Mechanics\Dbr::instance();
			$db->set('user'.$this->alias, serialize($this));
			$this->client = $client;
		}
		
		public function handleDeath()
		{
			parent::handleDeath();
			\Commands\Look::perform($this);
		}
		
		public function getDisciplinePrimary()
		{
			return \Mechanics\Alias::lookup($this->discipline_primary);
		}
		
		public function setDisciplinePrimary(\Mechanics\DisciplinePrimary $discipline)
		{
			$this->discipline_primary = $discipline->getAlias()->getAliasName();
		}
		
		public function getDisciplineFocus()
		{
			return \Mechanics\Alias::lookup($this->discipline_focus);
		}
		
		public function setDisciplineFocus(\Mechanics\DisciplineFocus $discipline)
		{
			$this->discipline_focus = $discipline->getAlias()->getAliasName();
		}
	}

?>
