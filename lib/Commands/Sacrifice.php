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
	use \Mechanics\Alias,
		\Mechanics\Actor,
		\Mechanics\Item as mItem,
		\Mechanics\Server,
		\Mechanics\Command\Command,
		\Items\Corpse,
		\Living\User as lUser,
		\Living\Mob as lMob;

	class Sacrifice extends Command
	{
		protected function __construct()
		{
			new Alias('sacrifice', $this);
		}
	
		public function perform(Actor $actor, $args = array())
		{
			$item = $actor->getRoom()->getInventory()->getItemByInput($args[1]);
			
			if($item instanceof mItem)
			{
				$actor->getRoom()->getInventory()->remove($item);
				$copper = max(1, $item->getLevel()*3);
				if(!($item instanceof Corpse))
					$copper = min($copper, $item->getValue());
				Server::out($actor, "Mojo finds ".$item." pleasing and rewards you.");
				$actor->getRoom()->announce($actor, $actor." sacrifices ".$item." to Mojo.");
				$actor->addCopper($copper);
				return;
			}
			else if($actor instanceof User && $actor->isDM())
			{
				$mob = $actor->getRoom()->getActorByInput($args[1]);
				if($mob instanceof Mob)
				{
					$actor->getRoom()->actorRemove($mob);
					return Server::out($actor, "You slay ".$mob." and eat its soul in the name of your gods.");
				}
				
				$door = $actor->getRoom()->getDoorByInput($args[1]);
				if($door instanceof Door)
				{
					$actor->getRoom()->removeDoor($door);
					return Server::out($actor, ucfirst($door)." crumbles into dust and disappears into the wind.");
				}
			}
			Server::out($actor, "You can't find that.");
		}
	}
?>
