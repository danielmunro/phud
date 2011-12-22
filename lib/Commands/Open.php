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
	use \Mechanics\Actor,
		\Mechanics\Alias,
		\Mechanics\Server,
		\Mechanics\Door as mDoor,
		\Mechanics\Command\Command;

	class Open extends Command
	{
	
		protected $dispositions = array(Actor::DISPOSITION_STANDING);
	
		protected function __construct()
		{
			self::addAlias('open', $this);
		}
	
		public function perform(Actor $actor, $args = array())
		{
			if(sizeof($args) < 2)
				return Server::out($actor, 'Open what?');
			
			$door = $actor->getRoom()->getDoorByInput($args[1]);
			
			if(!empty($door) && !$door->isHidden())
			{
				switch($door->getDisposition())
				{
					case mDoor::DISPOSITION_CLOSED:
						$door->setDisposition(mDoor::DISPOSITION_OPEN);
						$door->getParnterDoor()->setDisposition(mDoor::DISPOSITION_OPEN);
						return Server::out($actor, 'You open '.$door.'.');
					case mDoor::DISPOSITION_OPEN:
						return Server::out($actor, ucfirst($door).' is already open.');
					case mDoor::DISPOSITION_LOCKED:
						return Server::out($actor, ucfirst($door).' is locked.');
				}					
			}
			return Server::out($actor, "You can't open anything like that.");
		}
	}
?>
