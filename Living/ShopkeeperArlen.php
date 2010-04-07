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

	class ShopKeeperArlen extends Shopkeeper
	{
	
		public function __construct()
		{
		
			$this->alias = 'Arlen';
			$this->noun = 'shopkeeper arlen';
			$this->description = 'A short and jovial man stands before you.';
			$this->level = 15;
			$this->movement_speed = 0;
			$this->hp = 10;
			$this->max_hp = 10;
			$this->mana = 100;
			$this->max_mana = 100;
			$this->movement = 100;
			$this->max_movement = 100;
			$this->setRace('human');
			$this->kill_experience_min = 50;
			$this->kill_experience_max = 100;
			$this->auto_flee = true;
			$this->fightable = false;
			
			parent::__construct('temple', 5);
			
			$this->inventory->add(new Food(
											0,
											"A delicious pumpkin pie lies here.",
											"a pumpkin pie",
											"pumpkin pie",
											4,
											1,
											100,
											5,
											true));
			$this->inventory->add(new Drink(
											0,
											"A bottomless flask of water.",
											"a flask of water",
											"flask water",
											50,
											1,
											100,
											5,
											true));
		}
		
		public function describe()
		{
			return $this->description;
		}
		
		public function listItems()
		{
			return "Arlen turns around, wiping the flour from his hands. He says, \"here's what I have for now\"\n" .
				$this->inventory->displayContents(true);
		}
	}
?>
