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
    use \Mechanics\Ability\Ability,
		\Mechanics\Ability\Skill,
    	\Mechanics\Actor,
    	\Mechanics\Server,
    	\Mechanics\Race;

	class Dodge extends Skill
	{
		protected $alias = 'dodge';
		protected $proficiency = 'evasive';
		protected $required_proficiency = 25;
		protected $easy_modifier = ['dex'];

		public function getSubscriber()
		{
			return new Subscription(
				Event::EVENT_MELEE_ATTACKED,
				$this,
				function($subscriber, $fighter, $ability, $attack_event) {
					$ability->perform($fighter, [$attack_event, $subscriber]);
				}
			);
		}
	
		public function modifyRoll(Actor $actor)
		{
			return ($actor->getSize() - Race::SIZE_NORMAL) * 10;
		}

		protected function success(Actor $actor, Actor $target, $args)
		{
			$args[0]->suppress();
			$args[1]->satisfyBroadcast();
			$actor->getRoom()->announce2([
				['actor' => $actor, 'message' => "You dodge ".$target."'s attack."],
				['actor' => $target, 'message' => $actor." dodges your attack."],
				['actor' => '*', 'message' => $actor." dodges ".$target."'s attack."]
			]);
		}
	}
?>
