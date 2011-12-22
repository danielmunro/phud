<?php

	namespace Mechanics;
	trait Alias
	{
		protected static $aliases = [];
		
		public static function addAlias($alias, $lookup, $priority = 10)
		{
			self::$aliases[$alias] = [$lookup, $priority];
		}
		
		public static function lookup($alias)
		{
			// Direct match
			if(isset(self::$aliases[$alias])) {
				return self::$aliases[$alias][0];
			}
			
			$possibilities = [];
			foreach(self::$aliases as $key => $instance) {
				if(strpos($key, $alias) === 0) {
					$possibilities[] = $instance;
					if(empty($key) || empty($instance)) {
						var_dump(self::$instances);die;
					}
				}
			}
			
			// Return the highest priority match
			if($possibilities) {
				usort($possibilities, function($a, $b) {
					return $a[1] < $b[1];
				});
				return $possibilities[0][0];
			}
		}
		
		public static function getAliases()
		{
			return self::$aliases;
		}
	}

?>
