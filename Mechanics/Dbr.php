<?php

	namespace Mechanics;
	class Dbr extends \Redis
	{
		private static $instance = null;
		
		public function __construct()
		{
			//throw new DB_Exception('Use instance() to get an instance of DB, not new DB()', DB_Exception::USE_SINGLETON);
		}
		
		public static function instance()
		{
			if(!isset(self::$instance))
			{
				self::$instance = new self();
				if(!self::$instance->connect('127.0.0.1'))
					throw new DB_Exception('Could not connect to redis', DB_Exception::CONNECTION_UNAVAILABLE);
			}
			return self::$instance;
		}
	}

?>
