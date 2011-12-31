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
		protected $proficiency = 'healing';
		protected $required_proficiency = 20;
		protected $saving_attribute = 'wis';

		protected function __construct()
		{
			self::addAlias('meditation', $this);
		}

		public function getSubscriber()
		{
			return new Subscription(
				Event::EVENT_TICK,
				function($subscription, $meditation, $actor) {
					$meditation->perform($actor, $actor->getProficiencyIn($meditation->getProficiency()));
				}
			);
		}
	
		public function perform(Actor $actor, $percent, $args = null)
		{
			if($actor->getDisposition() === Actor::DISPOSITION_STANDING) {
				return;
			}
		
			$roll = Server::chance() - $percent;
			$roll += $this->getEasyAttributeModifier($actor->getWis());
			
			if($roll < $chance) {
				$amount = rand(0.01, 0.05);
				$actor->setHp($actor->getHp() + ($actor->getMaxHp() * $amount));
				$actor->setMana($actor->getMana() + ($actor->getMaxMana() * $amount));
				$actor->setMovement($actor->getMovement() + ($actor->getMovement() * $amount));
			}
		}
	}
?>
