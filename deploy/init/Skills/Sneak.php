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

	class Sneak extends Skill
	{
		protected $alias = 'sneak';
		protected $proficiency = 'stealth';
		protected $required_proficiency = 30;
		protected $normal_modifier = ['dex'];

		public function getSubscriber()
		{
			return $this->getInputSubscriber();
		}
		
		protected function applyCost(Actor $actor)
		{
			$actor->incrementDelay(1);
			$m = $actor->getAttribute('movement');
			$cost = -(round((0.05/min(1, $actor->getLevel()/10))*$m));
			$actor->modifyAttribute('movement', $cost);
		}

		protected function success(Actor $actor)
		{
			$a = new Affect([
				'affect' => 'sneak',
				'message_affect' => 'Affect: sneak',
				'message_end' => 'You no longer move silently.',
				'timeout' => min($actor->getAttribute('dex') * 2, $actor->getLevel()),
				'apply' => $actor
			]);
			$actor->getRoom()->announce2([
				['actor' => $actor, 'message' => 'You begin to move silently.'],
				['actor' => '*', 'message' => $actor.' fades into the shadows.']
			]);
		}
		
		protected function fail(Actor $actor)
		{
			Server::out($actor, "Your attempt to move undetected fails.");
		}
	}
?>
