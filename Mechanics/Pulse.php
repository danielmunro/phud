<?php
	namespace Mechanics;
	class Pulse
	{
		private $tick = 0;
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
			$actors = ActorObserver::instance()->getActors();
			foreach($actors as $actor)
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
		
				// TAKE THIS AND CONSIDER PULSE EVENTS
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
			
			$this->tick = date('U');
			$pulses = self::randomizePulse(self::PULSES_PER_TICK, 0.1);
			self::registerEvent($pulses, function() { Pulse::instance()->tick(); }, null);
		}
		
		public static function randomizePulse($pulse, $mod = 0.5)
		{
		
			$low_mod = $mod * $pulse;
			$high_mod = $pulse + $low_mod;
			return rand($low_mod, $high_mod);
		}
		
		public function checkEvents()
		{
		
			$this->pulse = date('U');
			Debug::addDebugLine('Pulse on '.$this->pulse);
			
			// Cycle through events
			if(isset($this->events[$this->pulse]))
			{
				foreach($this->events[$this->pulse] as $event)
					$event['fn']($event['args']);
				unset($this->events[$this->pulse]);
			}
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
