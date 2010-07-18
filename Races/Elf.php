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
	class Elf extends \Mechanics\Race
	{
	
		public function __construct()
		{
		
			$this->str = 14;
			$this->int = 18;
			$this->wis = 21;
			$this->dex = 21;
			$this->con = 15;
			$this->max_str = 18;
			$this->max_int = 22;
			$this->max_wis = 25;
			$this->max_dex = 25;
			$this->max_con = 19;
			
			$this->movement_cost = 1;
			
			$this->decrease_thirst = 1;
			$this->decrease_nourishment = 1;
			$this->full = 40;
			
			$this->ac_bash = 100;
			$this->ac_slash = 100;
			$this->ac_pierce = 100;
			$this->ac_magic = 100;
			
			$this->hit_roll = 1;
			$this->dam_roll = 2;
			
			$this->weapons = array
			(
			);
			
			$this->unarmed_verb = 'punch';
			
			$this->move_verb = 'walks';
			
			$this->size = self::SIZE_NORMAL;
			$this->playable = true;
			
			parent::__construct();
		
		}
	
	}

?>
