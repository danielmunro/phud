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
	namespace Mechanics\Ability;
	use \Living\User,
		\Mechanics\Event\Subscriber,
		\Mechanics\Event\Event,
		\Mechanics\Server;

	abstract class Skill extends Ability
	{
		public function tryPerform(User $user, $args = [])
		{
			$proficiency = $user->getProficiencyIn($this->proficiency);
			if($this->required_proficiency < $proficiency) {
				return Server::out($user, "You do not have the skill necessary to do that.");
			}
			if($this->is_performable) {
				$ability->perform($user, $percent, $args);
			}
		}

		protected function getInputSubscriber($alias)
		{
			return new Subscriber(
				Event::EVENT_INPUT,
				$this,
				function($subscriber, $user, $ability, $args) use ($alias) {
					if(!$subscriber->isBroadcastSatisfied() && strpos($alias, $args[0]) === 0) {
						// @TODO check if user can do this
						$ability->perform($user, $user->getProficiencyIn($ability->getProficiency()), $args);
						$subscriber->satisfyBroadcast();
					}
				},
				true
			);
		}

		//abstract public function perform(Actor $actor, $proficiency, $args = []);
		abstract public function getSubscriber();
	}
?>
