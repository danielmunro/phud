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
	namespace Mechanics;
	class Server
	{
		
		const ADDRESS = '127.0.0.1';
		const PORT = 9000;
		
		private $socket = null;
		private $clients = array();
		static $instance = null;
		
		private function __construct()
		{
			$this->initEnvironment();
			$this->openSocket();
		}
		
		private function __destruct()
		{
			socket_close($this->socket);
		}
		
		public static function start()
		{
			self::$instance = new Server();
			self::$instance->run();
			Debug::addDebugLine("Success...");
		}
		
		private function initEnvironment()
		{
			Debug::addDebugLine("Calling initEnvironment() on game components...");
			$req_init = array(
							'\Mechanics\Ability',
							'\Mechanics\Command',
							'\Mechanics\Race',
							'\Mechanics\Discipline',
							'\Living\Mob',
							//'Shopkeeper'
						);
			foreach($req_init as $required)
			{
				Debug::addDebugLine("initEnvironment() ".$required);
				$required::runInstantiation();
			}
		}
		
		public function run()
		{
			while(1)
			{
				$read = array($this->socket);
				
				foreach($this->clients as $i => $client)
					if(isset($this->clients[$i]) && $this->clients[$i]->getSocket())
						$read[$i + 1] = $this->clients[$i]->getSocket();
				
				$null = null;
				socket_select($read, $null, $null, 0, 0);
				
				// Add new connection
				if(in_array($this->socket, $read))
				{
					$added = false;
					for($i = 0; $i < sizeof($this->clients); $i ++)
						if(!isset($this->clients[$i]))
						{
							$socket = socket_accept($this->socket);
							$this->clients[$i] = new Client($socket);
							$added = $i;
							break;
						}
					
					if($added === false)
					{
						$socket = socket_accept($this->socket);
						$this->clients[] = new Client($socket);
						$added = sizeof($this->clients)-1;
					}
					self::out($this->clients[$added], 'By what name do you wish to be known? ', false);
				}
				
				// Pulse
				$seconds = date('U');
				$next_pulse = Pulse::instance()->getLastPulse() + 1;
				if($seconds == $next_pulse)
					Pulse::instance()->checkEvents($next_pulse);
				
				// Input
				foreach($this->clients as $i => $client)
				{
					if(!isset($this->clients[$i]))
						continue;
					
					if(in_array($this->clients[$i]->getSocket(), $read))
					{
						$input = socket_read($this->clients[$i]->getSocket(), 1024);
						if(strpos($input, 'room ') !== 0)
							$input = trim($input);
						
						// Modify the command buffer appropriately
						if(trim($input) == '~')
							$this->clients[$i]->clearCommandBuffer();
						else
							$this->clients[$i]->addCommandBuffer($input);
					}
					
					// Check for a delay in the user's commands
					if($this->clients[$i]->getUser() && $this->clients[$i]->getUser()->getDelay())
						continue;
					
					$input = $this->clients[$i]->shiftCommandBuffer();
					
					if(!empty($input))
					{
						// Check a repeat statement
						if(trim($input) == '!')
							$input = $this->clients[$i]->getLastInput();
						else
							$this->clients[$i]->setLastInput($input);
						
						$args = explode(' ', trim($input));
						
						if(!$this->clients[$i]->getUser())
						{
							$logged = $this->clients[$i]->handleLogin($args);
							if($logged === false)
								unset($this->clients[$i]);
							else if($logged === true)
								Server::out($this->clients[$i], "\n".$this->clients[$i]->getUser()->prompt(), false);
							continue;
						}
						
						$alias = Alias::lookup($args[0]);
						if($alias instanceof Command)
						{
							if(!sizeof($alias->getDispositions()) || in_array($this->clients[$i]->getUser()->getDisposition(), $alias->getDispositions()))
							{
								// Perform command
								$alias->perform($this->clients[$i]->getUser(), $args);
								self::out($this->clients[$i], "\n" . $this->clients[$i]->getUser()->prompt(), false);
							}
							else if($this->clients[$i]->getUser()->getDisposition() === Actor::DISPOSITION_SITTING)
								self::out($this->clients[$i], "You need to stand up.");
							else if($this->clients[$i]->getUser()->getDisposition() === Actor::DISPOSITION_SLEEPING)
								self::out($this->clients[$i], "You are asleep!");
						}
						else if($alias instanceof Ability)
						{
							self::out($this->clients[$i], $this->clients[$i]->getUser()->perform($alias, $args));
							self::out($this->clients[$i], "\n" . $this->clients[$i]->prompt(), false);
							continue;
						}
						
						/**
						$command = Command::find($args[0]);
						if($command)
						{
							if(!sizeof($command::getDispositions()) || in_array($this->clients[$i]->getDisposition(), $command::getDispositions()))
							{
								// Perform command
								$command->perform($this->clients[$i], $args);
								if(isset($this->clients[$i]))
									self::out($this->clients[$i], "\n" . $this->clients[$i]->prompt(), false);
							}
							else if($this->clients[$i]->getDisposition() === Actor::DISPOSITION_SITTING)
								self::out($this->clients[$i], "You need to stand up.");
							else if($this->clients[$i]->getDisposition() === Actor::DISPOSITION_SLEEPING)
								self::out($this->clients[$i], "You are asleep!");
						}
						else
						{
						
							// Skills and spells
							$ability = Ability::lookup($args[0]);
							if($ability)
							{
								self::out($this->clients[$i], $this->clients[$i]->perform($ability, $args));
								self::out($this->clients[$i], "\n" . $this->clients[$i]->prompt(), false);
								continue;
							}
							
							$doors = Door::findByRoomId($this->clients[$i]->getRoom()->getId());
							$input = trim($input);
							$bail = false;
							foreach($doors as $door)
								if($door->getHidden() && $door->getHiddenShowCommand() == $input)
								{
									self::out($this->clients[$i], $door->getHiddenAction());
									$door->setHidden(false);
									$bail = true;
									continue;
								}
								elseif(!$door->getHidden() && $door->getHiddenShowCommand() == $input)
								{
									self::out($this->clients[$i], "That is already done.");
									continue;
								}
							if(!$bail)
							{
								self::out($this->clients[$i], "What was that?");
								continue;
							}
						}
						*/
					}
				}
			}
			Debug::addDebugLine('done');
		}
		
		public static function out($client, $message, $break_line = true)
		{
			if($client instanceof \Living\Mob)
				return Debug::addDebugLine($client->getAlias(true).': '.$message);
			
			if($client instanceof \Living\User)
				$client = $client->getClient();
			
			if(!($client instanceof Client) || is_null($client->getSocket()))
				return;
			
			socket_write($client->getSocket(), $message . ($break_line === true ? "\r\n" : ""));
		
		}
		
		private function openSocket()
		{
		
			$this->socket = socket_create(AF_INET, SOCK_STREAM, 0);
			if($this->socket === false)
				die('No socket');
			socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
			socket_bind($this->socket, self::ADDRESS, self::PORT) or die('Could not bind to address');
			socket_listen($this->socket);
		
		}
		
		public function disconnectClient(Client $client)
		{
			socket_close($client->getSocket());
		}
		
		public static function getInstance()
		{
			return self::$instance;
		}
		
		public function getSocket()
		{
			return $this->socket;
		}
		
		public static function chance()
		{
			return rand(0, 10000) / 100;
		}
	}
?>
