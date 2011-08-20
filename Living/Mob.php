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
		protected $nouns = '';
		protected $auto_flee = false;
		protected $unique = false;
		protected $respawn_time;
		protected $default_respawn_time;
		protected $dead = false;
		protected $start_room_id = 0;
		protected $area = '';
		
		const FLEE_PERCENT = 10;
		
		public function __construct()
		{
			parent::__construct();
		}
		
		/**
		public function setFromProperties($properties)
		{
			foreach($properties as $property => $value)
			{
				if($property == 'race')
					$this->setRace(\Races\Human::instance()); // HACK @todo fixme this is a hack. Need to have configurable races
				elseif($property == 'respawn_time')
					$this->respawn_time = $this->default_respawn_time = $value;
				elseif(property_exists($this, $property))
					$this->$property = $value;
			}
			$this->save();
			$this->registerMove();
		}
		*/
		
		public static function runInstantiation()
		{
			/**
			\Mechanics\Debug::addDebugLine('Initializing mobs');
			$results = \Mechanics\Db::getInstance()->query('SELECT * FROM mobs')->fetch_objects();
			\Mechanics\Debug::addDebugLine('mobs: '.sizeof($results));
			foreach($results as $mob)
				new self($mob);
			die;
			*/

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
			if($this->movement_speed)
			{
				$seconds = \Mechanics\Pulse::getRandomSeconds($this->movement_speed);
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
			$this->respawn_time = $this->default_respawn_time;
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
		
		public function getMovementSpeed() { return $this->movement_speed; }
		
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
		public function getAutoFlee() { return $this->auto_flee; }
		public function isUnique() { return $this->unique; }
		public function getRespawnTime() { return $this->respawn_time; }
		public function getArea() { return $this->area; }
		
		public static function validateAlias($alias)
		{
			return preg_match('/^[A-Za-z ]{2,100}$/i', $alias);
		}
	}

?>
