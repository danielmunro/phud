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
	class Quest extends \Mechanics\Command
	{
	
		protected function __construct()
		{
		
			\Mechanics\Command::addAlias(__CLASS__, array('q', 'quest'));
		}
	
		public static function perform(&$actor, $args = null)
		{
			
			$action = $args[1];
			$target = null;
			
			if(sizeof($args) == 3)
			{
				$target = $actor->getRoom()->getActorByInput($args);
				if(!($target instanceof Questmaster))
					return Server::out($actor, "You don't see them anywhere.");
			}
			
			if(!($target instanceof Questmaster))
			{
				$actors = $actor->getRoom()->getActors();
				foreach($actors as $t)
					if($t instanceof Questmaster)
						$target = $t;
			}
			
			if(!($target instanceof Questmaster))
				return Server::out($actor, "There are no " . Tag::apply('Questmasters') . "here.");
			
			if(strpos('info', $action) === 0)
				return $target->questInfo($actor);

			if(strpos('accept', $action) === 0)
				return $target->questAccept($actor);
			
			if(strpos('done', $action) === 0)
				return $target->questDone($actor);
			
			return Server::out($actor, "There is no quest action like that. Try help quest.");
		}
	}
?>
