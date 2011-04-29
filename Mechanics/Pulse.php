<?php
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
