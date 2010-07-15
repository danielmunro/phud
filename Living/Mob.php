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
	class Mob extends \Mechanics\Actor
	{
	
		protected $movement_speed;
		protected $last_move;
		protected $noun;
		protected $auto_flee = false;
		protected $unique = false;
		protected $kill_experience_min = 0;
		protected $kill_experience_max = 0;
		protected $respawn_time;
		protected $default_respawn_time;
		protected $dead = false;
		protected $start_room_id = 0;
		protected $area = '';
		
		const FLEE_PERCENT = 10;
		
		public function __construct($alias, $noun, $long, $area, $room_id, $level, $race, $movement_speed, $respawn_time, $hp, $mana, $movement)
		{
			$this->alias = $alias;
			$this->noun = $noun;
			$this->long = $long;
			$this->level = $level;
			$this->movement_speed = $movement_speed;
			$this->setRace($race);
			$this->respawn_time = $this->default_respawn_time = $respawn_time;
			$this->start_room_id = $room_id;
			$this->last_move = time();
			$this->area = $area;
			$this->hp = $this->max_hp = $hp;
			$this->mana = $this->max_mana = $mana;
			$this->movement = $this->max_movement = $movement;
			parent::__construct($room_id);
			
			if($movement_speed)
			{
				$pulse = \Mechanics\Server::randomizePulse($movement_speed);
				\Mechanics\ActorObserver::instance()->registerPulseEvent($pulse, function($actor) { $actor->move(); }, $this);
			}
		}
		
		public function move($index = 0)
		{
		
			if($this->room->getId() == \Mechanics\Room::PURGATORY_ROOM_ID || $index > 4)
				return;
			
			$direction = rand(0, 5);
			$directions = array(
							'North' => $this->room->getNorth(),
							'South' => $this->room->getSouth(),
							'East' => $this->room->getEast(),
							'West' => $this->room->getWest(),
							'Up' => $this->room->getUp(),
							'Down' => $this->room->getDown());
			
			$directions = array_filter($directions);
			$i = array_rand($directions);
			
			$areas = explode(' ', $this->area);
			if(!in_array(\Mechanics\Room::find($directions[$i])->getArea(), $areas))
			{
				$this->move($index++);
				return;
			}
			
			\Mechanics\Debug::addDebugLine($this->getAlias() . ' is moving to room #' . $directions[$i] . '.');
			$event = \Mechanics\Command::find($i)->perform($this);
			
			$pulse = \Mechanics\Server::randomizePulse($this->movement_speed);
			\Mechanics\ActorObserver::instance()->registerPulseEvent($pulse, function($actor) { $actor->move(); }, $this);
			
		}
		public function handleRespawn()
		{
			$this->dead = true;
			$this->setRoom(\Mechanics\Room::find(\Mechanics\Room::PURGATORY_ROOM_ID));
		}
		public function decreaseRespawnTime()
		{
			return $this->respawn_time--;
		}
		public function resetRespawnTime()
		{
			$this->respawn_time = $this->default_respawn_time;
		}
		public function getKillExperience()
		{
			return parent::getKillExperience() + rand($this->kill_experience_min, $this->kill_experience_max);
		}
		public function getMovementSpeed() { return $this->movement_speed; }
		public function getNoun() { return $this->noun; }
		public function getTable() { return 'mobs'; }
		public function getDead() { return $this->dead; }
		public function setDead($dead) { $this->dead = $dead; }
		public function getDefaultRoomId() { return $this->start_room_id; }
	}

?>
