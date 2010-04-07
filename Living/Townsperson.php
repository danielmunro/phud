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

	class Townsperson extends Mob
	{
	
		public function __construct($alias, $noun, $description, $area, $room_id, $level, $race, $movement_speed, $respawn_time)
		{
		
			$this->alias = $alias;
			$this->noun = $noun;
			$this->description = $description;
			$this->level = $level;
			$this->movement_speed = $movement_speed;
			$this->hp = 10;
			$this->max_hp = 10;
			$this->mana = 100;
			$this->max_mana = 100;
			$this->movement = 100;
			$this->max_movement = 100;
			$this->setRace($race);
			$this->kill_experience_min = 50;
			$this->kill_experience_max = 100;
			$this->auto_flee = true;
			$this->respawn_time = $this->default_respawn_time = $respawn_time;
			
			parent::__construct($area, $room_id);
		}
		
		public function describe()
		{
			return $this->description;
		}
	
	}

?>
