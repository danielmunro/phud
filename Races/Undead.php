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
	
		protected $creation_points = 20;
	
		protected function __construct()
		{
			$this->alias = new \Mechanics\Alias('undead', $this);
		
			$this->attributes = new \Mechanics\Attributes();
			$this->attributes->setStr(15);
			$this->attributes->setInt(15);
			$this->attributes->setWis(13);
			$this->attributes->setDex(12);
			$this->attributes->setCon(12);
			$this->attributes->setMaxStr(21);
			$this->attributes->setMaxInt(21);
			$this->attributes->setMaxWis(17);
			$this->attributes->setMaxDex(16);
			$this->attributes->setMaxCon(17);
			$this->attributes->setAcBash(100);
			$this->attributes->setAcSlash(100);
			$this->attributes->setAcPierce(100);
			$this->attributes->setAcMagic(100);
			$this->attributes->setHit(1);
			$this->attributes->setDam(1);
			
			$this->movement_cost = 2;
			
			$this->decrease_thirst = 1;
			$this->decrease_nourishment = 2;
			$this->full = 40;
			
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
