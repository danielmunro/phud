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
	class MidgaardTemple extends \Mechanics\Area
	{
	
		protected static function instantiate()
		{
		
			//use \Living\Mob as Mob;
			
			// Arena
			new \Living\Mob('a snail', 'snail', 'A snail is desperately trying to get out of your way.', 'temple_arena', 53, 1, 'human', 20, 1, 10, 100, 100);
			new \Living\Mob('a snail', 'snail', 'A snail is desperately trying to get out of your way.', 'temple_arena', 28, 1, 'human', 20, 1, 10, 100, 100);
			new \Living\Mob('a snail', 'snail', 'A snail is desperately trying to get out of your way.', 'temple_arena', 35, 1, 'human', 20, 1, 10, 100, 100);
			new \Living\Mob('a snail', 'snail', 'A snail is desperately trying to get out of your way.', 'temple_arena', 42, 1, 'human', 20, 1, 10, 100, 100);
			
			new \Living\Mob('a fox', 'fox', 'A fox is eying you from a distance.', 'temple_arena', 31, 3, 'human', 50, 2, 15, 100, 100);
			new \Living\Mob('a fox', 'fox', 'A fox is eying you from a distance.', 'temple_arena', 50, 3, 'human', 50, 2, 15, 100, 100);
			new \Living\Mob('a fox', 'fox', 'A fox is eying you from a distance.', 'temple_arena', 31, 3, 'human', 50, 2, 15, 100, 100);
			new \Living\Mob('a boar', 'boar', 'A boar getting ready to charge!', 'temple_arena', 27, 3, 'human', 50, 2, 20, 100, 100);
			new \Living\Mob('a boar', 'boar', 'A boar getting ready to charge!', 'temple_arena', 39, 3, 'human', 50, 2, 20, 100, 100);
			
			// Rats
			new \Living\Mob('a giant gray rat', 'gray rat', 'A giant rat is here, staring nefariously at you.', 'midgaard_dungeon', 60, 6, 'human', 30, 2, 25, 100, 100);
			new \Living\Mob('a giant gray rat', 'gray rat', 'A giant rat is here, staring nefariously at you.', 'midgaard_dungeon', 63, 6, 'human', 30, 2, 25, 100, 100);
			new \Living\Mob('a giant gray rat', 'gray rat', 'A giant rat is here, staring nefariously at you.', 'midgaard_dungeon', 64, 6, 'human', 30, 2, 25, 100, 100);
			new \Living\Mob('a giant gray rat', 'gray rat', 'A giant rat is here, staring nefariously at you.', 'midgaard_dungeon', 70, 6, 'human', 30, 2, 25, 100, 100);
			new \Living\Mob('a giant gray rat', 'gray rat', 'A giant rat is here, staring nefariously at you.', 'midgaard_dungeon', 68, 6, 'human', 30, 2, 25, 100, 100);
			new \Living\Mob('a giant gray rat', 'gray rat', 'A giant rat is here, staring nefariously at you.', 'midgaard_dungeon', 73, 6, 'human', 30, 2, 25, 100, 100);
			new \Living\Mob('a giant gray rat', 'gray rat', 'A giant rat is here, staring nefariously at you.', 'midgaard_dungeon', 61, 6, 'human', 30, 2, 25, 100, 100);
			new \Living\Mob('a giant gray rat', 'gray rat', 'A giant rat is here, staring nefariously at you.', 'midgaard_dungeon', 74, 6, 'human', 30, 2, 25, 100, 100);
			new \Living\Mob('a giant gray rat', 'gray rat', 'A giant rat is here, staring nefariously at you.', 'midgaard_dungeon', 73, 6, 'human', 30, 2, 25, 100, 100);
			new \Living\Mob('a giant hissing black rat', 'hissing black rat', 'A giant hissing black rat is here, ready to defend the nest!', 'midgaard_rat_nest', 83, 10, 'human', 0, 2, 45, 100, 100);
			new \Living\Mob('a giant hissing black rat', 'hissing black rat', 'A giant hissing black rat is here, ready to defend the nest!', 'midgaard_rat_nest', 85, 10, 'human', 0, 2, 45, 100, 100);
			new \Living\Mob('a giant hissing black rat', 'hissing black rat', 'A giant hissing black rat is here, ready to defend the nest!', 'midgaard_rat_nest', 85, 10, 'human', 0, 2, 45, 100, 100);
			new \Living\Mob('a giant hissing black rat', 'hissing black rat', 'A giant hissing black rat is here, ready to defend the nest!', 'midgaard_rat_nest', 87, 10, 'human', 0, 2, 45, 100, 100);
			new \Living\Mob('a giant hissing black rat', 'hissing black rat', 'A giant hissing black rat is here, ready to defend the nest!', 'midgaard_rat_nest', 87, 10, 'human', 0, 2, 45, 100, 100);
		}
	}
?>
