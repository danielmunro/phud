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
		\Mechanics\Server,
		\Mechanics\Actor;

	class Shield_Block extends Skill
	{
		protected $alias = 'shield block';
		protected $proficiency = 'melee';
		protected $required_proficiency = 25;
		protected $normal_modifier = ['dex'];
		protected $hard_modifier = ['str'];
		
		public function getSubscriber()
		{
			return new Subscriber(
				Event::EVENT_MELEE_ATTACKED,
				$this,
				function($subscriber, $fighter, $ability, $attack_subscriber) {
					$ability->perform($fighter, [$attack_subscriber, $subscriber]);
				}
			);
		}

		protected function applyCost(Actor $actor)
		{
			if($actor->getAttribute('movement') >= 2) {
				$actor->modifyAttribute('movement', -2);
				return true;
			}
			return false;
		}

		protected function success(Actor $actor, Actor $target, $args)
		{
			$args[0]->suppress();
			$args[1]->satisfyBroadcast();
			$sexes = [Actor::SEX_MALE => 'his', Actor::SEX_FEMALE => 'her', Actor::SEX_NEUTRAL => 'its'];
			$s = $actor->getDisplaySex($sexes);
			$actor->getRoom()->announce2([
				['actor' => $actor, 'message' => 'You block '.$target."'s attack with your shield!"],
				['actor' => $target, 'message' => ucfirst($actor).' blocks your attack with '.$s.' shield!'],
				['actor' => '*', 'message' => ucfirst($actor).' blocks '.$target."'s attack with ".$s." shield!"]
			]);
		}
	}
?>
