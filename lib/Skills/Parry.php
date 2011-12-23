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
    	\Mechanics\Equipped,
    	\Mechanics\Ability\Ability,
    	\Mechanics\Server;

	class Parry extends Skill
	{
		protected $proficiency = 'evasive';
		protected $required_proficiency = 25;

		protected function __construct()
		{
			self::addAlias('parry', $this);
		}

		public function getSubscriber()
		{
			return new Subscriber(
				Event::EVENT_ATTACKED,
				function($subscriber, $parry, $client, $attack_event) {
					$user = $client->getUser();
					if($parry->perform($user, $user->getProficiencyIn($parry->getProficiency()))) {
						$attack_event->suppress();
						$subscriber->satisfyBroadcast();
					}
				}
			);
		}
	
		public function perform(Actor $actor, $args = null)
		{
			$weapon = $this->getEquipped()->getEquipmentByPosition(Equipped::POSITION_WIELD);
			if(!$weapon)
				return false;
		
			$roll = \Mechanics\Server::chance();
			switch($actor->getSize())
			{
				case \Mechanics\Race::SIZE_TINY:
					$roll -= 10;
					break;
				case \Mechanics\Race::SIZE_SMALL:
					$roll -= 5;
					break;
				case \Mechanics\Race::SIZE_LARGE:
					$roll += 5;
					break;
			}
			
			$roll += $this->getNormalAttributeModifier($actor->getDex());
			
			$roll *= 1.25;
			
			if($roll < $this->percent)
			{
				Server::out($actor, $args->getAlias(true) . ' parries your attack!');
				Server::out($args, 'You parry ' . $actor->getAlias() . "'s attack!");
				return true;
			}
		}
	
	}

?>
