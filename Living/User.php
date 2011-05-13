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
	
		private $socket;
		protected $last_input = '';
		protected $nourishment = 0;
		protected $thirst = 0;
		private $login = array('alias' => false);
		private $command_buffer = array();
		private static $instances = array();
		
		public function __construct($socket)
		{
			$this->socket = $socket;
			$this->login['alias'] = false;
		}
		
		public function prompt()
		{
			return 'hp:' . $this->getHp() . '/' . $this->getMaxHp() . ' mana: ' . $this->getMana() . '/' . $this->getMaxMana() . ' mv: ' . $this->getMovement() . '/' . $this->getMaxMovement() . ' >';
		}
		
		public function addCommandBuffer($input)
		{
			$this->command_buffer[] = $input;
		}
		
		public function clearCommandBuffer()
		{
			$this->command_buffer = array();
		}
		
		public function shiftCommandBuffer()
		{
			if(sizeof($this->command_buffer) > 0)
				return array_shift($this->command_buffer);
			else
				return null;
		}
		
		public function setId(int $id) {}
		
		public function loadByAliasAndPassword($alias, $password)
		{
			
			$row = \Mechanics\Db::getInstance()->query('SELECT * FROM users WHERE LOWER(alias) = ? AND pass = ?', array(strtolower($alias), sha1('mud password salt!' . $password)))->getResult()->fetch_object();
			if(empty($row))
				throw new \Exception('No user found');
			$this->alias = $row->alias;
			$this->level = $row->level;
			$this->copper = $row->copper;
			$this->silver = $row->silver;
			$this->gold = $row->gold;
			$this->password = $row->pass;
			$this->setRace($row->race);
			$this->experience = $row->experience;
			$this->exp_per_level = $row->exp_per_level;
			$this->id = $row->id;
			$this->thirst = $row->thirst;
			$this->nourishment = $row->nourishment;
			
			parent::__construct($row->fk_room_id);
			
			$discipline = 'Disciplines\\' . $row->discipline;
			$this->discipline = new $discipline($this);
			self::$instances[$this->id] = $this;
			\Mechanics\Affect::reapplyFromDb($this, $this->getTable());
		}
		public static function getInstances() { return self::$instances; }
		public function getTable() { return 'users'; }
		public function setLastInput($input) { $this->last_input = $input; }
		public function getLastInput() { return $this->last_input; }
		public function getSocket() { return $this->socket; }
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
		public function decreaseRacialNourishmentAndThirst()
		{
			$this->nourishment -= $this->getRace()->getDecreaseNourishment();
			$this->thirst -= $this->getRace()->getDecreaseThirst();
		}
		public function getNourishment() { return $this->nourishment; }
		public function getThirst() { return $this->thirst; }
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
			\Mechanics\Debug::addDebugLine("Saving actor " . $this->getAlias(true));
			if($this->id)
				\Mechanics\Db::getInstance()->query('UPDATE ' . $this->getTable() . ' SET 
											alias = ?,
											level = ?,
											copper = ?,
											silver = ?,
											gold = ?,
											pass = ?,
											race = ?,
											experience = ?,
											exp_per_level = ?,
											fk_room_id = ? WHERE id = ?', array(
											$this->getAlias(),
											$this->level,
											$this->copper,
											$this->silver,
											$this->gold,
											$this->password,
											$this->getRaceStr(),
											$this->experience,
											$this->exp_per_level,
											$this->getRoom()->getId(),
											$this->id));
			else
			{
				\Mechanics\Db::getInstance()->query('INSERT INTO users (alias, level, copper, silver, gold, pass, race, fk_room_id, discipline) VALUES
															(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
																$this->getAlias(),
																$this->level,
																$this->copper,
																$this->silver,
																$this->gold,
																$this->password,
																(string)$this->race,
																$this->getRoom()->getId(),
																(string)$this->discipline
															));
				$this->id = \Mechanics\Db::getInstance()->insert_id;
			}
			$this->inventory->save();
			$this->equipped->save();
			$this->ability_set->save();
			$this->attributes->save($this->getTable(), $this->id);
			foreach($this->affects as $affect)
				$affect->save($this->getTable(), $this->id);
		}
		
		public function resetLogin() { $this->login = array('alias' => false); }
		
		public function handleLogin($input)
		{
			if($this->login['alias'] === false)
			{
				$this->login['alias'] = $input;
				$row = \Mechanics\Db::getInstance()->query(
					'SELECT * FROM users WHERE alias = ?',
					$this->login['alias'])->getResult()->fetch_object();
				if(!empty($row))
				{
					\Mechanics\Server::out($this, 'Password: ', false);
					$this->login['pass'] = false;
				}
				else
				{
					\Mechanics\Server::out($this, 'Did I get that right, ' . $this->login['alias'] . '? (y/n) ', false);
					$this->login['confirm_new'] = false;
				}
				return;
			}
			
			if(isset($this->login['pass']) && $this->login['pass'] === false)
			{
				$this->login['pass'] = $input;
			
				$this->loadByAliasAndPassword($this->login['alias'], $this->login['pass']);
			
				if($this->id == 0)
				{
					\Mechanics\Server::out($this, 'Wrong password.');
					\Mechanics\Server::getInstance()->disconnectUser($this);
					$this->clearSocket();
				}
				else
					\Commands\Look::perform($this);

				return;
			}
			
			if(isset($this->login['confirm_new']) && $this->login['confirm_new'] === false)
			{
				$this->login['confirm_new'] = $input;
				switch($this->login['confirm_new'])
				{
					case 'y':
					case 'yes':
						$this->login['new_pass'] = false;
						\Mechanics\Server::out($this, "New character.");
						return \Mechanics\Server::out($this, "Give me a password for ".$this->login['alias'].": ", false);
					case 'n':
					case 'no':
						$this->login = array('alias' => false);
						return \Mechanics\Server::out($this, "Ok, what IS it then? ", false);
					default:
						return \Mechanics\Server::out($this, 'Please type Yes or No: ', false);
				}
			}
			
			if(isset($this->login['new_pass']) && $this->login['new_pass'] === false)
			{
				$this->login['new_pass'] = $input;
				$this->login['new_pass_2'] = false;
				return \Mechanics\Server::out($this, 'Please retype password: ', false);
			}
			
			if(isset($this->login['new_pass_2']) && $this->login['new_pass_2'] === false)
			{
				$this->login['new_pass_2'] = $input;
				if($this->login['new_pass'] == $this->login['new_pass_2'])
				{
					$this->login['race'] = false;
					$this->racesAvailable();
				}
				else
				{
					unset($this->login['new_pass_2']);
					$this->login['new_pass'] = false;
					\Mechanics\Server::out($this, "Passwords don't match.");
					\Mechanics\Server::out($this, "Retype password: ", false);
				}
				return;
			}
			
			if(isset($this->login['race']) && $this->login['race'] === false)
			{
				$this->login['race'] = $input;
				
				try
				{
					$this->setRace($this->login['race']);
				}
				catch(Exception $e)
				{
					\Mechanics\Server::out($this, "That's not a valid race.");
					$this->racesAvailable();
					$this->login['race'] = false;
					return;
				}
				
				\Mechanics\Server::out($this, "What is your sex (m/f)? ", false);
				$this->login['sex'] = '';
			}
			
			if(isset($this->login['sex']) && $this->login['sex'] === '')
			{
				if($input == 'm' || $input == 'male')
					$this->login['sex'] = 'm';
				else if($input == 'f' || $input == 'female')
					$this->login['sex'] = 'f';
				else
					\Mechanics\Server::out($this, "That's not a sex.\nWhat IS your sex? ", false);
				
				if($this->login['sex'])
				{
					\Mechanics\Server::out($this, "Select a class [barbarian crusader rogue wizard]: ", false);
					$this->login['discipline'] = false;
					return;
				}
			}
			
			if(isset($this->login['discipline']) && $this->login['discipline'] === false)
			{
				
				$discipline = 'Disciplines\\' . ucfirst($input);
				
				if(class_exists($discipline))
				{
					$this->discipline = new $discipline($this);
					$this->login['discipline'] = true;
					$this->login['align'] = false;
					\Mechanics\Server::out($this, "\nYou may be good, neutral, or evil.\nWhich alignment (g/n/e)? ", false);
				}
				else
				{
					\Mechanics\Server::out($this, "That's not a class.\nWhat IS your class? ", false);
				}
				return;
			}
			
			if(isset($this->login['align']) && $this->login['align'] === false)
			{
				if($input == 'g' || $input == 'good')
					$this->login['align'] = 500;
				else if($input == 'n' || $input == 'neutral')
					$this->login['align'] = 0;
				else if($input == 'e' || $input == 'evil')
					$this->login['align'] = -500;
				else
					return \Mechanics\Server::out($this, "That's not a valid alignment.\nWhich alignment (g/n/e)? ", false);
				
				$this->login['custom'] = false;
				return \Mechanics\Server::out($this, "Customization takes time, but allows a wider range of skills and abilities.\nCustomize (y/n)? ", false);
			}
			
			if(isset($this->login['custom']) && $this->login['custom'] === false)
			{
				if($input == 'y' || $input == 'yes')
					$this->login['custom'] = 1;
				else if($input == 'n' || $input == 'no')
					$this->login['custom'] = 2;
				else
					return \Mechanics\Server::out($this, "\nCustomize (y/n)? ", false);
			}
			
			if(isset($this->login['custom']) && $this->login['custom'] === 1)
			{
				if(!isset($this->login['custom_start']))
				{
					$this->login['custom_start'] = 1;
					return $this->customizeList();
				}
			}
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
				\Mechanics\Server::out($this, "Now let's figure out your attributes...");
				$this->login['attr'] = 0;
				\Mechanics\Server::out($this, "You have " . (10 - $this->login['attr']) . " points left to distribute to your attributes.");
				\Mechanics\Server::out($this,
					'Str ' . $this->getStr(true) . ' Int ' . $this->getInt(true) . ' Wis ' . $this->getWis(true) . ' Dex ' . $this->getDex(true) . ' Con ' . $this->getCon(true));
				\Mechanics\Server::out($this, "Add a point to: ", false);
			
			if(isset($this->login['attr']) && is_numeric($this->login['attr']))
			{
				
				$method_get = 'get' . $input;
				$method_set = 'set' . $input;
				
				if(!method_exists($this, $method_get) || !method_exists($this, $method_set))
					return \Mechanics\Server::out($this, 'Which attribute is that? ', false);
				
				if($this->$method_set($this->$method_get(true) + 1, true))
					$this->login['attr']++;
				else
					\Mechanics\Server::out($this, 'This attribute is maxed!');
				
				if($this->login['attr'] < 10)
				{
					\Mechanics\Server::out($this, "You have " . (10 - $this->login['attr']) . " points left to distribute to your attributes.");
					\Mechanics\Server::out($this,
						'Str ' . $this->getStr(true) . ' Int ' . $this->getInt(true) . ' Wis ' . $this->getWis(true) . ' Dex ' . $this->getDex(true) . ' Con ' . $this->getCon(true));
					\Mechanics\Server::out($this, "Add a point to: ", false);
				}
				else
				{
					$this->login['attr'] = true;
					return;
				}
			}
			
			if(isset($this->login['finish']) && $this->login['finish'] === false)
			{
				$this->alias = $this->login['alias'];
				$this->copper = 20;
				$this->silver = 0;
				$this->gold = 0;
				$this->password = sha1('mud password salt!' . $this->login['new_pass']);
				$this->experience = 1000;
				$this->exp_per_level = 1000;
				$this->level = 1;
				$this->thirst = 5;
				$this->nourishment = 5;
				$this->setRoom(\Mechanics\Room::find(3));
				
				parent::__construct($this->getRoom()->getId());
				$this->save();
				self::$instances[$this->id] = $this;
				$this->discipline->assignGroup();
				$this->ability_set->save();
				
				\Commands\Look::perform($this);
			}
		}
		private function racesAvailable()
		{
			\Mechanics\Server::out($this, 'The following races are available: ');
			\Mechanics\Server::out($this, '[human undead faerie elf ogre] ');
			\Mechanics\Server::out($this, 'What is your race (help for more information)? ', false);
		}
		private function customizeList()
		{
			\Mechanics\Server::out($this, "\nThe following skills and groups are available to your character:\n(this list may be seen again by typing list)");
			
		}
		public function handleDeath()
		{
			parent::handleDeath();
			\Commands\Look::perform($this);
		}
		public function clearSocket()
		{
			$this->socket = null;
		}
	}

?>
