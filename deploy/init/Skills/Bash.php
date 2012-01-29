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
		\Mechanics\Actor,
    	\Mechanics\Server,
		\Mechanics\Event\Event,
    	\Mechanics\Affect;

	class Bash extends Skill
	{
		protected $alias = 'bash';
		protected $proficiency = 'melee';
		protected $required_proficiency = 20;
		protected $easy_modifier = ['str'];
		protected $needs_target = true;

		public function getSubscriber()
		{
			return $this->getInputSubscriber();
		}

		protected function applyCost(Actor $actor)
		{
			$amount = min(20, 51 - $actor->getLevel());
			if($actor->getAttribute('movement') < $amount) {
				return false;
			}
			$actor->modifyAttribute('movement', -($amount));
		}
	
		protected function modifyRoll(Actor $actor)
		{
			$roll = 0;
			$roll -= $actor->getRace()['lookup']->getSize() * 1.25;
			$roll += $actor->getTarget()->getRace()['lookup']->getSize();
			$target->fire(Event::EVENT_BASHED, $actor->getTarget(), $roll);
			return $roll;
		}

		protected function fail(Actor $actor)
		{
			$sexes = [Actor::SEX_MALE=>'him',Actor::SEX_FEMALE=>'her',Actor::SEX_NEUTRAL=>'it'];
			$s = $actor->getDisplaySex($sexes);
			$actor->getRoom()->announce2([
				['actor' => $actor, 'message' => 'You fall flat on your face!'],
				['actor' => $actor->getTarget(), 'message' => ucfirst($actor)." tries to bash you but you evade their attack!"],
				['actor' => '*', 'message' => ucfirst($actor)." falls flat on ".$s." trying to bash ".$actor->getTarget()."!"]
			]);
		}

		protected function success(Actor $actor)
		{
			new Affect([
				'affect' => 'stun',
				'timeout' => 1,
				'apply' => $actor->getTarget()
			]);
			$sexes = [Actor::SEX_MALE=>'him',Actor::SEX_FEMALE=>'her',Actor::SEX_NEUTRAL=>'it'];
			$s = $actor->getTarget()->getDisplaySex($sexes);
			$actor->getRoom()->announce2([
				['actor' => $actor, 'message' => "You slam into ".$actor->getTarget()." and send ".$s." flying!"],
				['actor' => $actor->getTarget(), 'message' => ucfirst($actor)." slams into you and sends you flying!"],
				['actor' => '*', 'message' => ucfirst($actor)." slams into ".$actor->getTarget()." and sends ".$s." flying!"]
			]);
		}
	}
?>
