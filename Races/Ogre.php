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

	class Ogre extends Race
	{
	
		public function __construct()
		{
		
			$this->str = 21;
			$this->int = 12;
			$this->wis = 14;
			$this->dex = 17;
			$this->con = 21;
			$this->max_str = 25;
			$this->max_int = 17;
			$this->max_wis = 19;
			$this->max_dex = 21;
			$this->max_con = 25;
			
			$this->movement_cost = 2;
			
			$this->decrease_thirst = 1;
			$this->decrease_nourishment = 2;
			$this->full = 60;
			
			$this->ac_bash = -15;
			$this->ac_slash = -15;
			$this->ac_pierce = -15;
			$this->ac_magic = 10;
			
			$this->hit_roll = 1;
			$this->dam_roll = 3;
			
			$this->weapons = array
			(
			);
			
			$this->unarmed_verb = 'pummel';
			
			$this->move_verb = 'walks';
			
			$this->size = self::SIZE_LARGE;
			
			$this->effects_resist = array(Effect::FIRE, Effect::COLD);
			
			parent::__construct();
		
		}
	
	}

?>
