<?php

	namespace Mechanics;
	class Affect
	{
	
		const GLOW = 'glow';
		const STUN = 'stun';
		
		private $affect = '';
		private $message = '';
		private $target = null;
		private $pulse_start = 0;
		private $args = array();
		private static $instances = array();
		
		public function __construct($affect, &$target, $message = '', $args = array())
		{
		
			$this->affect = $affect;
			$this->target = $target;
			$this->message = $message;
			$this->target->addAffect($this);
			$this->args = $args;
			
			$class = get_class($target);
			self::$instances[$class][$target->getId()][] = $this;
			
			if($this->args)
				$this->initialize();
		}
		public function initialize()
		{
			
			if($this->pulse_start)
				$this->args['timeout'] = $this->args['timeout'] - (Server::getLastPulse() - $this->pulse_start);
			else
				$this->pulse_start = Server::getLastPulse();
			
			if($this->args['timeout'] <= 0)
				return false;
			
			$affect = $this->affect;
			$affect::apply($this->target, $this->args);
			
			return true;
		}
		public function getAffect() { return $this->affect; }
		public function getMessage() { return $this->message; }
		public function getTickTimeout()
		{
			return floor((Server::getLastPulse() - $this->pulse_start) / Server::PULSES_PER_TICK);
		}
		public static function reapply($target)
		{
			$class = get_class($target);
			foreach(self::$instances[$class][$target->getId()] as $instance)
				if(!$instance->initialize())
					unset($instance);
		}
		public static function isAffecting($target, $affect)
		{
			foreach($target->getAffects() as $a)
				if($a->getAffect() == $affect)
					return true;
		}
		public static function removeAffect($target, $affect)
		{
			foreach($target->getAffects() as $a)
				if($a->getAffect() == $affect)
					$target->removeAffect($a);
		}
		public static function getAffects($target)
		{
			$class = get_class($target);
			print_r(self::$instances[$class][$target->getId()]);
			if(isset(self::$instances[$class][$target->getId()]))
				return self::$instances[$class][$target->getId()];
			else
				return array();
		}
	}
?>
