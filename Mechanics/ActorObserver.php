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
		
		private $events = array();
		
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
		
		public function tick()
		{
			Debug::addDebugLine("Tick starting at " . date('Y-m-d H:i:s'));
			foreach($this->actors as $actor)
			{
				
				$actor->setHp($actor->getHp() + ($actor->getMaxHp() * 0.1));
				if($actor->getHp() > $actor->getMaxHp())
					$actor->setHp($actor->getMaxHp());
				
				$actor->setMana($actor->getMana() + ($actor->getMaxMana() * 0.1));
				if($actor->getMana() > $actor->getMaxMana())
					$actor->setMana($actor->getMaxMana());
				
				$actor->setMovement($actor->getMovement() + ($actor->getMaxMovement() * 0.1));
				if($actor->getMovement() > $actor->getMaxMovement())
					$actor->setMovement($actor->getMaxMovement());

				
				
				
				
				// TAKE BOTH OF THESE AND TURN THEM INTO PULSE EVENTS
				if($actor instanceof \Living\User)
				{
					$actor->decreaseRacialNourishmentAndThirst();
					if($actor->getNourishment() < 0)
						Server::out($actor, "You are hungry.");
					if($actor->getThirst() < 0)
						Server::out($actor, "You are thirsty.");
					$actor->save();
					Server::out($actor, "\n" . $actor->prompt(), false);
				}
				// END TAKE
				
				
				
				
			}
		
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
		
		public function checkPulseEvents()
		{
		
			$pulse = date('U');
			Debug::addDebugLine('Pulse on '.$pulse);
			
			// Cycle through events
			if(isset($this->events[$pulse]))
			{
				foreach($this->events[$pulse] as $event)
					$event['fn']($event['args']);
				unset($this->events[$pulse]);
			}
		}
		
		public function registerPulseEvent($pulses, $fn, $args)
		{
			
			$pulses = Server::getLastPulse() + 2 + ($pulses * 2);
			Debug::addDebugLine('Registering event ('.$pulses.')');
			if(!isset($this->events[$pulses]))
				$this->events[$pulses] = array();
			$this->events[$pulses][] = array('fn' => $fn, 'args' => $args);
			return sizeof($this->events[$pulses]);
		}
	
		public function getActors() { return $this->actors; }
	}

?>
