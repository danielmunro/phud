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
	class Cast extends \Mechanics\Command
	{
		
		protected function __construct()
		{
			\Mechanics\Command::addAlias(__CLASS__, array('c', 'cast'));
		}
		
		public static function perform(&$actor, $args = null)
		{
			
			// DETERMINE THE SPELL
			// Get rid of 'cast'
			array_shift($args);
			$input = implode(' ', $args);
			
			$spell = $actor->getAbilitySet()->isValidSpell($input);
			if(!$spell)
			{
				// Either target or spell
				$last = array_pop($args);
				$spell = $actor->getAbilitySet()->isValidSpell($input);
			}
			
			if(!$spell)
				return \Mechanics\Server::out($actor, "You don't know that spell.");
			
			// DETERMINE THE TARGET
			$actor_target = $actor->getTarget();
			$specified_target = \Mechanics\ActorObserver::instance()->getActorByRoomAndInput($actor->getRoomId(), $last);
			$real_target = null;
			
			// Target the fighter
			if($actor_target)
				$real_target = $actor_target;
			
			// Target the specified actor
			if($specified_target)
				$real_target = $specified_target;
			
			// Target the caster
			if(!$real_target)
				$real_target = $actor;
			
			\Mechanics\Server::out($actor, 'You utter the words, "' . $spell->getDisplayName(1) . '"');
			\Mechanics\Server::out($real_target, $spell->perform($actor, $real_target));
		}
	}
?>
