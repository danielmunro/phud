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
	use \Living\Mob,
		\Mechanics\Event\Event,
		\Mechanics\Event\Broadcaster,
		\Mechanics\Event\Subscriber,
		\Living\User;
	
	class Server
	{
		use Broadcaster;
		
		const ADDRESS = '192.168.0.102';
		const PORT = 9000;
		
		private $socket = null;
		private $clients = [];
		private static $instance = null;
		
		public function __construct()
		{
			self::$instance = $this;
			// initialize important classes/instances like commands and mobs
			Debug::addDebugLine("Initializing environment");
			foreach(
				array(
					'\Mechanics\Command\Command',
					'\Mechanics\Race',
					'\Living\Mob',
					'\Mechanics\Ability\Ability'
				) as $required) {
				Debug::addDebugLine("initializing ".$required);
				$required::runInstantiation();
			}

			// open the socket
			$this->socket = socket_create(AF_INET, SOCK_STREAM, 0);
			if($this->socket === false)
				die('No socket');
			socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
			socket_bind($this->socket, self::ADDRESS, self::PORT) or die('Could not bind to address');
			socket_listen($this->socket);
		}
		
		private function __destruct()
		{
			socket_close($this->socket);
		}

		public static function instance()
		{
			return self::$instance;
		}
		
		public function run()
		{
			$this->addSubscriber(
				new Subscriber(
					Event::EVENT_CONNECTED,
					function($subscriber, $server, $client) {
						$server->addClient($client);
					}
				)
			);
			$this->addSubscriber(
				new Subscriber(
					Event::EVENT_GAME_CYCLE,
					function($subscriber, $server) {
						$server->scanNewConnections();
					}
				)
			);
			$this->addSubscriber(
				new Subscriber(
					Event::EVENT_PULSE,
					function($subscriber, $server) {
						$users = User::getInstances();
						array_walk(
							$users,
							function($u) {
								$target = $u->getTarget();
								if($target) {
									Server::out($u, ucfirst($target).' '.$target->getStatus().".\n");
									Server::out($u, $u->prompt(), false);
								}
							}
						);
					},
					Subscriber::DEFERRED
				)
			);
			$pulse = intval(date('U'));
			$next_tick = $pulse + intval(round(rand(30, 40)));
			while(1) {
				$new_pulse = intval(date('U'));
				if($pulse + 1 === $new_pulse) {
					$this->fire(Event::EVENT_PULSE);
					$pulse = $new_pulse;
				}
				if($pulse === $next_tick) {
					$this->fire(Event::EVENT_TICK);
					$next_tick = $pulse + intval(round(rand(30, 40)));
				}
				$this->fire(Event::EVENT_GAME_CYCLE);
			}
		}

		public function scanNewConnections()
		{
			$n = null;

			// check for new connections
			$s = [$this->socket];
			$new_connection = socket_select($s, $n, $n, 0, 0);
			if($new_connection) {
				$this->fire(Event::EVENT_CONNECTED, new Client(socket_accept($this->socket)));
			}
		}
		
		public static function out($client, $message, $break_line = true)
		{
			if($client instanceof User) {
				$client = $client->getClient();
			}

			if($client instanceof Client) {

				if(!is_resource($client->getSocket())) {
					return false;
				}
				
				$data = $message . ($break_line === true ? "\r\n" : "");
				$bytes_written = socket_write($client->getSocket(), $data, strlen($data));

				if($bytes_written === false) {
					Debug::addDebugLine("Socket write error, client link dead");
					return false;
				}
			}
		}

		public function addClient(Client $client)
		{
			$this->clients[] = $client;
			$this->addSubscriber(
				new Subscriber(
					Event::EVENT_GAME_CYCLE,
					$client,
					function($subscriber, $server, $client) {
						$client->checkCommandBuffer();
						if(!is_resource($client->getSocket())) {
							$subscriber->kill();
						}
					}
				)
			);
		}
		
		public function disconnectClient(Client $client)
		{
			// Take the user out of its room
			$user = $client->getUser();
			if($user && $user->getRoom()) {
				$user->getRoom()->actorRemove($user);
			}
			
			// clean out the client
			socket_close($client->getSocket());
			$key = array_search($client, $this->clients);
			unset($this->clients[$key]);

			// reindex arrays
			$this->clients = array_values($this->clients);
			Debug::addDebugLine($user." disconnected");
		}
		
		public static function chance()
		{
			return rand(0, 10000) / 100;
		}

		public function __toString()
		{
			return self::ADDRESS.':'.self::PORT;
		}
	}
?>
