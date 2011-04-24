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
	class User extends \Mechanics\Actor
	{
	
		protected $id;
		private $socket;
		protected $last_input = '';
		protected $nourishment = 0;
		protected $thirst = 0;
		private $login = array('alias' => false);
		private $command_buffer = array();
		
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
		
		public function loadByAliasAndPassword($alias, $password)
		{
			
			$row = \Mechanics\Db::getInstance()->query('SELECT * FROM users WHERE LOWER(alias) = ? AND pass = ?', array(strtolower($alias), sha1('mud password salt!' . $password)))->getResult()->fetch_object();
			if(empty($row))
				throw new \Exception('No user found');
			$this->alias = $row->alias;
			$this->hp = $row->hp;
			$this->max_hp = $row->max_hp;
			$this->mana = $row->mana;
			$this->max_mana = $row->max_mana;
			$this->movement = $row->movement;
			$this->max_movement = $row->max_movement;
			$this->level = $row->level;
			$this->copper = $row->copper;
			$this->silver = $row->silver;
			$this->gold = $row->gold;
			$this->password = $row->pass;
			$this->base_str = $this->current_str = $row->str;
			$this->base_int = $this->current_int = $row->int;
			$this->base_wis = $this->current_wis = $row->wis;
			$this->base_dex = $this->current_dex = $row->dex;
			$this->base_con = $this->current_con = $row->con;
			$this->setRace($row->race);
			$this->experience = $row->experience;
			$this->exp_per_level = $row->exp_per_level;
			$this->id = $row->id;
			$this->thirst = $row->thirst;
			$this->nourishment = $row->nourishment;
			
			parent::__construct($row->fk_room_id);
			
			$discipline = 'Disciplines\\' . $row->discipline;
			$this->discipline = new $discipline($this);
			//$this->ability_set->addAbility(new \Skills\Berserk(100, $this->id));
			\Mechanics\Affect::reapplyFromDb($this, 'users');
		}
		public function getTable() { return 'users'; }
		public function setLastInput($input) { $this->last_input = $input; }
		public function getLastInput() { return $this->last_input; }
		public function getSocket() { return $this->socket; }
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
		
		public function getId() { return $this->id; }
		
		public function save()
		{
			\Mechanics\Debug::addDebugLine("Saving actor " . $this->getAlias(true));
			
			$this->inventory->save();
			$this->equipped->save();
			
			if($this->id)
				$this->ability_set->save();
			
			if($this->id)
				\Mechanics\Db::getInstance()->query('UPDATE ' . $this->getTable() . ' SET 
											alias = ?,
											hp = ?,
											max_hp = ?,
											mana = ?,
											max_mana = ?,
											movement = ?,
											max_movement = ?,
											level = ?,
											copper = ?,
											silver = ?,
											gold = ?,
											pass = ?,
											str = ?,
											`int` = ?,
											wis = ?,
											dex = ?,
											con = ?,
											race = ?,
											experience = ?,
											exp_per_level = ?,
											fk_room_id = ? WHERE id = ?', array(
											$this->getAlias(),
											$this->hp,
											$this->max_hp,
											$this->mana,
											$this->max_mana,
											$this->movement,
											$this->max_movement,
											$this->level,
											$this->copper,
											$this->silver,
											$this->gold,
											$this->password,
											$this->base_str,
											$this->base_int,
											$this->base_wis,
											$this->base_dex,
											$this->base_con,
											$this->getRaceStr(),
											$this->experience,
											$this->exp_per_level,
											$this->getRoom()->getId(),
											$this->id));
			else
			{
				\Mechanics\Db::getInstance()->query('INSERT INTO users (alias, hp, max_hp, mana, max_mana, movement, max_movement, level, copper, silver, gold, pass, str, `int`, wis, dex, con, race, fk_room_id, discipline) VALUES
															(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
																$this->getAlias(),
																$this->hp,
																$this->max_hp,
																$this->mana,
																$this->max_mana,
																$this->movement,
																$this->max_movement,
																$this->level,
																$this->copper,
																$this->silver,
																$this->gold,
																$this->password,
																$this->base_str,
																$this->base_int,
																$this->base_wis,
																$this->base_dex,
																$this->base_con,
																(string)$this->race,
																$this->getRoom()->getId(),
																(string)$this->discipline
															));
				$this->id = \Mechanics\Db::getInstance()->insert_id;
			}
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
					\Mechanics\Server::out($this, 'Please give me yer secret password: ', false);
					$this->login['pass'] = false;
				}
				else
				{
					\Mechanics\Server::out($this, 'Ah, a newcomer to our realm! I will help get you set up to begin your adventure.');
					\Mechanics\Server::out($this, 'My senses are old and weary, but I must keep up the records!');
					\Mechanics\Server::out($this, 'First things first, did I hear ye right, yer name is ' . $this->login['alias'] . '? (y/n) ', false);
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
					\Mechanics\Server::out($this, 'Hmm... Did I hear you right? Try again.');
					$this->login = array('alias' => false);
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
						return \Mechanics\Server::out($this, "Alright, good. Now I'll need a secret password from ye so I'll recognize you later. What is that password? ", false);
					case 'n':
					case 'no':
						$this->login = array('alias' => false);
						return \Mechanics\Server::out($this, "Must've misheard ye. What is yer name again? ", false);
					default:
						return \Mechanics\Server::out($this, 'Eh? It was a yes or no question, so which is it? ', false);
				}
			}
			
			if(isset($this->login['new_pass']) && $this->login['new_pass'] === false)
			{
				$this->login['new_pass'] = $input;
				$this->login['new_pass_2'] = false;
				return \Mechanics\Server::out($this, 'Can I get that again? ', false);
			}
			
			if(isset($this->login['new_pass_2']) && $this->login['new_pass_2'] === false)
			{
				$this->login['new_pass_2'] = $input;
				if($this->login['new_pass'] == $this->login['new_pass_2'])
				{
					$this->login['race'] = false;
					\Mechanics\Server::out($this, 'Alright! What race are you?');
					\Mechanics\Server::out($this, '[human undead faerie elf ogre] ', false);
				}
				else
				{
					unset($this->login['new_pass_2']);
					$this->login['new_pass'] = false;
					\Mechanics\Server::out($this, "Yer passwords don't match. What is yer password? ", false);
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
					\Mechanics\Server::out($this, "I'm not familiar with that race, please tell me again? ", false);
					$this->login['race'] = false;
					return;
				}
				
				\Mechanics\Server::out($this, "Ah! Right, you'll have to forgive my vision. I can see you now.");
				\Mechanics\Server::out($this, "Now let's figure out your attributes...");
				$this->login['attr'] = 0;
				\Mechanics\Server::out($this, "You have " . (10 - $this->login['attr']) . " points left to distribute to your attributes.");
				\Mechanics\Server::out($this,
					'Str ' . $this->getStr(true) . ' Int ' . $this->getInt(true) . ' Wis ' . $this->getWis(true) . ' Dex ' . $this->getDex(true) . ' Con ' . $this->getCon(true));
				\Mechanics\Server::out($this, "Add a point to: ", false);
			}
			
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
					$this->login['discipline'] = false;
					$this->login['attr'] = true;
					\Mechanics\Server::out($this, "Please select your primary discipline:");
					\Mechanics\Server::out($this, "[barbarian crusader rogue wizard]");
					return;
				}
			}
			
			if(isset($this->login['discipline']) && $this->login['discipline'] === false)
			{
				$this->login['discipline'] = $input;
				
				$discipline = 'Disciplines\\' . ucfirst($this->login['discipline']);
				
				if(class_exists($discipline))
				{
					$this->discipline = new $discipline($this);
					$this->login['finish'] = false;
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
				
				$this->discipline->assignGroup();
				$this->ability_set->save();
				
				\Commands\Look::perform($this);
			}
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
