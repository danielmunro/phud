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

	class Kick extends Skill
	{
	
		public static function perform(Actor &$actor, $args = null)
		{
		
			$actor_target = $actor->getTarget();
			$specified_target = ActorObserver::instance()->getActorByRoomAndInput($actor->getRoomId(), $args);
			
			$final_target = null;
			if($actor_target instanceof Actor)
				$final_target = $actor_target;
			else if($specified_target instanceof Actor)
				$final_target = $specified_target;
			
			if($final_target === null)
				return Server::out($actor, 'You kick your legs wildly!');
			
			
			Server::out($actor, 'You kick ' . $final_target->getAlias() . ', causing him pain!');
			Server::out($final_target, $actor->getAlias(true) . ' kicks you!');
			
			$final_target->setHp($final_target->getHp() - (1 + $actor->getLevel() * 0.1), $actor);
			
			if(!($actor_target instanceof Actor))
				$actor->addFighter($final_target);
			
			$actor->incrementDelay(1);
			
		}
		
		public function getName() { return 'kick'; }
	
	}

?>
