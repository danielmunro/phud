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
	namespace Skills;
    use \Mechanics\Ability\Skill,
		\Mechanics\Alias,
    	\Mechanics\Actor,
    	\Mechanics\Server,
    	\Disciplines\Thief;

	class Backstab extends Skill
	{
		protected $required_skill = 20;

		protected function __construct()
		{
			new Alias('backstab', $this);
		}
	
		public function perform(Actor $actor, $args = [])
		{
			$target = $actor->reconcileTarget($args);
			if(!$target) {
				return;
			}

			$stealth = $actor->getProficiencyIn('stealth');
			$roll = Server::chance();
			$roll -= $stealth->getPercent();
			$roll += $this->getHardAttributeModifier($actor->getDex());
			
			if($roll < $this->percent)
			{
				$actor->incrementDelay(2);
				$actor->attack('bks');
			}
			else
			{
				$delay = 2;
				if($stealth->getMastery() > Discipline::NOVICE)
					$delay = 1;
				$actor->incrementDelay($delay);
				Server::out($actor, "You fumble your backstab.");
			}
		}
	}
?>
