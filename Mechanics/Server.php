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
	use \Living\Mob;
	use \Living\User;
	class Server
	{
		
		const ADDRESS = '127.0.0.1';
		const PORT = 9000;
		
		private $socket = null;
		private $sockets = array();
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
			if(self::$instance)
				return;
			self::$instance = new Server();
			self::$instance->run();
			Debug::addDebugLine("Success...");
		}
		
		private function initEnvironment()
		{
			Debug::addDebugLine("Calling initEnvironment() on game components...");
			$req_init = array(
							'\Mechanics\Command',
							'\Mechanics\Race',
							'\Mechanics\Discipline',
							'\Living\Mob'
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
				$read = array_merge($this->sockets, array($this->socket));
				$null = null;
				socket_select($read, $null, $null, 0, 0);
				
				// Add new connection
				$key = array_search($this->socket, $read);
				if($key !== false)
				{
					$cl = new Client(socket_accept($this->socket));
					$this->clients[] = $cl;
					$this->sockets[] = $cl->getSocket();
					unset($read[$key]);
					self::out($cl, 'By what name do you wish to be known? ', false);
				}
				
				// Pulse
				$seconds = date('U');
				$next_pulse = Pulse::instance()->getLastPulse() + 1;
				if($seconds == $next_pulse)
					Pulse::instance()->checkPulseEvents($next_pulse);

				// For each socket that is reading input, modify the corresponding client's command buffer
				foreach($read as $socket)
				{
					$key = array_search($socket, $this->sockets);
					$input = trim(socket_read($socket, 1024));
					if($input === '~')
						$this->clients[$key]->clearCommandBuffer();
					else
						$this->clients[$key]->addCommandBuffer($input);
				}
				
				// Input
				foreach($this->clients as $k => $cl)
				{		
					// Check for a delay in the user's commands
					if($cl->getUser() && $cl->getUser()->getDelay())
						continue;
					
					// There's no delay, get the oldest command in the buffer and evaluate
					$input = $cl->shiftCommandBuffer();
					if(!empty($input))
					{
						// Check a repeat statement
						if(trim($input) === '!')
							$input = $cl->getLastInput();
						else
							$cl->setLastInput($input);
						
						// Break down user input into separate arguments
						$args = explode(' ', trim($input));
						
						// By now if the client does not have a user object it is because they are in the process of logging in
						if(!$cl->getUser())
						{
							$this->userLogin($cl, $args);
						}
						else
						{
							// Evaluate user input for a command
							$command = Alias::lookup($args[0]);
							if($command instanceof Command)
							{
								$command->tryPerform($cl->getUser(), $args);
								self::out($cl, "\n".$cl->getUser()->prompt(), false);
								continue;
							}

							// No command was found -- attempt to perform an ability
							$ability = $cl->getAbilitySet()->getAbilityByAlias($args[0]);
							if($ability instanceof Ability && $ability->isPerformable())
							{
								$ability->perform($args);
								self::out($cl, "\n".$cl->getUser()->prompt(), false);
								continue;
							}
						
							// Not sure what the user was trying to do
							self::out($cl, "\nHuh?");
							self::out($cl, "\n" . $cl->getUser()->prompt(), false);
						}
					}

					// Clean up clients/sockets
					if(!$cl->getSocket())
					{
						unset($this->clients[$k]);
						unset($this->sockets[$k]);
						$this->clients = array_values($this->clients);
						$this->sockets = array_values($this->sockets);
					}
				}
			}
			Debug::addDebugLine('done');
		}
		
		public static function out($client, $message, $break_line = true)
		{
			if($client instanceof Mob)
				return Debug::addDebugLine($client->getAlias(true).': '.$message);
			
			if($client instanceof User)
				$client = $client->getClient();
			
			if(!($client instanceof Client) || is_null($client->getSocket()))
				return;
			
			socket_write($client->getSocket(), $message . ($break_line === true ? "\r\n" : ""));
		
		}

		private function userLogin(Client $cl, $args)
		{
			$user_status = $cl->handleLogin($args);
			if($user_status === true)
				Server::out($cl, "\n".$cl->getUser()->prompt(), false);
			else if($user_status === false)
				$this->disconnectClient($cl);
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
