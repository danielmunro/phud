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
	namespace Living;
	use \Living\User,
		\Mechanics\Server;

	class Trainer extends Mob
	{
		protected $alias = 'a generic trainer';
		protected $nouns = 'generic trainer';
		protected $max_proficiency = 0;

		public function train(User $user, $stat)
		{
			if($user->getTrains()) {
				$user->decreaseTrains();
				$user->modifyAttribute($stat, 1);
				return Server::out($user, "Your ".$stat." increases!");
			}
			Server::out($user, "You don't have the trains to do that.");
		}
	}
?>