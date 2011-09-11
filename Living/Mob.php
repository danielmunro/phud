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
	use \Mechanics\Dbr;
	use \Mechanics\Pulse;
	use \Mechanics\Room;
	use \Mechanics\Debug;
	use \Mechanics\Alias;
	class Mob extends \Mechanics\Fighter
	{
	
		protected $movement_ticks = 10;
		protected $last_move;
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
		protected $alias = 'a generic mob';
		protected $nouns = 'generic mob';
		
		const FLEE_PERCENT = 10;
		
		public function __construct()
		{
			parent::__construct();
		}
		
		public static function runInstantiation()
		{
			$db = Dbr::instance();
			$mob_ids = $db->sMembers('mobs');
			foreach($mob_ids as $mob_id)
			{
				$mob = unserialize($db->get($mob_id));
				$mob->getRoom()->actorAdd($mob);
				$mob->registerMove();
			}
		}
		
		public function save()
		{
			$this->start_room_id = $this->getRoom()->getId();
			if(!$this->id)
				$this->id = microtime();
			$db = Dbr::instance();
			$db->set($this->id, serialize($this));
			$db->sAdd('mobs', $this->id);
		}
		
		public function delete()
		{
			$db = Dbr::instance();
			$db->del($this->id);
			$db->sRem('mobs', $this->id);
			$this->getRoom()->actorRemove($this);
		}
		
		private function registerMove()
		{
			if($this->movement_ticks)
			{
				$ticks = Pulse::getRandomSeconds($this->movement_ticks);
				Pulse::instance()->registerEvent($ticks, function($mob) { $mob->move(); }, $this, Pulse::EVENT_TICK);
			}
		}
		
		public function move()
		{
			if($this->getRoom()->getId() === Room::PURGATORY_ROOM_ID)
			{
				$this->registerMove();
				return;
			}
			
			$directions = array(
							'north' => $this->getRoom()->getNorth(),
							'south' => $this->getRoom()->getSouth(),
							'east' => $this->getRoom()->getEast(),
							'west' => $this->getRoom()->getWest(),
							'up' => $this->getRoom()->getUp(),
							'down' => $this->getRoom()->getDown());
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
			$areas = explode(' ', $this->area);
			foreach($directions as $dir => $room_id)
			{
				if(in_array(Room::find($room_id)->getArea(), $areas))
				{
					$command = Alias::lookup($dir);
					$command->perform($this);
					$this->registerMove();
					return;
				}
			}
			
			// Now the mob is stuck. Slow down their movement and try again.
			if($this->movement_ticks < 10)
				$this->movement_ticks = 10;
			$this->registerMove();
		}
		
		public function handleDeath()
		{
			parent::handleDeath(false);
			$this->setRoom(Room::find(Room::PURGATORY_ROOM_ID));
			$seconds = Pulse::getRandomSeconds($this->respawn_time);
			Pulse::instance()->registerEvent(
				$seconds,
				function($mob)
				{
					$mob->setRoom($mob->getStartRoom());
					$mob->getRoom()->announce($mob, $mob->getAlias(true).' arrives in a puff of smoke.');
				},
				$this
			);
		}
		
		public function setRace($race)
		{
			parent::setRace($race);
			$atts = $this->getRace()->getAttributes();
			$this->attributes->setStr($atts->getStr());
			$this->attributes->setInt($atts->getInt());
			$this->attributes->setWis($atts->getWis());
			$this->attributes->setDex($atts->getDex());
			$this->attributes->setCon($atts->getCon());
			$this->attributes->setCha($atts->getCha());
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
