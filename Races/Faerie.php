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
	class Faerie extends \Mechanics\Race
	{
	
		public function __construct()
		{
		
			$this->str = 10;
			$this->int = 21;
			$this->wis = 21;
			$this->dex = 20;
			$this->con = 10;
			$this->max_str = 13;
			$this->max_int = 25;
			$this->max_wis = 25;
			$this->max_dex = 25;
			$this->max_con = 13;
			
			$this->movement_cost = 0;
			
			$this->decrease_thirst = 0.5;
			$this->decrease_nourishment = 0.5;
			$this->full = 40;
			
			$this->ac_bash = 0;
			$this->ac_slash = 0;
			$this->ac_pierce = 0;
			$this->ac_magic = -15;
			
			$this->hit_roll = 1;
			$this->dam_roll = 1;
			
			$this->weapons = array
			(
			);
			
			$this->unarmed_verb = 'slap';
			
			$this->move_verb = 'flies';
			
			$this->size = self::SIZE_TINY;
			$this->playable = true;
			
			
			parent::__construct();
		
		}
	
	}

?>
