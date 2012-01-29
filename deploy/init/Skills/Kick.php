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
		\Mechanics\Actor,
		\Mechanics\Damage;

	class Kick extends Skill
	{
		protected $alias = 'kick';
		protected $proficiency = 'melee';
		protected $required_proficiency = 20;
		protected $normal_modifier = ['dex', 'str'];
		protected $needs_target = true;

		public function getSubscriber()
		{
			return $this->getInputSubscriber();
		}

		protected function applyCost(Actor $actor)
		{
			$actor->incrementDelay(1);
		}

		protected function fail(Actor $actor)
		{
			$actor->getRoom()->announce2([
				['actor' => $actor, 'message' => "Your kick misses ".$actor->getTarget()." harmlessly."],
				['actor' => $actor->getTarget(), 'message' => ucfirst($actor)."'s kick misses you harmlessly."],
				['actor' => '*', 'message' => ucfirst($actor)."'s kick misses ".$actor->getTarget()." harmlessly."]
			]);
		}

		protected function success(Actor $actor)
		{
			$damage = rand(1, (1+$actor->getLevel()/2));
			$actor->getTarget()->modifyAttribute('hp', -($damage));
			$sexes = [Actor::SEX_MALE => "him", Actor::SEX_FEMALE => "her", Actor::SEX_NEUTRAL => "it"];
			$s = $actor->getDisplaySex($sexes);
			$actor->getRoom()->announce2([
				['actor' => $actor, 'message' => "Your kick hits ".$actor->getTarget().", causing ".$s." pain!"],
				['actor' => $actor->getTarget(), 'message' => ucfirst($actor)."'s kick hits you!"],
				['actor' => '*', 'message' => ucfirst($actor)."'s kick hits ".$actor->getTarget().", causing ".$s." pain!"]
			]);
		}
	}

?>
