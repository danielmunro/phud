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

	class User extends Actor
	{
	
		private $socket;
		private $logged_in = false;
		protected $id = 0;
		protected $last_input = '';
		protected $nourishment = 0;
		protected $thirst = 0;
		private $login = array('alias' => false);
		private $command_buffer = array();
		
		public function __construct($socket)
		{
			$this->socket = $socket;
		}
		
		public function prompt()
		{
			return 'hp:' . $this->getHp() . '/' . $this->getMaxHp() . ' mana: ' . $this->getMana() . '/' . $this->getMaxMana() . ' mv: ' . $this->getMovement() . '/' . $this->getMaxMovement() . ' >';
		}
		
		public function handleLogin($input)
		{
			if($this->login['alias'] === false)
			{
				$this->login['alias'] = $input;
				$row = Db::getInstance()->query(
					'SELECT * FROM users WHERE alias = ?',
					$this->login['alias'])->getResult()->fetch_object();
				if(!empty($row))
				{
					Server::out($this, 'Please give me yer secret password: ', false);
					$this->login['pass'] = false;
				}
				else
				{
					Server::out($this, 'Ah, a newcomer to our realm! I will help get you set up to begin your adventure.');
					Server::out($this, 'My senses are old and weary, but I must keep up the records!');
					Server::out($this, 'First things first, did I hear ye right, yer name is ' . $this->login['alias'] . '? (y/n) ', false);
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
					Server::out($this, 'Hmm... Did I hear you right? Try again.');
					$this->login = array('alias' => false);
				}
				else
				{
					$this->logged_in = true;
					Command::find('Command_Look')->perform($this);
				}
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
						Server::out($this, "Alright, good. Now I'll need a secret password from ye so I'll recognize you later. What is that password? ", false);
						return;
					case 'n':
					case 'no':
						$this->login = array('alias' => false);
						Server::out($this, "Must've misheard ye. What is yer name again? ", false);
						return;
					default:
						Server::out($this, 'Eh? It was a yes or no question, so which is it? ', false);
						return;
				}
			}
			
			if(isset($this->login['new_pass']) && $this->login['new_pass'] === false)
			{
				$this->login['new_pass'] = $input;
				$this->login['new_pass_2'] = false;
				Server::out($this, 'Can I get that again? ', false);
				return;
			}
			
			if(isset($this->login['new_pass_2']) && $this->login['new_pass_2'] === false)
			{
				$this->login['new_pass_2'] = $input;
				if($this->login['new_pass'] == $this->login['new_pass_2'])
				{
					$this->login['race'] = false;
					Server::out($this, 'Alright! What race are you?');
					Server::out($this, '[human undead faerie] ', false);
				}
				else
				{
					unset($this->login['new_pass_2']);
					$this->login['new_pass'] = false;
					Server::out($this, "Yer passwords don't match. What is yer password? ", false);
				}
				return;
			}
			
			if(isset($this->login['race']) && $this->login['race'] === false)
			{
				$this->login['race'] = $input;
				switch($this->login['race'])
				{
					case 'human':
						$this->setRace('human');
						Server::out($this, "Ah! Right, you'll have to forgive my vision. I can see you now.");
						Server::out($this, "Now let's figure out your statistics...");
						$this->login['attr'] = 0;
						Server::out($this, "You have " . (10 - $this->login['attr']) . " points left to distribute to your attributes.");
						Server::out($this,
							'Str ' . $this->getStr() . ' Int ' . $this->getInt() . ' Wis ' . $this->getWis() . ' Dex ' . $this->getDex() . ' Con ' . $this->getCon());
						Server::out($this, "Add a point to: ", false);
						return;
					default:
						Server::out($this, "I'm not familiar with that race, please tell me again? ", false);
				}
			}
			
			if(isset($this->login['attr']) && is_numeric($this->login['attr']))
			{
				
				switch($input)
				{
					case 'str':
						try
						{
							$this->setStr($this->getStr() + 1);
						}
						catch(Actor_Exception $e)
						{
							$this->login['attr']--;
							Server::out($this, 'This attribute is maxed!');
						}
						break;
					case 'int':
						try
						{
							$this->setInt($this->getInt() + 1);
						}
						catch(Actor_Exception $e)
						{
							$this->login['attr']--;
							Server::out($this, 'This attribute is maxed!');
						}
						break;
					case 'wis':
						try
						{
							$this->setWis($this->getWis() + 1);
						}
						catch(Actor_Exception $e)
						{
							$this->login['attr']--;
							Server::out($this, 'This attribute is maxed!');
						}
						break;
					case 'dex':
						try
						{
							$this->setDex($this->getDex() + 1);
						}
						catch(Actor_Exception $e)
						{
							$this->login['attr']--;
							Server::out($this, 'This attribute is maxed!');
						}
						break;
					case 'con':
						try
						{
							$this->setCon($this->getCon() + 1);
						}
						catch(Actor_Exception $e)
						{
							$this->login['attr']--;
							Server::out($this, 'This attribute is maxed!');
						}
						break;
					default:
						Server::out($this, 'Which attribute is that? ', false);
						return;
				}
				
				$this->login['attr']++;
				
				if($this->login['attr'] < 10)
				{
					Server::out($this, "You have " . (10 - $this->login['attr']) . " points left to distribute to your attributes.");
					Server::out($this,
						'Str ' . $this->getStr() . ' Int ' . $this->getInt() . ' Wis ' . $this->getWis() . ' Dex ' . $this->getDex() . ' Con ' . $this->getCon());
					Server::out($this, "Add a point to: ", false);
				}
				else
				{
					$this->login['finish'] = false;
					$this->login['attr'] = true;
					Server::out($this, "Well done! Press any key to begin your journey.");
				}
			}
			
			if(isset($this->login['finish']) && $this->login['finish'] === false)
			{
				$this->alias = $this->login['alias'];
				$this->hp = 20;
				$this->max_hp = 20;
				$this->mana = 100;
				$this->max_mana = 100;
				$this->movement = 100;
				$this->max_movement = 100;
				$this->copper = 20;
				$this->silver = 0;
				$this->gold = 0;
				$this->password = sha1('mud password salt!' . $this->login['new_pass']);
				$this->experience = 1000;
				$this->exp_per_level = 1000;
				$this->level = 1;
				$this->logged_in = true;
				$this->thirst = 5;
				$this->nourishment = 5;
				$this->setRoom(Room::find(1));
				
				Db::getInstance()->query('INSERT INTO users (alias, hp, max_hp, mana, max_mana, movement, max_movement, level, copper, silver, gold, pass, str, `int`, wis, dex, con, race, fk_room_id) VALUES
															(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
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
																$this->str,
																$this->int,
																$this->wis,
																$this->dex,
																$this->con,
																$this->getRaceStr(),
																$this->getRoom()->getId()
															));
				$this->id = Db::getInstance()->last_insert_id;
				parent::__construct($this->getRoom()->getId());
			}
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
			$row = Db::getInstance()->query('SELECT * FROM users WHERE LOWER(alias) = ? AND pass = ?', array(strtolower($alias), sha1('mud password salt!' . $password)))->getResult()->fetch_object();
			if(empty($row))
				throw new Exception('No user found');
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
			$this->str = $row->str;
			$this->int = $row->int;
			$this->wis = $row->wis;
			$this->dex = $row->dex;
			$this->con = $row->con;
			$this->setRace($row->race);
			$this->setRoom(Room::find($row->fk_room_id));
			$this->experience = $row->experience;
			$this->exp_per_level = $row->exp_per_level;
			$this->id = $row->id;
			$this->thirst = $row->thirst;
			$this->nourishment = $row->nourishment;
			parent::__construct($this->getRoom()->getId());
		}
		public function getTable() { return 'users'; }
		public function setLastInput($input) { $this->last_input = $input; }
		public function getLastInput() { return $this->last_input; }
		public function getLoggedIn() { return $this->logged_in; }
		public function getSocket() { return $this->socket; }
		public function __toString() { return $this->socket; }
		public function save()
		{
			$this->inventory->save();
			parent::save();
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
	}

?>
