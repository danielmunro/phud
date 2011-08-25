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
	
		protected $movement_ticks = 10;
		protected $last_move;
		protected $nouns = '';
		protected $auto_flee = false;
		protected $unique = false;
		protected $respawn_time;
		protected $default_respawn_ticks = 1;
		protected $dead = false;
		protected $start_room_id = 0;
		protected $area = '';
		protected $gold_repop = 0;
		protected $silver_repop = 0;
		protected $copper_repop = 0;
		
		const FLEE_PERCENT = 10;
		
		public function __construct()
		{
			parent::__construct();
		}
		
		public static function runInstantiation()
		{
			$db = \Mechanics\Dbr::instance();
			$mob_count = $db->lSize('mobs');
			$mobs = $db->lRange('mobs', 0, $mob_count);
			foreach($mobs as $i => $mob)
			{
				$m = unserialize($mob);
				$m->setRoom($m->getStartRoom());
				//$m->registerMove();
			}
		}
		
		public function save()
		{
			$db = \Mechanics\Dbr::instance();
			$this->start_room_id = $this->getRoom()->getId();
			if(is_numeric($this->id))
				$db->lSet('mobs', $this->id, serialize($this));
			else
			{
				$this->id = $db->rPush('mobs', serialize($this)) - 1;
				$this->save(); //write the new ID
			}
		}
		
		private function registerMove()
		{
			if($this->movement_ticks)
			{
				$seconds = \Mechanics\Pulse::getRandomSeconds($this->movement_ticks);
				\Mechanics\Pulse::instance()->registerEvent($seconds, function($actor) { $actor->move(); }, $this);
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
			\Mechanics\Command::find($i)->perform($this); // Move the damn thing
			$this->registerMove();
		}
		
		public function handleDeath()
		{
			parent::handleDeath(false);
			$this->setRoom(\Mechanics\Room::find(\Mechanics\Room::PURGATORY_ROOM_ID));
			$seconds = \Mechanics\Pulse::getRandomSeconds($this->respawn_time);
			\Mechanics\Pulse::instance()->registerEvent(
				$seconds,
				function($mob)
				{
					$mob->setRoom($mob->getStartRoom());
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
			$this->respawn_time = $this->default_respawn_ticks;
		}
		
		public function getRespawnTime()
		{
			return $this->respawn_time;
		}
		
		public function getDefaultRespawnTicks()
		{
			return $this->default_respawn_ticks;
		}
		
		public function setDefaultRespawnTicks($ticks)
		{
			$this->default_respawn_ticks = $ticks;
		}
		
		public function getKillExperience()
		{
			return parent::getKillExperience() * rand(0.8, 1.2);
		}
		
		public function getExperiencePerLevel()
		{
			return $this->getExperiencePerLevelFromCP();
		}
		
		public function getStartRoom()
		{
			return \Mechanics\Room::find($this->start_room_id);
		}
		
		public function setStartRoom()
		{
			$this->start_room_id = $this->room_id;
		}
		
		public function getMovementTicks()
		{
			return $this->movement_ticks;
		}
		
		public function setMovementTicks($movement_ticks)
		{
			$this->movement_ticks = $movement_ticks;
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
