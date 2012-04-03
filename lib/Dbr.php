<?php
namespace Phud;

class Dbr extends \Redis
{
	private static $instance = null;
	
	public function __construct()
	{
	}
	
	public static function instance()
	{
		if(!isset(self::$instance))
		{
			self::$instance = new self();
			if(!self::$instance->connect('127.0.0.1'))
				throw new DB_Exception('Could not connect to redis', DB_Exception::CONNECTION_UNAVAILABLE);
			self::$instance->select(1);
		}
		return self::$instance;
	}
}
?>