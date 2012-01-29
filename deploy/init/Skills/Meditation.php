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
    	\Mechanics\Server,
    	\Mechanics\Actor;

	class Meditation extends Skill
	{
		protected $alias = 'meditation';
		protected $proficiency = 'healing';
		protected $required_proficiency = 20;
		protected $easy_modifier = ['wis'];

		public function getSubscriber()
		{
			return new Subscription(
				Event::EVENT_TICK,
				function($subscription, $meditation, $actor) {
					$meditation->perform($actor);
				}
			);
		}

		protected function applyCost(Actor $actor)
		{
		}

		protected function success(Actor $actor)
		{
			$amount = $actor->getProficiencyIn($this->proficiency) / 100;
			$actor->modifyAttribute('hp', $actor->getMaxAttribute('hp') * $amount);
			$actor->modifyAttribute('mana', $actor->getMaxAttribute('mana') * $amount);
			$actor->modifyAttribute('movement', $actor->getMaxAttribute('movement') * $amount);
		}
	
		protected function fail(Actor $actor)
		{
		}
	}
?>
