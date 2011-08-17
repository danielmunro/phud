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
			$pw_hash = sha1('mud password salt!' . $password);
			return $this->unverified_user->getPassword() == $pw_hash;
		}
		
		///////////////////////////////////////////////////////////
		// Login
		///////////////////////////////////////////////////////////
		public function handleLogin($input)
		{
			if($this->login['alias'] === false)
			{
				$this->login['alias'] = $input;
				
				$db = Dbr::instance();
				$this->unverified_user = $db->get('user'.$input);
				
				if(!empty($this->unverified_user))
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
			
				if($this->isValidated($input))
				{
					$this->user = $this->unverified_user;
					$this->unverified_user = null;
					\Commands\Look::perform($this->user);
				}
				else
				{
					\Mechanics\Server::out($this, 'Wrong password.');
					\Mechanics\Server::getInstance()->disconnectClient($this);
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
				$this->login['custom'] = false;
				return \Mechanics\Server::out($this, "Customization takes time, but allows a wider range of skills and abilities.\nCustomize (y/n)? ", false);
			}
			
			if(isset($this->login['custom']) && $this->login['custom'] === false)
			{
				if($input == 'y' || $input == 'yes')
					$this->login['custom'] = 0;
				else if($input == 'n' || $input == 'no')
					$this->login['custom'] = 2;
				else
					return \Mechanics\Server::out($this, "\nCustomize (y/n)? ", false);
			}
			if(isset($this->login['custom']) && ($this->login['custom'] === 0 || $this->login['custom'] === 1))
			{
				$input = explode(' ', $input);
				$spell_groups = array_merge(
								$this->unverified_user->getDisciplinePrimary()->getAbilitySet()->getSpellGroups(),
								$this->unverified_user->getDisciplineFocus()->getOtherDiscipline($this->unverified_user)->getAbilitySet()->getSpellGroups()
							);
				$skills = array_merge(
									$this->unverified_user->getDisciplinePrimary()->getAbilitySet()->getSkills(),
									$this->unverified_user->getDisciplineFocus()->getOtherDiscipline($this->unverified_user)->getAbilitySet()->getSkills()
								);
				if($input[0] == 'list' || $this->login['custom'] === 0)
				{
					$this->login['custom'] = 1;
					Server::out($this, "Spell Groups:");
					foreach($spell_groups as $i => $s)
					{
						$padding = substr("                          ", strlen($s));
						Server::out($this, $s.
												($i % 2 ? $padding : "\n"), false);
					}
					Server::out($this, "\nSkills:");
					foreach($skills as $s)
					{
						$padding = substr("                          ", strlen($s));
						Server::out($this, $s->getAlias()->getAliasName().
												($i % 2 ? $padding : "\n"), false);
					}
				}
				else if($input[0] == 'add')
				{
					$try_add = Alias::lookup($input[1]);
					
					// Find out if it's a skill or spell
					$look_in = array();
					if($try_add instanceof Skill)
						$look_in = $skills;
					else
						$look_in = $spell_groups;
					
					if(!$look_in)
						Server::out($this, "What is that?");
					else if(in_array($try_add, $look_in))
					{
						$this->unverified_user->getAbilitySet()->addAbility($try_add);
						Server::out($this, "You added ".$try_add->getAlias()->getAliasName().".");
					}
					else
						Server::out($this, "You can't add that.");
				}
				else if($input[0] == 'drop')
				{
					$try_drop = Alias::lookup($input[1]);
					$learned = $this->unverified_user->getAbilitySet()->getLearnedAbility($try_drop);
					if($learned)
					{
						$this->unverified_user->getAbilitySet()->dropAbility($learned);
						return Server::out($this, $learned." dropped.");
					}
					return Server::out($this, "You don't know that.");
				}
				else if($input[0] == 'done')
				{
					$this->login['custom'] = 3;
				}
				
				if($this->login['custom'] !== 3)
				{
					$cp = $this->unverified_user->getAbilitySet()->getCreationPoints();
					Server::out($this, "\n\nYou have ".$cp." creation points, and ".$this->unverified_user->getExperiencePerLevel()." experience per level.");
					return Server::out($this, "What would you like to do (add, list, drop, done)?");
				}
			}
			if(isset($this->login['custom']) && $this->login['custom'] === 2)
			{
				
			}
			/**
			if(isset($this->login['custom']) && $this->login['custom'] === 1)
			{
				if(!isset($this->login['custom_start']))
				{
					$this->login['custom_start'] = 1;
					return $this->customizeList();
				}
			}
			*/
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
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
	}

?>
