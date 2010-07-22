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
		
		private $actors;
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
		}
		
		public function getActorsInRoom($room_id)
		{
			
			$actors = array();
			foreach($this->actors as $actor)
				if($actor->getRoom()->getId() == $room_id)
					$actors[] = $actor;
			
			return $actors;
			
		}
		
		public function getActorByRoomAndInput($room_id, $input)
		{
			
			if(empty($input[1]))
				return;
			
			if(is_array($input))
				$input = array_pop($input);
			
			$person = strtolower($input);
			foreach($this->actors as $actor)
			{
			
				if($actor->getRoom()->getId() != $room_id)
					continue;
				
				if(property_exists($actor, 'noun'))
					$look_for = explode(' ', $actor->getNoun());
				else
					$look_for = array($actor->getAlias());
				
				foreach($look_for as $look)
					if(stripos($look, $person) === 0)
						return $actor;
			}
			return null;
		}
		
		public function updateRoomChange(Actor $actor_changed, $details)
		{
			
			foreach($this->actors as $actor)
			{
				
				if($actor->getRoom()->getId() == $actor_changed->getRoom()->getId() && $actor->getAlias() != $actor_changed->getAlias())
				{
					if($details == 'arriving')
						Server::out($actor, $actor_changed->getAlias(true) . ' has arrived.');
					
					if(strpos($details, 'leaving') !== false)
						Server::out($actor, $actor_changed->getAlias(true) . ' ' . $actor_changed->getRace()->getMoveVerb() . ' ' . strtolower(substr($details, 8)) . '.');
					
					if($details == 'looking')
						Server::out($actor_changed, ($actor instanceof \Living\Questmaster ? Tag::apply('Questmaster') : '') . $actor->getAlias(true) . ' is here.');
				}
			}
		
		}
		
		public function tick()
		{
			Debug::addDebugLine("Tick starting at " . date('Y-m-d H:i:s'));
			foreach($this->actors as $actor)
			{
				
				$actor->setHp($actor->getHp() + ($actor->getMaxHp() * .1));
				if($actor->getHp() > $actor->getMaxHp())
					$actor->setHp($actor->getMaxHp());
				
				$actor->setMana($actor->getMana() + ($actor->getMaxMana() * .1));
				if($actor->getMana() > $actor->getMaxMana())
					$actor->setMana($actor->getMaxMana());
				
				$actor->setMovement($actor->getMovement() + ($actor->getMaxMovement() * .1));
				if($actor->getMovement() > $actor->getMaxMovement())
					$actor->setMovement($actor->getMaxMovement());

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
				
				if($actor instanceof \Living\Mob && $actor->getDead())
					if($actor->decreaseRespawnTime() < 1)
					{
						$actor->setRoom(Room::find($actor->getDefaultRoomId()));
						$actor->resetRespawnTime();
						$actor->setDead(false);
						self::announceToOthersInRoom($actor->getRoom()->getId(), $actor->getAlias(true));
					}
			}
		
		}
		
		public static function announceToOthersInRoom($room_id, $actor_alias)
		{
			$actors = self::instance()->getActorsInRoom($room_id);
			foreach($actors as $actor)
				if($actor->getAlias(true) != $actor_alias)
					Server::out($actor, $actor_alias . " arrives in a puff of smoke.");
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
			print $pulse . "\n";
			if(isset($this->events[$pulse]))
			{
				foreach($this->events[$pulse] as $event)
					$event['fn']($event['args']);
				unset($this->events[$pulse]);
			}
			
			// Target statuses for fighters
			foreach($this->actors as $actor)
				if($target = $actor->getTarget())
					Server::out($actor, $target->getAlias(true) . ' ' . $target->getStatus() . ".\n\n" . ($actor instanceof \Living\User ? $actor->prompt() : ''), false);
		}
		
		public function registerPulseEvent($pulses, $fn, $args)
		{
		
			$pulses = Server::getLastPulse() + 2 + ($pulses * 2);
			print 'REGISTER EVENT ON (' . $pulses . ")\n";
			if(!isset($this->events[$pulses]))
				$this->events[$pulses] = array();
			$this->events[$pulses][] = array('fn' => $fn, 'args' => $args);
		}
	
		public function getActors() { return $this->actors; }
	}

?>
