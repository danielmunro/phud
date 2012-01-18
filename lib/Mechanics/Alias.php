<?php

	namespace Mechanics;
	trait Alias
	{
		protected static $aliases = [];
		
		public static function addAlias($alias, $lookup, $priority = 10)
		{
			self::$aliases[$alias] = ['alias' => $alias, 'lookup' => $lookup, 'priority' => $priority];
		}
		
		public static function lookup($alias)
		{
			// Direct match
			if(isset(self::$aliases[$alias])) {
				return self::$aliases[$alias];
			}
			
			$possibilities = array_filter(
				self::$aliases,
				function($lookup) use ($alias) {
					return strpos($lookup['alias'], $alias) === 0;
				}
			);
			
			// Return the highest priority match
			if($possibilities) {
				usort($possibilities, function($a, $b) {
					return $a['priority'] < $b['priority'];
				});
				var_dump($possibilities);
				return $possibilities[0];
			}
		}
		
		public static function getAliases()
		{
			return self::$aliases;
		}
	}

?>
