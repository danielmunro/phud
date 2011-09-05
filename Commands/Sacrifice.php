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
	use \Mechanics\Alias;
	use \Mechanics\Actor;
	use \Mechanics\Item;
	use \Mechanics\Server;
	use \Living\Corpse;
	class Sacrifice extends \Mechanics\Command
	{
	
		protected function __construct()
		{
			new Alias('sacrifice', $this);
		}
	
		public function perform(Actor $actor, $args = array())
		{
			$item = $actor->getRoom()->getInventory()->getItemByInput($args[1]);
			
			if($item instanceof Item)
			{
				$actor->getRoom()->getInventory()->remove($item);
				$copper = max(1, $item->getLevel()*3);
				if(!($item instanceof Corpse))
					$copper = min($copper, $item->getValue());
				Server::out($actor, "Mojo finds ".$item->getShort()." pleasing and rewards you.");
				$actor->getRoom()->announce($actor, $actor->getAlias()." sacrifices ".$item->getShort()." to Mojo.");
				$actor->addCopper($copper);
			}
			else
			 return Server::out($actor, "You can't find that.");
		}
	}
?>
