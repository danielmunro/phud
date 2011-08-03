<?php

	namespace Mechanics;
	class Alias
	{
		protected $alias_name = '';
		protected $lookup_object = null;
		protected $priority = 0;
		protected static $instances = array();
		
		const PRIORITY_NORMAL = 5;
		
		public function __construct($alias_name, $lookup_object, $priority = self::PRIORITY_NORMAL)
		{
			$this->alias_name = $alias_name;
			$this->lookup_object = $lookup_object;
			$this->priority = $priority;
			if(isset(self::$instances[$this->alias_name]))
				Debug::addDebugLine('Alias "'.$this->alias_name.'" already set. '.print_r(self::$instances[$this->alias_name], true), \Exceptions\Alias::ALIAS_ALREADY_EXISTS);
			
			self::$instances[$this->alias_name] = $this;
		}
		
		public static function lookup($alias_name)
		{
			// Direct match
			if(isset(self::$instances[$alias_name]))
				return self::$instances[$alias_name]->getLookupObject();
			
			$possibilities = array();
			foreach(self::$instances as $key => $instance)
				if(strpos($key, $alias_name) === false)
					$possibilities[] = $instance;
			
			usort($possibilities, function($a, $b)
				{
					return $a->getPriority() < $b->getPriority();
				});
			// Return the highest priority match
			if($possibilities)
				return $possibilities[0]->getLookupObject();
		}
		
		public function getAliasName()
		{
			return $this->alias_name;
		}
		
		public function getLookupObject()
		{
			return $this->lookup_object;
		}
		
		public function getPriority()
		{
			return $this->priority;
		}
		
		public function __toString()
		{
			return $this->alias_name;
		}
	}

?>
