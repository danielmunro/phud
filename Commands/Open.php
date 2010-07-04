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
	class Open extends \Mechanics\Command
	{
	
		protected function __construct()
		{
		
			\Mechanics\Command::addAlias(__CLASS__, array('o', 'open'));
		}
	
		public static function perform(&$actor, $args = null)
		{
		
			if(sizeof($args) < 2)
				return Server::out($actor, 'Open what?');
			
			$door = \Mechanics\Command::findObjectByArgs(
									\Mechanics\Door::findByRoomId($actor->getRoom()->getId()),
									$args[1]);
			
			if(empty($door))
				$door = \Mechanics\Door::findByRoomAndDirection($actor->getRoom()->getId(), $args[1]);
			
			if(!empty($door) && !$door->getHidden($actor->getRoom()->getId()))
				switch($door->getDisposition())
				{
					case \Mechanics\Door::DISPOSITION_CLOSED:
						$door->setDisposition(\Mechanics\Door::DISPOSITION_OPEN);
						return \Mechanics\Server::out($actor, 'You open ' . $door->getShort() . '.');
					case \Mechanics\Door::DISPOSITION_OPEN:
						return \Mechanics\Server::out($actor, ucfirst($door->getShort()) . ' is already open.');
					case \Mechanics\Door::DISPOSITION_LOCKED:
						return \Mechanics\Server::out($actor, ucfirst($door->getShort()) . ' is locked.');
				}					
			
			return Server::out($actor, "You can't open anything like that.");
		}
	}
?>
