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
	namespace Mechanics;
	class ActorObserver
	{
	
		static $instance = null;
		
		private $actors = array();
		private $queue;
		
		private function __construct() {}
		
		public static function instance()
		{
		
			if(!self::$instance)
				self::$instance = new ActorObserver();
			
			return self::$instance;
		
		}
		
		public function add(Actor &$instance)
		{
			$this->actors[] = $instance;
			return sizeof($this->actors);
		}
		
		public function whoList($actor)
		{
		
			Server::out($actor, 'Who list:');
			$players = 0;
			foreach($this->actors as $actors)
			{
				if(!($actors instanceof \Living\User))
					continue;
				Server::out($actor, '[' . $actors->getLevel() . ' ' . $actors->getRace() . ' ' . $actors->getDiscipline() . '] ' . $actors->getAlias());
				$players++;
			}
			Server::out($actor, $players . ' player' . (sizeof($this->actors) != 1 ? 's' : '') . ' found.');
		
		}
	
		public function getActors() { return $this->actors; }
	}

?>
