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
	class Mob extends \Mechanics\Fighter
	{
	
		protected $movement_speed;
		protected $last_move;
		protected $noun;
		protected $auto_flee = false;
		protected $unique = false;
		protected $respawn_time;
		protected $default_respawn_time;
		protected $dead = false;
		protected $start_room_id = 0;
		protected $area = '';
		
		const FLEE_PERCENT = 10;
		
		public function __construct($properties)
		{
			foreach($properties as $property => $value)
			{
				if($property == 'race')
					$this->setRace($value);
				elseif($property == 'respawn_time')
					$this->respawn_time = $this->default_respawn_time = $value;
				elseif($property == 'fk_room_id')
					$this->start_room_id = $value;
				elseif(property_exists($this, $property))
					$this->$property = $value;
			}
			parent::__construct($this->start_room_id);
			if($this->movement_speed)
			{
				$pulse = \Mechanics\Pulse::randomizePulse($this->movement_speed);
				\Mechanics\Pulse::instance()->registerEvent($pulse, function($actor) { $actor->move(); }, $this);
			}
		}
		
		public static function instantiate($data = null)
		{
			\Mechanics\Debug::addDebugLine('Initializing mobs');
			$results = \Mechanics\Db::getInstance()->query('SELECT * FROM mobs')->fetch_objects();
			\Mechanics\Debug::addDebugLine('mobs: '.sizeof($results));
			foreach($results as $mob)
				new self($mob);
		}
		
		public function save()
		{
			if($this->id)
			{
				\Mechanics\Db::getInstance()->query('UPDATE mobs SET alias = ?, noun = ?, `long` = ?, auto_flee = ?, `unique` = ?, area = ?, movement_speed = ?, 
					respawn_time = ?, str = ?, `int` = ?, wis = ?, dex = ?, con = ?, vit = ?, wil = ?, hp = ?, max_hp = ?, mana = ?, max_mana = ?, movement = ?, 
					max_movement = ?, gold = ?, silver = ?, copper = ?, race = ?, fk_room_id = ?, level = ? WHERE id = ?', array ($this->alias, $this->noun,
					$this->long, $this->auto_flee ? 1 : 0, $this->unique ? 1 : 0, $this->area, $this->movement_speed, $this->respawn_time, $this->base_str, 
					$this->base_int, $this->base_wis, $this->base_dex, $this->base_con, $this->base_vit, $this->base_wil, $this->hp, $this->max_hp, $this->mana,
					$this->max_mana, $this->movement, $this->max_movement, $this->gold, $this->silver, $this->copper, $this->race, $this->room->getId(),
					$this->level, $this->id), true);
			}
			else
			{
				\Mechanics\Db::getInstance()->query('INSERT INTO mobs (alias, noun, `long`, auto_flee, `unique`, area, movement_speed, respawn_time, str, `int`, wis, dex, 
					con, vit, wil, hp, max_hp, mana, max_mana, movement, max_movement, gold, silver, copper, race, fk_room_id, level) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 
					?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array ($this->alias, $this->noun, $this->long, $this->auto_flee ? 1 : 0, 
					$this->unique ? 1 : 0, $this->area, $this->movement_speed, $this->respawn_time, $this->base_str, $this->base_int, $this->base_wis, $this->base_dex, 
					$this->base_con, $this->base_vit, $this->base_wil, $this->hp, $this->max_hp, $this->mana, $this->max_mana, $this->movement, $this->max_movement, 
					$this->gold, $this->silver, $this->copper, $this->race, $this->room->getId(), $this->level), true);
				$this->id = \Mechanics\Db::getInstance()->insert_id;
				$this->inventory->setTableId($this->id);
			}
			$this->inventory->save();
			$this->ability_set->save();
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
			
			\Mechanics\Debug::addDebugLine($this->getAlias() . ' is moving to room #' . $directions[$i] . ' with an index of ('.$index.').');
			$event = \Mechanics\Command::find($i)->perform($this); // Move the damn thing
			
			$pulse = \Mechanics\Pulse::randomizePulse($this->movement_speed);
			\Mechanics\Pulse::instance()->registerEvent($pulse, function($actor) { $actor->move(); }, $this); // Move it again later
			
		}
		public function handleDeath()
		{
			parent::handleDeath(false);
			$this->setRoom(\Mechanics\Room::find(\Mechanics\Room::PURGATORY_ROOM_ID));
			$respawn_pulses = \Mechanics\Pulse::randomizePulse($this->respawn_time, 0.1);
			\Mechanics\Pulse::instance()->registerEvent(
				$respawn_pulses, 
				function($mob)
				{
					$mob->setRoom(\Mechanics\Room::find($mob->getDefaultRoomId()));
					$mob->getRoom()->announce($mob, $mob->getAlias(true).' arrives in a puff of smoke.');
				},
				$this
			);
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
			return parent::getKillExperience() * rand(0.8, 1.2);
		}
		public function getMovementSpeed() { return $this->movement_speed; }
		public function getNoun() { return $this->noun; }
		public function getTable() { return 'mobs'; }
		public function getDead() { return $this->dead; }
		public function setDead($dead) { $this->dead = $dead; }
		public function getDefaultRoomId() { return $this->start_room_id; }
	}

?>
