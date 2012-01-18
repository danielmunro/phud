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
    use \Mechanics\Ability\Skill;
    use \Mechanics\Server;
    use \Mechanics\Actor;

	class Shield_Block extends Skill
	{
		protected $proficiency = 'melee';
		protected $required_proficiency = 25;
		protected $saving_attribute = 'str';

		protected function __construct()
		{
			self::addAlias('shield block', $this);
		}
		
		public function getSubscriber()
		{
			return new Subscriber(
				Event::EVENT_MELEE_ATTACKED,
				$this,
				function($subscriber, $fighter, $ability, $attack_subscriber) {
					if($ability->perform($fighter, $fighter->getProficiencyIn($ability->getProficiency()))) {
						$attack_subscriber->suppress();
						Server::out($fighter, "You block ".$fighter->getTarget()."'s attack with your shield!");
						$subscriber->satisfyBroadcast();
					}
				}
			);
		}
		
		public function perform(Actor $actor, $percent, $args = null)
		{
			$roll = Server::chance();
			
			$roll += $this->getEasyAttributeModifier($actor->getAttribute('dex'));
			$roll += $this->getHardAttributeModifier($actor->getAttribute('str'));
			
			$roll *= 1.25;
			
			if($roll < $this->percent)
			{
				Server::out($actor, ucfirst($args)." blocks your attack with " . $args->getDisplaySex() . " shield!");
				Server::out($args, "You block ".$actor."'s attack with your shield!");
				return true;
			}
			return false;
		}
	
	}

?>