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
	class Pulse
	{
		private $tick = 0;
		private $next_tick = 0;
		private $pick = 0;
		private $events = array();
		private static $instance = null;
		
		const TICK_MIN = 50;
		const TICK_MAX = 50;
		const TICK = 50;
		const PULSES_PER_TICK = 25;
		
		private function __construct()
		{
			$this->pulse = $this->tick = date('U');
			$this->tick();
		}
		
		public static function instance()
		{
			if(!isset(self::$instance))
				self::$instance = new self();
			return self::$instance;
		}
		
		public function tick()
		{
			Debug::addDebugLine("Tick starting at " . date('Y-m-d H:i:s'));
			$pulses = self::randomizePulse(self::PULSES_PER_TICK);
			$this->tick = date('U');
			$this->next_tick = $pulses;
			self::registerEvent($pulses, function() { Pulse::instance()->tick(); }, null);
		}
		
		public static function randomizePulse($pulse, $mod = 0.1)
		{
		
			$low_mod = $mod * $pulse;
			$high_mod = $pulse + $low_mod;
			return rand($low_mod, $high_mod);
		}
		
		public function checkEvents()
		{
		
			$this->pulse = date('U');
			$this->next_tick--;
			Debug::addDebugLine('Pulse on '.$this->pulse);
			
			// Cycle through events
			if(isset($this->events[$this->pulse]))
			{
				foreach($this->events[$this->pulse] as $event)
					$event['fn']($event['args']);
				unset($this->events[$this->pulse]);
			}
		}
		
		public function registerTickEvent($fn, $args)
		{
			$this->registerEvent($this->next_tick, $fn, $args);
		}
		
		public function registerEvent($pulses, $fn, $args)
		{
			
			$pulses = $this->pulse + 2 + ($pulses * 2);
			Debug::addDebugLine('Registering event ('.$pulses.')');
			if(!isset($this->events[$pulses]))
				$this->events[$pulses] = array();
			$this->events[$pulses][] = array('fn' => $fn, 'args' => $args);
			return sizeof($this->events[$pulses]);
		}
		
		public function getLastPulse()
		{
			return $this->pulse;
		}
	}
?>
