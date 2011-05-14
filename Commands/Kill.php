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
	class Kill extends \Mechanics\Command
	{
	
		protected static $dispositions = array(\Mechanics\Actor::DISPOSITION_STANDING);
	
		protected function __construct()
		{
		
			\Mechanics\Command::addAlias(__CLASS__, array('k', 'm', 'kill', 'murder'));
		}
	
		public static function perform(&$actor, $args = null)
		{
		
			if($actor->getTarget())
				\Mechanics\Server::out($actor, "Whoa! Don't you think one is enough?");
			
			$target = $actor->getRoom()->getActorByInput($args);
			
			if(!($target instanceof \Mechanics\Actor))
				return \Mechanics\Server::out($actor, 'Nothing is here.');
			
			if(!($target instanceof \Mechanics\Fighter))
				return \Mechanics\Server::out($actor, $target->getAlias(true)." wouldn't like that very much.");
			
			\Mechanics\Server::out($actor, "You scream and attack!");
			$actor->initiateBattle($target);
		}
	
	}

?>
