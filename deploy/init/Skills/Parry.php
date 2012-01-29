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
    	\Mechanics\Ability\Ability,
		\Mechanics\Event\Event,
		\Mechanics\Event\Subscriber,
		\Mechanics\Actor,
    	\Mechanics\Equipped,
		\Mechanics\Race,
    	\Mechanics\Server;

	class Parry extends Skill
	{
		protected $alias = 'parry';
		protected $proficiency = 'evasive';
		protected $required_proficiency = 25;
		protected $hard_modifier = ['dex'];

		public function getSubscriber()
		{
			return new Subscriber(
				Event::EVENT_MELEE_ATTACKED,
				$this,
				function($subscriber, $fighter, $ability, $attack_event) {
					$ability->perform($fighter, [$attack_event, $subscriber]);
				}
			);
		}
		
		protected function modifyRoll(Actor $actor)
		{
			$weapon = $actor->getEquipped()->getEquipmentByPosition(Equipped::POSITION_WIELD);
			if(!$weapon) {
				return -1;
			}
			return ($actor->getSize() - Race::SIZE_NORMAL) * 10;
		}

		protected function success(Actor $actor, Actor $target, $args)
		{
			$args[0]->suppress();
			$args[1]->satisfyBroadcast();
			$actor->getRoom()->announce2([
				['actor' => $actor, 'message' => "You parry ".$actor->getTarget()."'s attack!"],
				['actor' => $actor->getTarget(), 'message' => ucfirst($actor)." parries your attack!"],
				['actor' => '*', 'message' => ucfirst($actor)." parries ".$actor->getTarget()."'s attack!"]
			]);
		}
	}
?>
