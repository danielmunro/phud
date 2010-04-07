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

	class Human extends Race
	{
	
		protected function __construct()
		{
		
			$this->str = 17;
			$this->int = 20;
			$this->wis = 18;
			$this->dex = 19;
			$this->con = 18;
			
			$this->max_str = 21;
			$this->max_int = 24;
			$this->max_wis = 22;
			$this->max_dex = 23;
			$this->max_con = 22;
			
			$this->movement_cost = 2;
			
			$this->decrease_thirst = 1;
			$this->decrease_nourishment = 1;
			$this->full = 20;
			
			$this->move_verb = 'walks';
			
			$this->unarmed_verb = 'punch';
			
			$this->weapons = array
			(
			);
			
			parent::__construct();
		
		}
	
	}

?>
