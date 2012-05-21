<?php
namespace Phud;

class Dbr
{
	private static $instance = null;
	private $connection = null;
	
	public function __construct()
	{
		if(self::$instance) {
			throw new Exception('Redis db connection already exists');
		}

		require_once('Predis/Autoloader.php');

		// Set up redis
		\Predis\Autoloader::register();
		$this->connection = new \Predis\Client(array(
			'host' => 'localhost',
			'port' => 6379,
			'connection_persistent' => true
		));
		$this->connection->select(1);
	}

	public function getConnection()
	{
		return $this->connection;
	}
	
	public static function instance()
	{
		$i = self::$instance ? self::$instance : self::$instance = new self();
		return $i->getConnection();
	}
}
?>
