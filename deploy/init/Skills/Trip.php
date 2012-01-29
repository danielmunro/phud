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
		\Mechanics\Affect,
		\Mechanics\Server,
    	\Mechanics\Actor;

	class Trip extends Skill
	{
		protected $alias = 'trip';
		protected $proficiency = 'melee';
		protected $required_proficiency = 20;
		protected $hard_modifier = ['dex'];
		protected $needs_target = true;

		public function getSubscriber()
		{
			return $this->getInputSubscriber();
		}

		protected function applyCost(Actor $actor)
		{
			$cost = 50 - $this->level;
			$actor->incrementDelay(1);
			if($actor->getAttribute('movement') < $cost) {
				Server::out($actor, "You don't have enough energy to trip.");
				return false;
			}
			$actor->modifyAttribute('movement', -($cost));
		}
		
		protected function success(Actor $actor)
		{
			new Affect([
				'affect' => 'stun',
				'timeout' => 1,
				'apply' => $actor->getTarget()
			]);
			$actor->getRoom()->announce2([
				['actor' => $actor, 'message' => 'You throw a leg out and trip '.$actor->getTarget().'.'],
				['actor' => $actor->getTarget(), 'message' => ucfirst($actor).' trips you and you go down!'],
				['actor' => '*', 'message' => ucfirst($actor).' trips '.$actor->getTarget().' and they go down!']
			]);
		}

		protected function fail(Actor $actor)
		{
			$actor->getRoom()->announce2([
				['actor' => $actor, 'message' => 'You try to trip '.$actor->getTarget().' but fail.'],
				['actor' => $actor->getTarget(), 'message' => ucfirst($actor).' tries to trip you but you evade their attack.'],
				['actor' => '*', 'message' => ucfirst($actor).' tries to trip '.$actor->getTarget().' but they fail.']
			]);
		}
	}
?>
