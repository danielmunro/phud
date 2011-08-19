<?php

	namespace Mechanics;
	class Client
	{
		private $user = null;
		private $unverified_user = null;
		private $socket = null;
		private $command_buffer = array();
		private $login = array('alias' => false);
		protected $last_input = '';
		
		public function __construct($socket)
		{
			$this->socket = $socket;
		}
		
		public function setUser(User $user)
		{
			$this->user = $user;
		}
		
		public function getUser()
		{
			return $this->user;
		}
		
		public function getSocket()
		{
			return $this->socket;
		}
		
		public function clearSocket()
		{
			$this->socket = null;
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
		
		public function setLastInput($input)
		{
			$this->last_input = $input;
		}
		
		public function getLastInput()
		{
			return $this->last_input;
		}
		
		public function isValidated($password)
		{			
			$pw_hash = sha1($this->unverified_user->getAlias().$this->unverified_user->getDateCreated().$password);
			return $this->unverified_user->getPassword() == $pw_hash;
		}
		
		///////////////////////////////////////////////////////////
		// Login
		///////////////////////////////////////////////////////////
		public function handleLogin($args)
		{
			$input = array_shift($args);
			if($this->login['alias'] === false)
			{
				$this->login['alias'] = $input;
				
				$db = Dbr::instance();
				$this->unverified_user = unserialize($db->get('user'.$input));
				
				if(!empty($this->unverified_user))
				{
					$this->unverified_user->setClient($this);
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
			
				if($this->isValidated($input))
				{
					$this->user = $this->unverified_user;
					$this->unverified_user = null;
					$look = Alias::lookup('look');
					$look->perform($this->user);
					return true;
				}
				else
				{
					\Mechanics\Server::out($this, 'Wrong password.');
					\Mechanics\Server::getInstance()->disconnectClient($this);
					return false;
				}
			}
			
			if(isset($this->login['confirm_new']) && $this->login['confirm_new'] === false)
			{
				$this->login['confirm_new'] = $input;
				switch($this->login['confirm_new'])
				{
					case 'y':
					case 'yes':
						$this->unverified_user = new \Living\User($this->socket);
						$this->unverified_user->setAlias($this->login['alias']);
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
					$this->unverified_user->setPassword($this->login['new_pass']);
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
				
				$race = Alias::lookup($this->login['race']);
				if($race instanceof Race)
					$this->unverified_user->setRace($this->login['race']);
				else
				{
					\Mechanics\Server::out($this, "That's not a valid race.");
					$this->racesAvailable();
					$this->login['race'] = false;
					return;
				}
				
				\Mechanics\Server::out($this, "What is your sex (m/f)? ", false);
				$this->login['sex'] = '';
				return;
			}
			
			if(isset($this->login['sex']) && $this->login['sex'] === '')
			{
				if($input == 'm' || $input == 'male')
					$this->login['sex'] = 'm';
				else if($input == 'f' || $input == 'female')
					$this->login['sex'] = 'f';
				else
					return \Mechanics\Server::out($this, "That's not a sex.\nWhat IS your sex? ", false);
				
				if($this->login['sex'])
				{
					$this->unverified_user->setSex($this->login['sex']);
					\Mechanics\Server::out($this, "Select a primary discipline [warrior cleric thief mage]: ", false);
					$this->login['disciplinep'] = false;
					return;
				}
			}
			
			if(isset($this->login['disciplinep']) && $this->login['disciplinep'] === false)
			{
				$discipline = Alias::lookup($input);
				if($discipline instanceof DisciplinePrimary)
				{
					$this->unverified_user->setDisciplinePrimary($discipline);
					$focuses = $discipline->getDisciplineFocuses();
					$this->login['disciplinep'] = true;
					$this->login['disciplinef'] = false;
					\Mechanics\Server::out($this, "Ok.\nSelect a discipline focus [", false);
					foreach($focuses as $focus)
						\Mechanics\Server::out($this, $focus->getAlias()." ", false);
					\Mechanics\Server::out($this, "]: ", false);
				}
				else
					\Mechanics\Server::out($this, "That's not a primary discipline. What IS your primary discipline? ", false);
				return;
			}
			
			if(isset($this->login['disciplinef']) && $this->login['disciplinef'] === false)
			{
				$discipline = Alias::lookup($input);
				if(in_array($discipline, $this->unverified_user->getDisciplinePrimary()->getDisciplineFocuses()))
				{
					$this->unverified_user->setDisciplineFocus($discipline);
					$this->login['disciplinef'] = true;
					$this->login['align'] = false;
					\Mechanics\Server::out($this, "\nYou may be good, neutral, or evil.\nWhich alignment (g/n/e)? ", false);
				}
				else
					\Mechanics\Server::out($this, "That's not a discipline focus. What IS your discipline focus? ", false);
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
				
				$this->unverified_user->setAlignment($this->login['align']);
				$this->login['custom'] = 0;
				\Mechanics\Server::out($this, "Skill and spell customization. Choose from the list below of skills and spells:");
			}
			if(isset($this->login['custom']) && ($this->login['custom'] === 0 || $this->login['custom'] === 1))
			{
				$ability_list = array_merge(
								$this->unverified_user->getDisciplinePrimary()->getAbilities(),
								$this->unverified_user->getDisciplineFocus()->getOtherDiscipline($this->unverified_user)->getAbilities()
							);
				if($input == 'list' || $this->login['custom'] === 0)
				{
					$this->login['custom'] = 1;
					$this->abilityList($ability_list);
				}
				else if($input == 'add')
				{
					$try_add = Alias::lookup(implode(' ', $args));
					
					if(!$try_add instanceof Skill && !$try_add instanceof Spell_Group)
						return Server::out($this, "You can't add that.");
					
					if($this->unverified_user->getAbilitySet()->getLearnedAbility($try_add))
						return Server::out($this, "You already know that.");
					
					// Find out if it's a skill or spell
					$look_in = array();
					if($try_add instanceof Skill)
					{
						if(in_array($try_add, $ability_list))
						{
							$this->unverified_user->getAbilitySet()->addAbility($try_add);
							Server::out($this, "You added ".$try_add->getAlias().".");
						}
					}
					else if($try_add instanceof Spell_Group)
					{
						$this->unverified_user->getAbilitySet()->addAbilities($try_add->getSpells());
						Server::out($this, "You added ".$try_add->getAlias().".");
					}
					else
						Server::out($this, "You can't add that.");
				}
				else if($input == 'drop')
				{
					$try_drop = Alias::lookup(implode(' ', $args));
					$learned = $this->unverified_user->getAbilitySet()->getLearnedAbility($try_drop);
					if($learned)
					{
						$this->unverified_user->getAbilitySet()->dropAbility($learned);
						return Server::out($this, $learned." dropped.");
					}
					return Server::out($this, "You don't know that.");
				}
				else if($input == 'learned')
				{
					$this->abilityList($this->unverified_user->getAbilitySet()->getAbilities());
				}
				else if($input == 'done')
				{
					$this->login['custom'] = 3;
					\Mechanics\Server::out($this, "Now let's figure out your attributes...");
					$this->login['attr'] = 0;
					$this->login['attr_mod'] = array('str' => 0, 'int' => 0, 'wis' => 0, 'dex' => 0, 'con' => 0);
					\Mechanics\Server::out($this, "You have " . (10 - $this->login['attr']) . " points left to distribute to your attributes.");
					\Mechanics\Server::out($this,
						'Str ' . $this->unverified_user->getBaseStr() . ' Int ' . $this->unverified_user->getBaseInt() . ' Wis ' . $this->unverified_user->getBaseWis() . 
						' Dex ' . $this->unverified_user->getBaseDex() . ' Con ' . $this->unverified_user->getBaseCon());
					\Mechanics\Server::out($this, "Add a point to: ", false);
				}
				
				if($this->login['custom'] !== 3)
				{
					$cp = $this->unverified_user->getCreationPoints();
					Server::out($this, "\nYou have ".$cp." creation points, and ".$this->unverified_user->getExperiencePerLevel()." experience per level.");
					return Server::out($this, "What would you like to do (add, list, drop, done)?");
				}
			}
			if(isset($this->login['attr']) && is_numeric($this->login['attr']))
			{
				
				$method_get = 'getBase'.ucfirst($input);
				$method_set = 'set'.ucfirst($input);
				$method_get_max = 'getMax'.ucfirst($input);
				
				if(!method_exists($this->unverified_user, $method_get) || !method_exists($this->unverified_user, $method_set))
					return \Mechanics\Server::out($this, 'Which attribute is that? ', false);
				
				if($this->login['attr'] + $this->login['attr_mod'][$input] + 1 > 10)
				{
					\Mechanics\Server::out($this, "You don't have enough points to do that.");
				}
				else if($this->unverified_user->$method_get() < $this->unverified_user->$method_get_max())
				{
					$this->unverified_user->$method_set($this->unverified_user->$method_get() + 1);
					$this->login['attr'] += $this->login['attr_mod'][$input] + 1;
					$this->login['attr_mod'][$input]++;
				}
				else
					\Mechanics\Server::out($this, 'This attribute is maxed!');
				
				$modifiable = false;
				foreach($this->login['attr_mod'] as $attr)
				{
					if(10 - $this->login['attr'] >= $attr + 1)
					{
						$modifiable = true;
						break;
					}
				}
				
				if($this->login['attr'] < 10 && $modifiable)
				{
					\Mechanics\Server::out($this, "You have " . (10 - $this->login['attr']) . " points left to distribute to your attributes.");
					\Mechanics\Server::out($this,
						'Str ' . $this->unverified_user->getBaseStr() . ' Int ' . $this->unverified_user->getBaseInt() . ' Wis ' . $this->unverified_user->getBaseWis() . 
						' Dex ' . $this->unverified_user->getBaseDex() . ' Con ' . $this->unverified_user->getBaseCon());
					\Mechanics\Server::out($this, "Add a point to: ", false);
				}
				else
				{
					$this->unverified_user->addTrains(10 - $this->login['attr']);
					$this->login['attr'] = true;
					$this->login['finish'] = false;
				}
			}
			
			if(isset($this->login['finish']) && $this->login['finish'] === false)
			{
				$this->user = $this->unverified_user;
				$this->user->setAlias($this->login['alias']);
				$this->user->increaseCopper(20);
				$this->user->setPassword(sha1($this->user->getAlias().$this->user->getDateCreated().$this->login['new_pass']));
				$this->user->setRoom(Room::find(Room::START_ROOM));
				$this->user->setClient($this);
				$this->user->save();
				
				$look = Alias::lookup('look');
				$look->perform($this->user);
			}
		}
		
		private function abilityList($ability_list)
		{
			Server::out($this, "\nspell groups:");
			$groups = array();
			foreach($ability_list as $i => $a)
			{
				if($a instanceof Learned_Ability)
				{
					$s = $a->getAbility();
					$learned_check = false;
				}
				else
				{
					$s = $a;
					$learned_check = $this->unverified_user->getAbilitySet()->getLearnedAbility($s);
				}
				
				if($s instanceof Spell && !$learned_check && !in_array($s->getSpellGroup(), $groups))
				{
					$padding = substr("                          ", strlen($s->getSpellGroup()->getAlias()));
					Server::out($this, $s->getSpellGroup()->getAlias().$padding.$s->getCreationPoints()."cp");
					$groups[] = $s->getSpellGroup();
				}
			}
			Server::out($this, "\nskills:");
			foreach($ability_list as $i => $a)
			{
				if($a instanceof Learned_Ability)
				{
					$s = $a->getAbility();
					$learned_check = false;
				}
				else
				{
					$s = $a;
					$learned_check = $this->unverified_user->getAbilitySet()->getLearnedAbility($s);
				}
				if($s instanceof Skill && !$learned_check)
				{
					$padding = substr("                          ", strlen($s->getAlias()));
					Server::out($this, $s->getAlias().$padding.$s->getCreationPoints()."cp");
				}
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
	}

?>
