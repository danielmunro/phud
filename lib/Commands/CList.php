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
		\Mechanics\Server,
		\Mechanics\Actor,
		\Mechanics\Command\User as cUser,
		\Living\User as lUser,
		\Living\Shopkeeper as lShopkeeper;

	class CList extends cUser
	{
		protected $dispositions = array(Actor::DISPOSITION_STANDING, Actor::DISPOSITION_SITTING);
	
		protected function __construct()
		{
			new Alias('list', $this);
		}
	
		public function perform(lUser $user, $args = array())
		{
			
			if(sizeof($args) == 3)
				$target = $user->getRoom()->getUserByInput($args);
			else
			{
				$targets = $user->getRoom()->getActors();
				foreach($targets as $potential_target)
					if($potential_target instanceof lShopkeeper)
						$target = $potential_target;
			}
			
			if(!isset($target))
				return Server::out($user, "They are not here.");
			
			if(!($target instanceof lShopkeeper))
				return Server::out($user, "They are not selling anything.");
			
			Say::perform($target, $target->getListItemMessage());
			Server::out($user, $target->getInventory()->displayContents(true));
		}
	}
?>
