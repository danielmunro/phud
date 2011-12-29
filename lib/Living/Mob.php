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
	namespace Living;
	use \Mechanics\Dbr,
		\Mechanics\Room,
		\Mechanics\Debug,
		\Mechanics\Alias,
		\Mechanics\Fighter,
		\Mechanics\Server,
		\Mechanics\Event\Subscriber,
		\Mechanics\Event\Event,
		\Mechanics\Command\Command,
		\Mechanics\Persistable;

	class Mob extends Fighter
	{
		protected $movement_pulses = 100;
		protected $movement_pulses_timeout = 100;
		protected $respawn_ticks = 5;
		protected $respawn_ticks_timeout = 5;
		protected $auto_flee = false;
		protected $unique = false;
		protected $default_respawn_ticks = 1;
		protected $dead = false;
		protected $start_room_id = 0;
		protected $area = '';
		protected $gold_repop = 0;
		protected $silver_repop = 0;
		protected $copper_repop = 0;
		protected $alias = 'a generic mob';
		protected $nouns = 'generic mob';
		protected $path = [];
		protected $is_recording_path = false;
		protected $path_index = -1;
		protected $last_path_index = -2;
		
		const FLEE_PERCENT = 10;
		
		public function __construct()
		{
			parent::__construct();
			Server::instance()->addSubscriber($this->getMovementSubscriber());
		}
		
		public static function runInstantiation()
		{
			$db = Dbr::instance();
			$mob_ids = $db->sMembers('mobs');
			$server = Server::instance();
			foreach($mob_ids as $mob_id)
			{
				$mob = unserialize($db->get($mob_id));
				$mob->initActor();
				$mob->getRoom()->actorAdd($mob);
				$server->addSubscriber($mob->getMovementSubscriber());
			}
		}
		
		public function save()
		{
			$subscribers = $this->subscribers;
			$this->subscribers = null;
			parent::save();
			$this->subscribers = $subscribers;
			$dbr = Dbr::instance();
			$dbr->sAdd('mobs', $this->id);
		}

		public function delete()
		{
			$db = Dbr::instance();
			$db->del($this->id);
			$db->sRem('mobs', $this->id);
			$this->getRoom()->actorRemove($this);
		}

		public function getPath()
		{
			return $this->path;
		}

		public function isRecordingPath($toggle = null)
		{
			if($toggle === null) {
				return $this->is_recording_path;
			} else if(is_bool($toggle)) {
				$this->is_recording_path = $toggle;
			}
		}

		public function addPath($input)
		{
			$this->path[] = $input;
		}

		public function resetPath()
		{
			$this->path = [];
		}

		public function getMovementSubscriber()
		{
			return new Subscriber(
				Event::EVENT_PULSE,
				$this,
				function($subscriber, $broadcaster, $mob) {
					$mob->evaluateMove();
					if($mob->getMovementPulses() === 0) {
						$subscriber->kill();
					}
				}
			);
		}

		public function evaluateMove()
		{
			$this->movement_pulses_timeout--;
			if($this->movement_pulses_timeout < 0) {
				$min = $this->movement_pulses * 0.9;
				$max = $this->movement_pulses * 1.1;
				$this->movement_pulses_timeout = round(rand($min, $max));
				$this->move();
			}
		}
		
		public function move()
		{
			if($this->getRoom()->getId() === Room::PURGATORY_ROOM_ID)
			{
				return;
			}

			$directions = array(
							'north' => $this->getRoom()->getNorth(),
							'south' => $this->getRoom()->getSouth(),
							'east' => $this->getRoom()->getEast(),
							'west' => $this->getRoom()->getWest(),
							'up' => $this->getRoom()->getUp(),
							'down' => $this->getRoom()->getDown());

			if($this->path) {
				if($this->path_index > $this->last_path_index) {
					$this->path_index++;
					$this->last_path_index++;
					if($this->path_index > sizeof($this->path)-1) {
						$this->path_index = sizeof($this->path)-1;
						$this->last_path_index = sizeof($this->path);
						$direction = Room::getReverseDirection($this->path[$this->path_index]);
					} else {
						$direction = $this->path[$this->path_index];
					}
				} else {
					$this->path_index--;
					$this->last_path_index--;
					if($this->path_index < 0) {
						$this->path_index = 0;
						$this->last_path_index = -1;
						$direction = $this->path[$this->path_index];
					} else {
						$direction = Room::getReverseDirection($this->path[$this->path_index]);
					}
				}
				Debug::addDebugLine($this.' is moving, path index: '.$this->path_index.', direction: '.$direction);
				foreach($directions as $alias => $d) {
					if(strpos($alias, $direction) === 0) {
						$directions = [$direction => $d];
					}
				}
			} else {
				$direction = rand(0, sizeof($directions)-1);
				$directions = array_filter(
										$directions,
										function($d)
										{
											return $d !== -1;
										}
									);
				uasort(
					$directions,
					function($i)
					{
						return rand(0, 1);
					}
				);
			}
			$areas = explode(' ', $this->area);
			foreach($directions as $dir => $room_id)
			{
				$other_areas = explode(' ', Room::find($room_id)->getArea());
				$intersection = array_intersect($areas, $other_areas);
				if($intersection)
				{
					$command = Command::lookup($dir);
					$command['lookup']->perform($this);
					return;
				}
			}
		}
		
		public function handleDeath()
		{
			parent::handleDeath();
			$this->setHp(-1);
			$this->setRoom(Room::find(Room::PURGATORY_ROOM_ID));
			$this->respawn_ticks_timeout = round(rand($this->respawn_ticks - 2, $this->respawn_ticks + 2));
			Server::instance()->addSubscriber(
				new Subscriber(
					Event::EVENT_TICK,
					$this,
					function($subscriber, $server, $mob) {
						$mob->evaluateRespawn();
						if($mob->isAlive()) {
							$subscriber->kill();
						}
					}
				)
			);
		}

		public function evaluateRespawn()
		{
			$this->respawn_ticks_timeout--;
			if($this->respawn_ticks_timeout < 0) {
				$this->setHp($this->getMaxHp());
				$this->setMana($this->getMaxMana());
				$this->setMovement($this->getMaxMovement());
				$this->setRoom($this->getStartRoom());
				$this->getRoom()->announce($this, ucfirst($this).' arrives in a puff of smoke.');
			}
		}
		
		public function getExperiencePerLevel()
		{
			return $this->getExperiencePerLevelFromCP();
		}
		
		public function getStartRoom()
		{
			return Room::find($this->start_room_id);
		}
		
		public function setStartRoom()
		{
			$this->start_room_id = $this->room_id;
		}
		
		public function getMovementPulses()
		{
			return $this->movement_pulses;
		}

		public function setMovementPulses($pulses)
		{
			$this->movement_pulses = intval($pulses);
		}

		public function getRespawnTicks()
		{
			return $this->respawn_ticks;
		}
		
		public function getNouns()
		{
			return $this->nouns;
		}
		
		public function setNouns($nouns)
		{
			$this->nouns = trim($nouns);
		}
		
		public function getDead() { return $this->dead; }
		public function setDead($dead) { $this->dead = $dead; }
	
		public function getAutoFlee()
		{
			return $this->auto_flee;
		}
		
		public function setAutoFlee($auto_flee)
		{
			$this->auto_flee = $auto_flee;
		}
	
		public function isUnique()
		{
			return $this->unique;
		}
		
		public function setUnique($unique)
		{
			$this->unique = $unique;
		}
		
		public function getArea()
		{
			return $this->area;
		}
		
		public function setArea($area)
		{
			$this->area = $area;
		}
		
		public static function validateAlias($alias)
		{
			return preg_match('/^[A-Za-z ]{2,100}$/i', $alias);
		}
		
		///////////////////////////////////////////////////////////////////////////
		// Money stuff
		///////////////////////////////////////////////////////////////////////////
		
		public function getGoldRepop()
		{
			return $this->gold_repop;
		}
		
		public function setGoldRepop($gold)
		{
			$this->gold_repop = $gold;
		}
		
		public function getSilverRepop()
		{
			return $this->silver_repop;
		}
		
		public function setSilverRepop($silver)
		{
			$this->silver_repop = $silver;
		}
		
		public function getCopperRepop()
		{
			return $this->copper_repop;
		}
		
		public function setCopperRepop($copper)
		{
			$this->copper_repop = $copper;
		}
	}

?>
