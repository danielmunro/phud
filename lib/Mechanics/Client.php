<?php

	namespace Mechanics;
	use \Mechanics\Command\Command,
		\Living\User,
		\Mechanics\Ability\Ability,
		\Mechanics\Event\Event,
		\Mechanics\Event\Subscriber;

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
			Server::out($this, 'By what name do you wish to be known? ', false);
		}
		
		public function getUser()
		{
			return $this->user;
		}

		public function getSocket()
		{
			return $this->socket;
		}

		public function getLastInput()
		{
			return $this->last_input;
		}

		public function checkCommandBuffer()
		{
			$n = null;
			
			// Check for input from the socket
			$s = [$this->socket];
			socket_select($s, $n, $n, 0, 0);
			if($s) {
				$input = socket_read($s[0], 5120);
				if($input === '~')
					$this->command_buffer = [];
				else
					$this->command_buffer[] = trim($input);
			}

			// Cases where we don't want to check the buffer, the client has a delay or the command buffer is empty
			if(($this->user && $this->user->getDelay()) || empty($this->command_buffer)) {
				return;
			}

			// Read from the user's command buffer
			$input = array_shift($this->command_buffer);
			if(!empty($input)) {
				// Check a repeat statement
				if(trim($input) === '!')
					$input = $this->last_input;
				else
					$this->last_input = $input;
				
				// Break down client input into separate arguments and evaluate
				$args = explode(' ', trim($input));
				if($this->user) {
					$satisfied = $this->user->fire(Event::EVENT_INPUT, $args);
					if(!$satisfied) {
						Server::out($this, "\nHuh?"); // No subscriber could make sense of input
					}
					Server::out($this, "\n".$this->user->prompt(), false);
				} else {
					$this->userLogin($args);
				}
			}
		}
		
		public function isValidated($password)
		{			
			$pw_hash = sha1($this->unverified_user->getAlias().$this->unverified_user->getDateCreated().$password);
			return $this->unverified_user->getPassword() == $pw_hash;
		}

		protected function initUser(User $user)
		{
			$this->user = $user;
			$this->user->addSubscriber(
				new Subscriber(
					Event::EVENT_INPUT,
					function($subscriber, $user, $args) {
						$command = Command::lookup($args[0]);
						if($command) {
							$command['lookup']->tryPerform($user, $args, $subscriber);
							$subscriber->satisfyBroadcast();
						}
					}
				)
			);
			User::addInstance($this->user);
			$this->user->initActor();
		}

		///////////////////////////////////////////////////////////
		// Login
		///////////////////////////////////////////////////////////

		private function userLogin($args)
		{
			$user_status = $this->handleLogin($args);
			if($user_status === true)
			{
				Server::out($this, "\n".$this->user->prompt(), false);
				Debug::addDebugLine($this->user->getAlias()." logged in");
			}
			else if($user_status === false)
			{
				Server::instance()->disconnectClient($this);
			}
		}

		private function handleLogin($args)
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
					$this->initUser($this->unverified_user);
					$this->user->getRoom()->actorAdd($this->user);
					$this->unverified_user = null;
					$command = Command::lookup('look');
					$command['lookup']->perform($this->user);
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
				
				$race = Race::lookup($this->login['race']);
				if($race)
				{
					$this->unverified_user->setRace($race);
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
					$this->login['align'] = false;
					return Server::out($this, "What is your alignment (good/neutral/evil)? ", false);
				}
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
				$this->login['finish'] = false;
			}

			if(isset($this->login['finish']) && $this->login['finish'] === false)
			{
				$this->initUser($this->unverified_user);
				$this->user->setAlias($this->login['alias']);
				$this->user->addCopper(20);
				$this->user->setPassword(sha1($this->user->getAlias().$this->user->getDateCreated().$this->login['new_pass']));
				$this->user->setRoom(Room::find(Room::START_ROOM));
				$this->user->setClient($this);
				$this->user->save();
				
				$command = Command::lookup('look');
				$command['lookup']->perform($this->user);
				Server::out($this, $this->user->prompt(), false);
			}
		}
		
		private function racesAvailable()
		{
			$races = Race::getAliases();
			Server::out($this, 'The following races are available: ');
			$race_list = '[';
			array_walk(
				$races,
				function($r, $k) use (&$race_list) {
					$race_list .= $k.' ';
				}
			);
			Server::out($this, trim($race_list).']');
			Server::out($this, 'What is your race (help for more information)? ', false);
		}
		
		private function customizeList()
		{
			Server::out($this, "\nThe following skills and groups are available to your character:\n(this list may be seen again by typing list)");
			
		}
	}

?>
