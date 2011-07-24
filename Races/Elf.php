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
		
			$this->attributes = new \Mechanics\Attributes();
			$this->attributes->setStr(10);
			$this->attributes->setInt(15);
			$this->attributes->setWis(14);
			$this->attributes->setDex(14);
			$this->attributes->setCon(12);
			$this->attributes->setAcBash(100);
			$this->attributes->setAcSlash(100);
			$this->attributes->setAcPierce(100);
			$this->attributes->setAcMagic(100);
			$this->attributes->setHit(1);
			$this->attributes->setDam(1);
			
			$this->max_str = 15;
			$this->max_int = 20;
			$this->max_wis = 19;
			$this->max_dex = 19;
			$this->max_con = 17;
			
			$this->movement_cost = 1;
			
			$this->decrease_thirst = 1;
			$this->decrease_nourishment = 1;
			$this->full = 40;
			
			$this->weapons = array
			(
			);
			
			$this->unarmed_verb = 'punch';
			
			$this->move_verb = 'walks';
			
			$this->size = self::SIZE_NORMAL;
			$this->playable = true;
			
			$this->available_disciplines = array(
				\Disciplines\Crusader::instance(),
				\Disciplines\Rogue::instance(),
				\Disciplines\Wizard::instance()
			);
			
			parent::__construct();
		
		}
	
	}

?>
