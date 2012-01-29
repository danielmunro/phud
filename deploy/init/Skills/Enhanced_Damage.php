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
		\Mechanics\Event\Subscriber,
		\Mechanics\Event\Event,
    	\Mechanics\Actor,
    	\Mechanics\Server;

	class Enhanced_Damage extends Skill
	{
		protected $alias = 'enhanced damage';
		protected $proficiency = 'melee';
		protected $required_proficiency = 35;
		protected $hard_modifier = ['str'];

		public function getSubscriber()
		{
			return new Subscriber(
				Event::EVENT_DAMAGE_MODIFIER_ATTACKING,
				$this,
				function($subscriber, $fighter, $enh, $target, &$modifier, &$dam_roll) {
					$modifier += $this->perform($fighter);
				}
			);
		}

		protected function applyCost(Actor $actor) {}

		protected function fail(Actor $actor)
		{
			return 0;
		}

		protected function success(Actor $actor)
		{
			$v1 = $actor->getAttribute('str') / 100;
			return rand($v1 / 2, $v1 * 1.25);
		}
	}
?>
