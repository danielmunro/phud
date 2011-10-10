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
		
		const ADDRESS = '192.168.0.111';
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
					$this->handshake($cl);
					self::out($cl, 'By what name do you wish to be known? ', false);
					Debug::addDebugLine("Client connecting");
				}
				
				// Pulse
				$seconds = date('U');
				$next_pulse = Pulse::instance()->getLastPulse() + 1;
				if($seconds == $next_pulse)
					Pulse::instance()->checkPulseEvents($next_pulse);

				// For each socket that is reading input, determine the type of request and attempt to fulfill it
				foreach($read as $socket)
				{
					$key = array_search($socket, $this->sockets);
					$input = trim(socket_read($socket, 5120));
					$json = self::_hybi10DecodeData($input);
					Debug::addDebugLine($json);
					$payload = json_decode($json);
					if(isset($payload->cmd))
					{
						$this->evaluateClientRequest($this->clients[$key], $payload);
					}
					else
					{
						$this->disconnectClient($this->clients[$key]);
					}
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
                                $this->cleanupSocket($cl);
								continue;
							}

							// No command was found -- attempt to perform an ability
							$ability = $cl->getUser()->getAbilitySet()->getSkillByAlias($args[0]);
							if($ability && $ability->isPerformable())
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
			
			self::send($client->getSocket(), ['req' => 'out', 'data' => $message . ($break_line === true ? "\r\n" : "")]);
		}

		private function evaluateClientRequest(Client $cl, $payload)
		{
			switch($payload->cmd) {
				
				// Modifying the user's command buffer
				case 'input':
					if($payload->transport === '~')
						$cl->clearCommandBuffer();
					else
						$cl->addCommandBuffer($payload->transport);
					return;
				
				// Broadcasting new coords of actor to room
				case 'updateCoords':
					$usr = $cl->getUser();
					$usr->setX($payload->x);
					$usr->setY($payload->y);
					$room = $usr->getRoom();
					if($room) {
						array_walk($room->getActors(), function($a) use ($usr) {
							if($a instanceof User && $a != $usr)
								self::send($a->getClient()->getSocket(), ['req' => 'actor', 'data' => $usr]);
						});
					};
					return;
				
				// Initial enter of room/mud -- request all information about the actors in the room
				case 'reqActors':
					$usr = $cl->getUser();
					$room = $usr->getRoom();
					$data = [];
					if($room) {
						$actors = $room->getActors();
						array_walk($actors, function($a) use ($usr, &$data) {
							if($a != $usr)
								$data[$a->getID()] = $a;
						});
						if($data) {
							self::send($cl->getSocket(), ['req' => 'actors', 'data' => $data]);
						}
					}
					return;
			}
		}

		private static function send($socket, $data)
		{
			$json = self::_hybi10EncodeData(json_encode($data));
			socket_write($socket, $json, strlen($json));
		}

        private function cleanupSocket(Client $cl)
        {
            // Clean up clients/sockets
			if(!is_resource($cl->getSocket()))
			{
                $key = array_search($cl, $this->clients);
	    		unset($this->clients[$key]);
				unset($this->sockets[$key]);
				$this->clients = array_values($this->clients);
				$this->sockets = array_values($this->sockets);
			}
        }

		private function userLogin(Client $cl, $args)
		{
			$user_status = $cl->handleLogin($args);
			if($user_status === true)
			{
				$u = $cl->getUser();
				self::send($cl->getSocket(), ['req' => 'loggedIn', 'data' => $u]);
				self::roomPush($u, ['req' => 'actor', 'data' => $u]);
				self::out($cl, "\n".$cl->getUser()->prompt(), false);
				Debug::addDebugLine($cl->getUser()->getAlias()." logged in");
			}
			else if($user_status === false)
			{
				$this->disconnectClient($cl);
			}
		}

		private function roomPush(Actor $actor, $data)
		{
			$actors = $actor->getRoom()->getActors();
			foreach($actors as $a)
				if($a instanceof User && $a != $actor)
					self::send($a->getClient()->getSocket(), $data);
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
			$user = $client->getUser();
			if($user && $user->getRoom()) {
				$user->getRoom()->actorRemove($user);
			}
			socket_close($client->getSocket());
            $this->cleanupSocket($client);
			Debug::addDebugLine("client disconnected");
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
		
		private function handshake(Client $cl)
		{
			$fnSockKey = function($headers) {
				$lines = explode("\r\n", $headers);
    	        foreach($lines as $line)
        	    {
            	    if(strpos($line, 'Sec-WebSocket-Key') === 0)
					{
    	                $ex = explode(": ", $line);
        	            return $ex[1];
            	    }
            	}
			};
			$headers = socket_read($cl->getSocket(), 1024);
            $upgrade = "HTTP/1.1 101 Switching Protocols\r\n" .
						"Upgrade: websocket\r\n" .
                        "Connection: Upgrade\r\n" .
                        "WebSocket-Origin: http://localhost\r\n" .
                        "WebSocket-Location: ws://localhost:9000\r\n" .
                        "Sec-WebSocket-Accept: ".base64_encode(pack('H*', sha1($fnSockKey($headers)."258EAFA5-E914-47DA-95CA-C5AB0DC85B11")))."\r\n\r\n";
        	socket_write($cl->getSocket(), $upgrade, strlen($upgrade));
		}

		private static function _hybi10EncodeData($data)
		{
			$frame = Array();
			$mask = array(rand(0, 255), rand(0, 255), rand(0, 255), rand(0, 255));
			$encodedData = '';
			$frame[0] = 0x81;
			$dataLength = strlen($data);
			
			if($dataLength <= 125)
			{		
				$frame[1] = $dataLength + 128;		
			}
			else
			{
				$frame[1] = 254;  
				$frame[2] = $dataLength >> 8;
				$frame[3] = $dataLength & 0xFF; 
			}	
			$frame = array_merge($frame, $mask);	
			for($i = 0; $i < strlen($data); $i++)
			{		
				$frame[] = ord($data[$i]) ^ $mask[$i % 4];
			}
		 	for($i = 0; $i < sizeof($frame); $i++)
			{
				$encodedData .= chr($frame[$i]);
			}		
		 	return $encodedData;
		}
		
		private static function _hybi10DecodeData($data)
		{		
			$bytes = $data;
			$dataLength = '';
			$mask = '';
			$coded_data = '';
			$decodedData = '';
			$secondByte = sprintf('%08b', ord($bytes[1]));		
			$dataLength = ord($bytes[1]) & 127;
			if($dataLength === 126)
			{
			   $mask = substr($bytes, 4, 4);
	   		   $coded_data = substr($bytes, 8);
	   		}
			elseif($dataLength === 127)
			{
				$mask = substr($bytes, 10, 4);
				$coded_data = substr($bytes, 14);
			}
			else
			{
				$mask = substr($bytes, 2, 4);		
				$coded_data = substr($bytes, 6);		
			}	
			for($i = 0; $i < strlen($coded_data); $i++)
			{		
				$decodedData .= $coded_data[$i] ^ $mask[$i % 4];
			}

			// HACK/BUGFIX -- for some reason the last char gets chopped off
			// sometimes. Manually add it back for now
			if(strlen($decodedData) && substr($decodedData, -1) !== "}")
				$decodedData .= "}";

		 	return $decodedData;
		 }

	}
?>
