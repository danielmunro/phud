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
	namespace Races;
	class Undead extends \Mechanics\Race
	{
	
		public function __construct()
		{
		
			$this->str = 20;
			$this->int = 15;
			$this->wis = 15;
			$this->dex = 13;
			$this->con = 21;
			$this->max_str = 23;
			$this->max_int = 20;
			$this->max_wis = 20;
			$this->max_dex = 18;
			$this->max_con = 24;
			
			$this->movement_cost = 2;
			
			$this->decrease_thirst = 1;
			$this->decrease_nourishment = 2;
			$this->full = 40;
			
			$this->ac_bash = 85;
			$this->ac_slash = 85;
			$this->ac_pierce = 85;
			$this->ac_magic = 125;
			
			$this->hit_roll = 1;
			$this->dam_roll = 2;
			
			$this->weapons = array
			(
			);
			
			$this->unarmed_verb = 'swipe';
			
			$this->move_verb = 'limps';
			$this->playable = true;
			
			parent::__construct();
		
		}
	
	}

?>
