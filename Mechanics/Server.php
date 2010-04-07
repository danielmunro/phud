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

	class Server
	{
		
		const ADDRESS = '127.0.0.1';
		const PORT = 9000;
		const TICK_MIN = 30;
		const TICK_MAX = 45;
		
		private $socket = null;
		private $clients = array();
		static $instance = null;
		static $tick = 0;
		
		private function __construct()
		{
			$this->initSocket();
		}
		
		public static function getInstance()
		{
			return self::$instance;
		}
		
		public static function start()
		{
		
			set_time_limit(0);
			
			Debug::addDebugLine("Starting server...");
			self::$instance = new Server();
			Debug::addDebugLine("Initializing environment...");
			self::$instance->initializeEnvironment();
			Debug::addDebugLine("Running main loop...");
			self::$instance->run();
			
			Debug::addDebugLine("Success...");
		
		}
		
		public function run()
		{
		
			$seconds = date('U');
		
			while(!empty(self::$instance))
			{
				$read = array();
				$read[0] = $this->socket;
				
				for($i = 0; $i < sizeof($this->clients); $i ++)
					if($this->clients[$i] instanceof User)
						$read[$i + 1] = $this->clients[$i]->getSocket();
				
				$null = null;
				socket_select($read, $null, $null, 0, 0);
				
				// Add new connection
				if(in_array($this->socket, $read))
				{
					$added = false;
					
					for($i = 0; $i < sizeof($this->clients); $i ++)
						if($this->clients[$i] === null)
						{
							$socket = socket_accept($this->socket);
							$this->clients[$i] = new User($socket);
							$added = $i;
							
							break;
						}
					
					if($added === false)
					{
						$socket = socket_accept($this->socket);
						$this->clients[] = new User($socket);
						$added = sizeof($this->clients) - 1;
					}
					self::out($this->clients[$added], 'Welcome to mud. What is yer name? ', false);
				}
				
				// Tick
				if(date('U') > $seconds + 2)
				{
					ActorObserver::instance()->walk();
					ActorObserver::instance()->battles();
					
					if(!isset(self::$tick))
						self::$tick = date('U') + rand(self::TICK_MIN, self::TICK_MAX);
					
					if(self::$tick < date('U'))
					{
						ActorObserver::instance()->tick();
						foreach(Door::getInstances() as $instance)
							if($instance->decreaseReloadTick() < 1)
								$instance->reload();
						self::$tick = date('U') + rand(self::TICK_MIN, self::TICK_MAX);
					}
					$seconds = date('U');
				}
				
				// Input
				for($i = 0; $i < sizeof($this->clients); $i ++)
				{
					if(!($this->clients[$i] instanceof User))
						continue;
					
					if(in_array($this->clients[$i]->getSocket(), $read))
					{
						$input = strtolower(socket_read($this->clients[$i]->getSocket(), 1024));
						
						// Modify the command buffer appropriately
						if(trim($input) == '~')
							$this->clients[$i]->clearCommandBuffer();
						else
							$this->clients[$i]->addCommandBuffer($input);
					}
					
					// Check for a delay in the user's commands
					if($this->clients[$i]->getDelay())
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
						$command = Command::find('Command_' . ucfirst($args[0]));
						if(!$this->clients[$i]->getLoggedIn())
						{
							$this->clients[$i]->handleLogin($args[0]);
							continue;
						}
						
						if($command instanceof Command)
						{
							$command->perform($this->clients[$i], $args);
							continue;
						}
						
						$doors = Door::findByRoomId($this->clients[$i]->getRoom()->getId());
						$input = trim($input);
						foreach($doors as $door)
							if($door->getHidden() && $door->getHiddenShowCommand() == $input)
							{
								Server::out($this->clients[$i], $door->getHiddenAction());
								$door->setHidden(false);
								continue;
							}
							elseif(!$door->getHidden() && $door->getHiddenShowCommand() == $input)
							{
								Server::out($this->clients[$i], "That is already done.");
								continue;
							}
					}
				}
			}
		}
		
		public function initSocket()
		{
		
			$this->socket = socket_create(AF_INET, SOCK_STREAM, 0);
			if($this->socket === false)
				die('No socket');
			socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
			socket_bind($this->socket, self::ADDRESS, self::PORT) or die('Could not bind to address');
			socket_listen($this->socket);
		
		}
		
		public static function out($client, $message, $break_line = true)
		{
			
			if(!($client instanceof User))
				return;
			
			socket_write($client->getSocket(), $message . ($break_line === true ? "\r\n" : ""));
		
		}
		
		public function initializeEnvironment()
		{
			new Townsperson
			(
				'A town crier',
				'town crier',
				'You see a town crier before you.',
				'temple midgaard',
				3,
				1,
				'human',
				27,
				1
			);
			new Townsperson
			(
				'The zombified remains of the mayor of Midgaard',
				'zombie corpse mayor',
				'The partially decomposed, moaning zombie corpse of the mayor of Midgaard stands before you.',
				'temple midgaard',
				2,
				1,
				'undead',
				14,
				5
			);
			$shopkeep = new ShopkeeperArlen();
		}
		
		public function getSocket()
		{
			return $this->socket;
		}
		
	}

?>
