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
		const TICK_MIN = 50;
		const TICK_MAX = 50;
		const TICK = 50;
		const PULSES_PER_TICK = 25;
		
		private $socket = null;
		private $clients = array();
		static $instance = null;
		static $tick = 0;
		private static $last_pulse;
		
		private function __construct() { $this->openSocket(); }
		private function __destruct() { $this->closeSocket($this->socket); }
		
		public static function start()
		{
		
			Debug::addDebugLine("Initializing environment...");
			\Mechanics\Command::runInstantiation();
			\Mechanics\Area::runInstantiation();
			
			Debug::addDebugLine("Starting server...");
			self::$instance = new Server();
			
			Debug::addDebugLine("Running main loop...");
			self::$instance->run();
			
			Debug::addDebugLine("Success...");
		
		}
		
		public function run()
		{
		
			$seconds = date('U');
		
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
							$this->clients[$i] = new \Living\User($socket);
							$added = $i;
							break;
						}
					
					if($added === false)
					{
						$socket = socket_accept($this->socket);
						$this->clients[] = $user = new \Living\User($socket);
						$added = array_search($user, $this->clients);
					}
					self::out($this->clients[$added], 'Welcome to mud. What is yer name? ', false);
				}
				
				// Pulse
				if(date('U') == self::$last_pulse + 2)
				{
					ActorObserver::instance()->checkPulseEvents();
					
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
					self::$last_pulse = date('U');
				}
				
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
						
						if(!$this->clients[$i]->getAlias())
						{
							$this->clients[$i]->handleLogin($args[0]);
							continue;
						}
						
						try
						{
							$command = Command::find($args[0]);
						}
						catch(\Exceptions\Command $e)
						{
						
							// Skills -- See the cast command for spells
							if($skill = $this->clients[$i]->getAbilitySet()->isValidSkill($args[0]))
							{
								$n = null;
								$skill->perform($this->clients[$i], $n, $args);
								self::out($this->clients[$i], "\n" . $this->clients[$i]->prompt(), false);
								continue;
							}
							
							$doors = Door::findByRoomId($this->clients[$i]->getRoom()->getId());
							$input = trim($input);
							foreach($doors as $door)
								if($door->getHidden() && $door->getHiddenShowCommand() == $input)
								{
									self::out($this->clients[$i], $door->getHiddenAction());
									$door->setHidden(false);
									continue;
								}
								elseif(!$door->getHidden() && $door->getHiddenShowCommand() == $input)
								{
									self::out($this->clients[$i], "That is already done.");
									continue;
								}
							self::out($this->clients[$i], "What was that?");
							continue;
						}
						
						// Perform command
						$command->perform($this->clients[$i], $args);
						if(isset($this->clients[$i]))
							self::out($this->clients[$i], "\n" . $this->clients[$i]->prompt(), false);
					}
				}
			}
			Debug::addDebugLine('done');
		}
		
		public static function out($client, $message, $break_line = true)
		{
			
			if(!($client instanceof \Living\User) || is_null($client->getSocket()))
				return;
			
			socket_write($client->getSocket(), $message . ($break_line === true ? "\r\n" : ""));
		
		}
		public static function randomizePulse($pulse, $mod = 0.5)
		{
		
			$low_mod = $mod * $pulse;
			$high_mod = $pulse + $low_mod;
			return rand($low_mod, $high_mod);
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
		private function closeSocket($socket) { socket_close($socket); }
		public function disconnectUser(\Living\User $user) { $this->closeSocket($user->getSocket()); }
		
		public static function getLastPulse()
		{
			
			if(!self::$last_pulse)
				self::$last_pulse = date('U');
			return self::$last_pulse;
		}
		public static function getInstance() { return self::$instance; }
		public function getCommandFromClass($class) { return strtolower(str_replace('_', ' ', $class)); }
		public function getSocket() { return $this->socket; }
	}
?>
