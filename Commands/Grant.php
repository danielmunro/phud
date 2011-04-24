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
	namespace Commands;
	class Grant extends \Mechanics\Command
	{
	
		protected function __construct()
		{
		
			\Mechanics\Command::addAlias(__CLASS__, array('grant'));
		}
	
		public static function perform(&$actor, $args = null)
		{
			$target = $actor->getRoom()->getActorByInput($args);
			$ability = \Mechanics\Ability::exists($args[1]);
			if($ability)
			{
				$a = new $ability($args[2], $target->getId(), $target->getType());
				$target->getAbilitySet()->addAbility($a);
				\Mechanics\Server::out($target, $actor->getAlias(true)." has bestowed the knowledge of ".$a->getCleanName()." on you.");
				return \Mechanics\Server::out($actor, "You've granted ".$a->getCleanName()." to ".$target->getAlias().".");
			}
			\Mechanics\Server::out($actor, "Ability not found.");
		}
	
	}
?>
