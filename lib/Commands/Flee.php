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
	use \Mechanics\Actor,
		\Mechanics\Server,
		\Mechanics\Command\Fighter,
		\Mechanics\Command\Command,
		\Mechanics\Event\Subscriber,
		\Mechanics\Fighter as mFighter;

	class Flee extends Fighter
	{
		protected $dispositions = array(Actor::DISPOSITION_STANDING);
	
		protected function __construct()
		{
			self::addAlias('flee', $this);
		}
	
		public function perform(mFighter $fighter, $args = [], Subscriber $command_subscriber)
		{
			$target = $fighter->getTarget();
			if(!$target) {
				return Server::out($fighter, "Flee from who?");
			}
			$target->setTarget(null);
			$fighter->setTarget(null);
			
			$directions = array(
							'north' => $fighter->getRoom()->getNorth(),
							'south' => $fighter->getRoom()->getSouth(),
							'east' => $fighter->getRoom()->getEast(),
							'west' => $fighter->getRoom()->getWest(),
							'up' => $fighter->getRoom()->getUp(),
							'down' => $fighter->getRoom()->getDown());
			$direction = rand(0, sizeof($directions)-1);
			$directions = array_filter(
									$directions,
									function($d)
									{
										return $d !== -1;
									}
								);
			uasort(
				$directions,
				function($i)
				{
					return rand(0, 1);
				}
			);
			foreach($directions as $dir => $id)
			{
				$command = Command::lookup($dir);
				$command->perform($fighter);
				Server::out($fighter, "You run scared!");
				return;
			}
		}
	
	}

?>
