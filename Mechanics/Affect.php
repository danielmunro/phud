<?php

	namespace Mechanics;
	class Affect
	{
	
		const GLOW = 'glow';
		const STUN = 'stun';
		
		private $affect = '';
		private $pulse_start = 0;
		private $pulse_timeout = 0;
		private $message_timeout = '';
		
		public function __construct(&$target, $affect, $start = null, $end = null, $pulse_timeout = null, $message_timeout = '')
		{
		
			$this->affect = $affect;
			
			if($pulse_timeout)
			{
				$this->pulse_start = Server::getLastPulse();
				$this->pulse_timeout = $pulse_timeout;
				$this->message_timeout = $message_timeout;
			}
			$target->addAffect($this);
	
			if($start)
				$start($target);
	
			if($end && $pulses)
				ActorObserver::instance()->registerPulseEvent
				(
					$this->pulse_timeout,
					function($args)
					{
						$args[0]->removeAffect($args[1]);
						$args[2]($args[0]);
						if($args[3])
							Server::out($args[0], $args[3]);
					},
					array($target, $this, $end, $this->message_timeout)
				);
		}
		public function getAffect() { return $this->affect; }
		public function getPulseStart() { return $this->pulse_start; }
		public function save($table, $id)
		{
		
			$pulse_timeout = $this->pulse_timeout - (Server::getLastPulse() - $this->pulse_start);
			Db::getInstance()->query('INSERT INTO affects (fk_table, fk_id, affect, pulse_timeout) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE pulse_timeout = ?', array($table, $id, $this->affect, $pulse_timeout, $pulse_timeout));
		}
		
		public static function isAffecting($target, $affect)
		{
			
			foreach($target->getAffects() as $a)
				if($a->getAffect() == $affect)
					return true;
		}
	}
?>
