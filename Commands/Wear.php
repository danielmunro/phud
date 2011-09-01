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
	class Wear extends \Mechanics\Command
	{
	
		protected $dispositions = array(\Mechanics\Actor::DISPOSITION_STANDING);
	
		protected function __construct()
		{
			new \Mechanics\Alias('wear', $this);
		}
	
		public function perform(\Mechanics\Actor $actor, $args = array())
		{
		
			$item = $actor->getInventory()->getItemByInput($args);
			
			if(!$item)
				return \Mechanics\Server::out($actor, 'You have nothing like that in your inventory.');
			
			if(!($item instanceof \Mechanics\Equipment))
				return \Mechanics\Server::out($actor, "You cannot equip " . $item->getShort() . ".");
			
			return $actor->getEquipped()->equip($actor, $item);
		}
	
	}

?>
