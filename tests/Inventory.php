<?php
namespace Tests;
class Inventory extends \PHPUnit_Framework_TestCase
{

	private $mob1 = null;
	private $mob2 = null;

	public function setUp()
	{
		\Mechanics\Debug::newLog();
		/**
		\Mechanics\Command::runInstantiation();
		\Living\Mob::instantiate();
		\Living\Shopkeeper::instantiate();
		$this->user = new \Living\User(null);
		$this->user->handleLogin('dan');
		$this->user->handleLogin('qwerty');
		*/
		$this->mob1 = new \Living\Mob(array(
			'race' => 'Human',
			'fk_room_id' => 1,
			'id' => 3294294 // rand -- we don't want an id match
		));
		$this->mob2 = new \Living\Mob(array(
			'race' => 'Human',
			'fk_room_id' => 1,
			'id' => 304203242 // rand -- we don't want an id match
		));
	}

	public function testAddItem()
	{
	}
}
?>
