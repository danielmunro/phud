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
	namespace Areas;
	class Midgaard extends \Mechanics\Area
	{
	
		protected static function instantiate()
		{
		
			$m = new \Living\Mob
			(
				'a town crier',
				'town crier',
				'You see a town crier before you.',
				'temple midgaard',
				3,
				1,
				'human',
				27,
				1,
				20,
				100,
				100
			);
			//new Skill(0, 'dodge', 100, $m->getAlias());
			new \Living\Mob
			(
				'the zombified remains of the mayor of Midgaard',
				'zombie corpse mayor',
				'The partially decomposed, moaning zombie corpse of the mayor of Midgaard stands before you.',
				'temple midgaard',
				2,
				1,
				'undead',
				14,
				5,
				20,
				100,
				100
			);
			$m = new \Living\Mob
			(
				'a giant rat',
				'giant rat',
				'A behemoth of a rat scurries about before you.',
				'temple midgaard',
				1,
				1,
				'human',
				20,
				5,
				6,
				100,
				100
			);
			$m->getInventory()->add(new \Items\Item(0, "White, red, and blue poker chips are here.", "Sid's poker chips", 'poker chips', 0, 1, 100, 'quest'));
			\Mechanics\Room::find(1)->getInventory()->add(new \Items\Weapon(0, 'a sub issue sword is here.', 'a sub issue sword', 'sub sword', 0, 4, \Items\Weapon::TYPE_SWORD, 1, 2));
			\Mechanics\Room::find(1)->getInventory()->add(new \Items\Weapon(0, 'a sub issue mace is here.', 'a sub issue mace', 'sub mace', 0, 4, \Items\Weapon::TYPE_MACE, 1, 2));
			\Mechanics\Room::find(1)->getInventory()->add(new \Items\Armor(0, 'a sub issue shield is here.', 'a sub issue shield', 'sub shield', 0, 5, \Items\Equipment::TYPE_WIELD, -10, -10, -10, 0));
		}
	}
?>
