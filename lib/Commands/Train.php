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
	namespace Commands;
	use \Mechanics\Command\Command,
		\Living\Trainer,
		\Mechanics\Actor,
		\Mechanics\Server;

	class Train extends Command
	{
		protected $dispositions = [Actor::DISPOSITION_STANDING];

		protected function __construct()
		{
			self::addAlias('train', $this);
		}

		public function perform(Actor $actor, $args = [])
		{
			$args[1] = strtolower($args[1]);
			switch($args[1]) {
				case 'str':
				case 'int':
				case 'wis':
				case 'dex':
				case 'con':
				case 'cha':
					break;
				default:
					return Server::out($actor, "What stat would you like to train (str, int, wis, dex, con, cha)?");
			}

			$actors = $actor->getRoom()->getActors();
			foreach($actors as $a) {
				if($a instanceof Trainer) {
					$a->train($actor, $args[1]);
					return;
				}
			}
			Server::out($actor, "A trainer is not here to help you.");
		}
	}
?>
