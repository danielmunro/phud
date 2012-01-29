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
    	\Mechanics\Affect;

	class Berserk extends Skill
	{
		protected $alias = 'berserk';
		protected $proficiency = 'melee';
		protected $required_proficiency = 25;
		protected $normal_modifier = ['str', 'dex'];

		public function getSubscriber()
		{
			return $this->getInputSubscriber();
		}

		protected function applyCost(Actor $actor)
		{
			if($actor->getAttribute('movement') < 75) {
				Server::out($actor, "You do not have the energy to do that.");
				return false;
			}
			$actor->incrementDelay(2);
			$actor->modifyAttribute('movement', -($actor->getAttribute('movement') / 2));
			$actor->modifyAttribute('mana', -($actor->getAttribute('mana') / 2));
		}
		
		protected function success(Actor $actor)
		{
			$p = $actor->getLevel() / Actor::MAX_LEVEL;
			$timeout = round(max(2, 2 * $p));
			$str = round(max(1, 4 * $p));
			$dex = round(max(1, 2 * $p));
			new Affect([
				'affect' => 'berserk',
				'message_affect' => 'Affect: berserk',
				'message_end' => 'You cool down.',
				'timeout' => $timeout,
				'attributes' => [
					'str' => $str,
					'dex' => $dex
				],
				'apply' => $actor
			]);
			$actor->modifyAttribute('hp', round(min(5, 75 * $p)));
			$sex = $actor->getDisplaySex([Actor::SEX_MALE => 'he', Actor::SEX_FEMALE => 'she', Actor::SEX_NEUTRAL => 'it']);
			$actor->getRoom()->announce2([
				['actor' => $actor, 'message' => 'Your pulse speeds up as you are consumed by rage!'],
				['actor' => '*', 'message' => ucfirst($actor)."'s pulse speeds up as ".$sex." is consumed by rage!"]
			]);
		}

		protected function fail(Actor $actor)
		{
			$actor->getRoom()->announce2([
				['actor' => $actor, 'message' => 'Your face gets a little red.'],
				['actor' => '*', 'message' => ucfirst($actor)."'s face gets a little red."]
			]);
		}
	}

?>
