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
	class CList extends \Mechanics\Command
	{
	
		protected $dispositions = array(\Mechanics\Actor::DISPOSITION_STANDING, \Mechanics\Actor::DISPOSITION_SITTING);
	
		protected function __construct()
		{
			new \Mechanics\Alias('list', $this);
		}
	
		public function perform(\Mechanics\Actor $actor, $args = array())
		{
			
			if(sizeof($args) == 3)
				$target = $actor->getRoom()->getActorByInput($args);
			else
			{
				$targets = $actor->getRoom()->getActors();
				foreach($targets as $potential_target)
					if($potential_target instanceof \Living\Shopkeeper)
						$target = $potential_target;
			}
			
			if(!isset($target))
				return \Mechanics\Server::out($actor, "They are not here.");
			
			if(!($target instanceof \Living\Shopkeeper))
				return \Mechanics\Server::out($actor, "They are not selling anything.");
			
			Say::perform($target, $target->getListItemMessage() . "\n" . $target->getInventory()->displayContents(true));
		}
	}
?>
