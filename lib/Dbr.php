<?php
namespace Phud;

class Dbr extends \Redis
{
	private static $instance = null;
	
	public function __construct()
	{
		if(self::$instance) {
			throw new Exception('Redis db connection already exists');
		}
		if(!$this->connect('127.0.0.1')) {
			throw new Exception('Could not connect to redis');
		}
		$this->select(1);
		parent::__construct();
	}
	
	public static function instance()
	{
		return self::$instance ? self::$instance : self::$instance = new self();
	}
}
?>
