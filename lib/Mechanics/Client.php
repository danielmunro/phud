<?php

	namespace Mechanics;
	class Client
	{
		private $user = null;
		private $unverified_user = null;
		private $socket = null;
		private $command_buffer = array();
		private $login = array('alias' => false);
		private $api_methods = array('input', 'updateCoords', 'reqRoom', 'reqImages', 'moveX', 'moveY');
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
		// Client requests
		///////////////////////////////////////////////////////////
		public function evaluateRequest($payload)
		{
			$method = $payload->cmd;
			if(in_array($method, $this->api_methods)) {
				return $this->$method($payload);	
			}
			Debug::addDebugLine("Invalid request from client: ".print_r($payload, true));
		}

		private function input($payload)
		{
			if($payload->transport === '~')
				$this->clearCommandBuffer();
			else
				$this->addCommandBuffer($payload->transport);
		}

		/**
		private function updateCoords($payload)
		{
			$usr = $this->getUser();
			$usr->setX($payload->x);
			$usr->setY($payload->y);
			Server::roomPush($usr, ['req' => 'room.actor', 'data' => $usr]);
		}
		*/

		private function moveX($payload)
		{
			$u = $this->user;
			$new_x = $u->getX() + $payload->x;
			$collision = $u->getRoom()->detectCollision($u->getImage('walking', 'resource'), $new_x, $u->getY());
			Debug::addDebugLine("collision: ".print_r($collision, true));
			if(!$collision)
			{
				Debug::addDebugLine("moving x");
				$u->setX($new_x);
				Server::roomPush($u, ['req' => 'room.actor', 'data' => $u]);
				return ['req' => 'user.moveX', 'data' => $payload->x];
			}
		}

		private function moveY($payload)
		{
			$u = $this->user;
			$new_y = $u->getY() + $payload->y;
			$collision = $u->getRoom()->detectCollision($u->getImage('walking', 'resource'), $u->getX(), $new_y);
			Debug::addDebugLine("collision: ".print_r($collision, true));
			if(!$collision)
			{
				Debug::addDebugLine("moving y");
				$u->setY($u->getY() + $payload->y);
				Server::roomPush($u, ['req' => 'room.actor', 'data' => $u]);
				return ['req' => 'user.moveY', 'data' => $payload->y];
			}
		}

		private function reqRoom()
		{
			return ['req' => 'room.load', 'data' => $this->user->getRoom()];
		}

		private function reqImages()
		{
			return ['req' => 'user.images', 'data' => $this->user->getImagesSrc()];
		}
	
		///////////////////////////////////////////////////////////
		// Login
		///////////////////////////////////////////////////////////
		public function handleLogin($args)
		{
			$input = array_shift($args);
			if($this->login['alias'] === false)
			{
			
				if(!\Living\User::validateAlias($input))
					return Server::out($this, "That is not a valid name. What IS your name?");
			
				$this->login['alias'] = $input;
				
				$db = Dbr::instance();
				$this->unverified_user = unserialize($db->get($input));
				
				if(!empty($this->unverified_user))
				{
					$this->unverified_user->setClient($this);
					Server::out($this, 'Password: ', false);
					$this->login['pass'] = false;
				}
				else
				{
					Server::out($this, 'Did I get that right, ' . $this->login['alias'] . '? (y/n) ', false);
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
					$this->user->getRoom()->actorAdd($this->user);
					$this->unverified_user = null;
					$look = Alias::lookup('look');
					$look->perform($this->user);
					return true;
				}
				else
				{
					Server::out($this, 'Wrong password.');
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
						Server::out($this, "New character.");
						return Server::out($this, "Give me a password for ".$this->login['alias'].": ", false);
					case 'n':
					case 'no':
						$this->login = array('alias' => false);
						return Server::out($this, "Ok, what IS it then? ", false);
					default:
						return Server::out($this, 'Please type Yes or No: ', false);
				}
			}
			
			if(isset($this->login['new_pass']) && $this->login['new_pass'] === false)
			{
				$this->login['new_pass'] = $input;
				$this->login['new_pass_2'] = false;
				return Server::out($this, 'Please retype password: ', false);
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
					Server::out($this, "Passwords don't match.");
					Server::out($this, "Retype password: ", false);
				}
				return;
			}
			
			if(isset($this->login['race']) && $this->login['race'] === false)
			{
				$this->login['race'] = $input;
				
				$race = Alias::lookup($this->login['race']);
				if($race instanceof Race)
				{
					$this->unverified_user->setRace($this->login['race']);
					$this->unverified_user->setAttributesFromRace();
				}
				else
				{
					Server::out($this, "That's not a valid race.");
					$this->racesAvailable();
					$this->login['race'] = false;
					return;
				}
				
				Server::out($this, "What is your sex (m/f)? ", false);
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
					return Server::out($this, "That's not a sex.\nWhat IS your sex? ", false);
				
				if($this->login['sex'])
				{
					$this->unverified_user->setSex($this->login['sex']);
					Server::out($this, "Select a primary discipline [warrior cleric thief mage]: ", false);
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
					Server::out($this, "Ok.\nSelect a discipline focus [".implode(" ", $focuses)."]: ", false);
				}
				else
					Server::out($this, "That's not a primary discipline. What IS your primary discipline? ", false);
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
					Server::out($this, "\nYou may be good, neutral, or evil.\nWhich alignment (g/n/e)? ", false);
				}
				else
					Server::out($this, "That's not a discipline focus. What IS your discipline focus? ", false);
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
					return Server::out($this, "That's not a valid alignment.\nWhich alignment (g/n/e)? ", false);
				
				$this->unverified_user->setAlignment($this->login['align']);
				$this->login['custom'] = 0;
				Server::out($this, "Skill and spell customization. Choose from the list below of skills and spells:");
			}
			if(isset($this->login['custom']) && ($this->login['custom'] === 0 || $this->login['custom'] === 1))
			{
				if($input == 'list' || $this->login['custom'] === 0)
				{
					$this->login['custom'] = 1;
					$this->abilityList();
				}
				else if($input == 'add')
				{
					// Set up some temp variables
					$dp = $this->unverified_user->getDisciplinePrimary()->getAbilitySet();
					$df = $this->unverified_user->getDisciplineFocus()->getAbilitySet();
					$input_ability = implode(' ', $args);

					// Check if the skill or spell group is already learned
					if($this->unverified_user->getAbilitySet()->getSkillByInput($input_ability) ||
						$this->unverified_user->getAbilitySet()->getSpellGroupByInput($input_ability))
						return Server::out($this, "You already know that.");

					// Try to add it from the pool of available abilities
					$skill = $dp->getSkillByInput($input_ability);
                    $spell_group = null;
                    if(!$skill)
                        $skill = $df->getSkillByInput($input_ability);
                    if($skill)
					{
						$this->unverified_user->getAbilitySet()->addSkill($skill);
                        Server::out($this, "You added ".$skill::getAlias().".");
					}
                    else
                    {
                        $spell_group = $dp->getSpellGroupByInput($input_ability);
                        if(!$spell_group)
                            $spell_group = $df->getSpellGroupByInput($input_ability);
                        if($spell_group)
                        {
						    $this->unverified_user->getAbilitySet()->addSpellGroup($spell_group);
                            Server::out($this, "You added ".$spell_group::getAlias().".");
                        }
					}
                    if(!$skill && !$spell_group)
						Server::out($this, "You can't add that.");
				}
				else if($input == 'drop')
				{
					// Set up some temp variables
					$set = $this->unverified_user->getAbilitySet();
					$input_ability = implode(' ', $args);

					// Try to remove it
					$ability = null;
					if($ability = $set->getSkillByInput($input_ability))
						$set->removeSkill($ability);
					else if($ability = $set->getSpellGroupByInput($input_ability))
						$set->removeSpellGroup($ability);

					if($ability)
						Server::out($this, "You remove ".$ability::getAlias().".");
					else
						Server::out($this, "You don't know that.");
				}
				else if($input == 'learned')
				{
					$this->abilityList();
				}
				else if($input == 'done')
				{
					$this->login['custom'] = 3;
					$this->unverified_user->setExperiencePerLevel();
					Server::out($this, "Now let's figure out your attributes...");
					$this->login['attr'] = 0;
					$this->login['attr_mod'] = array('str' => 0, 'int' => 0, 'wis' => 0, 'dex' => 0, 'con' => 0, 'cha' => 0);
					Server::out($this, "You have " . (10 - $this->login['attr']) . " points left to distribute to your attributes.");
					Server::out($this,
						'Str ' . $this->unverified_user->getStr() . ' Int ' . $this->unverified_user->getInt() . ' Wis ' . $this->unverified_user->getWis() . 
						' Dex ' . $this->unverified_user->getDex() . ' Con ' . $this->unverified_user->getCon() . ' Cha ' . $this->unverified_user->getCha());
					Server::out($this, "Add a point to: ", false);
				}
				
				if($this->login['custom'] !== 3)
				{
					$cp = $this->unverified_user->getCreationPoints();
					Server::out($this, "\nYou have ".$cp." creation points, and ".$this->unverified_user->getExperiencePerLevelFromCP()." experience per level.");
					return Server::out($this, "What would you like to do (add, list, drop, done)?");
				}
			}
			if(isset($this->login['attr']) && is_numeric($this->login['attr']))
			{
				
				$method_get = 'get'.ucfirst($input);
				$method_set = 'set'.ucfirst($input);
				$method_get_max = 'getMax'.ucfirst($input);
				
				if(!method_exists($this->unverified_user, $method_get) || !method_exists($this->unverified_user, $method_set))
					return Server::out($this, 'Which attribute is that? ', false);
				
				if($this->login['attr'] + $this->login['attr_mod'][$input] + 1 > 10)
				{
					Server::out($this, "You don't have enough points to do that.");
				}
				else if($this->unverified_user->$method_get() < $this->unverified_user->$method_get_max())
				{
					$this->unverified_user->$method_set($this->unverified_user->$method_get() + 1);
					$this->login['attr'] += $this->login['attr_mod'][$input] + 1;
					$this->login['attr_mod'][$input]++;
				}
				else
					Server::out($this, 'This attribute is maxed!');
				
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
					Server::out($this, "You have " . (10 - $this->login['attr']) . " points left to distribute to your attributes.");
					Server::out($this,
						'Str ' . $this->unverified_user->getStr() . ' Int ' . $this->unverified_user->getInt() . ' Wis ' . $this->unverified_user->getWis() . 
						' Dex ' . $this->unverified_user->getDex() . ' Con ' . $this->unverified_user->getCon() . ' Cha ' . $this->unverified_user->getCha());
					Server::out($this, "Add a point to: ", false);
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
				$this->user->addCopper(20);
				$this->user->setPassword(sha1($this->user->getAlias().$this->user->getDateCreated().$this->login['new_pass']));
				$this->user->setRoom(Room::find(Room::START_ROOM));
				$this->user->setClient($this);
				$this->user->save();
				
				$look = Alias::lookup('look');
				$look->perform($this->user);
				Server::out($this, $this->user->prompt(), false);
			}
		}
		
		private function abilityList()
		{
			Server::out($this, "\nspell groups:");
			$spell_groups = array_merge(
						$this->unverified_user->getDisciplinePrimary()->getAbilitySet()->getSpellGroups(),
						$this->unverified_user->getDisciplineFocus()->getAbilitySet()->getSpellGroups()
					);
			foreach($spell_groups as $spell_group)
			{
				if(!$this->unverified_user->getAbilitySet()->getSpellGroupByAlias($spell_group::getAlias()))
				{
					$padding = substr("                          ", strlen($spell_group::getAlias()));
					Server::out($this, $spell_group::getAlias().$padding.$spell_group::getCreationPoints()."cp");
				}
			}
			Server::out($this, "\nskills:");
			$skills = array_merge(
						$this->unverified_user->getDisciplinePrimary()->getAbilitySet()->getSkills(),
						$this->unverified_user->getDisciplineFocus()->getAbilitySet()->getSkills()
					);
			foreach($skills as $skill)
			{
				if(!$this->unverified_user->getAbilitySet()->getSkillByAlias($skill::getAlias()))
				{
					$padding = substr("                          ", strlen($skill::getAlias()));
					Server::out($this, $skill::getAlias().$padding.$skill::getCreationPoints()."cp");
				}
			}
		}
		
		private function racesAvailable()
		{
			Server::out($this, 'The following races are available: ');
			Server::out($this, '[human undead faerie elf ogre] ');
			Server::out($this, 'What is your race (help for more information)? ', false);
		}
		
		private function customizeList()
		{
			Server::out($this, "\nThe following skills and groups are available to your character:\n(this list may be seen again by typing list)");
			
		}
	}

?>
