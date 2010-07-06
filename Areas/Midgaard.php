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
		
			new \Living\Mob('a town crier', 'town crier', 'You see a town crier before you.', 'temple midgaard', 3, 1, 'human', 27, 1, 20, 100, 100);
			new \Living\Mob('the zombified remains of the mayor of Midgaard', 'zombie corpse mayor', 'The partially decomposed, moaning zombie corpse of the mayor of Midgaard stands before you.', 'temple midgaard', 3, 1, 'undead', 14, 5, 20, 100, 100);
			$m = new \Living\Shopkeeper('Erog the blacksmith', 'erog blacksmith', 'A large ogre stands before you with a giant smelting iron by his side.', 'midgaard', 12, 1, 'ogre');
			$m->getInventory()->add(array(
				new \Items\Weapon(0, 'a sub issue sword is here.', 'a sub issue sword', 'sub sword', 'slash', 100, 4, \Items\Weapon::TYPE_SWORD, \Items\Weapon::DAMAGE_SLASH, 1, 2),
				new \Items\Weapon(0, 'a sub issue mace is here.', 'a sub issue mace', 'sub mace', 'pound', 100, 4, \Items\Weapon::TYPE_MACE, \Items\Weapon::DAMAGE_POUND, 1, 2),
				new \Items\Weapon(0, 'a sub issue dagger is here.', 'a sub issue dagger', 'sub dagger', 'stab', 100, 4, \Items\Weapon::TYPE_DAGGER, \Items\Weapon::DAMAGE_PIERCE, 1, 2)
			));
			$m = new \Living\Shopkeeper('Halek the armorsmith', 'halek armorsmith', 'A cautious looking elf stands before you.', 'midgaard', 57, 1, 'elf');
			$m->getInventory()->add(array(
				new \Items\Armor(0, 'a sub issue shield is here.', 'a sub issue shield', 'sub shield', 0, 5, \Items\Equipment::TYPE_WIELD, -5, -5, -5, 0),
				new \Items\Armor(0, 'a pair of sub issue gloves are here.', 'a pair of sub issue gloves', 'sub gloves', 0, 5, \Items\Equipment::TYPE_HANDS, -5, -5, -5, 0),
				new \Items\Armor(0, 'a sub issue belt is here.', 'a sub issue belt', 'sub belt', 0, 5, \Items\Equipment::TYPE_WAIST, -5, -5, -5, 0),
				new \Items\Armor(0, 'a sub issue helmet is here.', 'a sub issue helmet', 'sub helmet', 0, 5, \Items\Equipment::TYPE_HEAD, -5, -5, -5, 0),
				new \Items\Armor(0, 'a pair of sub issue boots are here.', 'a pair of sub issue boots', 'sub boots', 0, 5, \Items\Equipment::TYPE_FEET, -5, -5, -5, 0)
			));
			$m = new \Living\Shopkeeper('Alfred the store clerk', 'alfred clerk', 'Alfred smiles and offers you to look around.', 'midgaard', 59, 1, 'human');
			$m->getInventory()->add(array(
				new \Items\Armor(0, 'a wooden torch is here.', 'a wooden torch', 'wooden torch', 1, 1, \Items\Equipment::TYPE_LIGHT, 0, 0, 0, 0, 100, 1, null, \Mechanics\Affect::TYPE_LIGHT)
			));
			$m = new \Living\Shopkeeper('Annir the bartender', 'annir bartender', 'Annir, the faerie bartender buzzes before you.', 'midgaard', 79, 1, 'faerie');
			$m->getInventory()->add(array(
				new \Items\Drink(0, 'a small leather canteen is here', 'a small leather canteen', 'leather canteen', 5, 1)
			));
		}
	}
?>
