<?php

namespace Phud\Tests\Commands\Arguments;
use Phud\Commands\Arguments as Args;

class Container extends \PHPUnit_Framework_TestCase
{
	protected $mob = null;
	
	public function setup()
	{
		$this->mob = new \Phud\Actors\Mob();
	}

	public function testActorHasContainer()
	{
		$this->mob->addItem(new \Phud\Items\Container(['alias' => 'satchel']));
		$container = (new Args\Container($this->mob))->parse('satchel');
		$this->assertInstanceOf('\Phud\Items\Container', $container);
	}

	public function testRoomHasContainer()
	{
		$room = \Phud\Room\Room::getByID(1);
		$room->addItem(new \Phud\Items\Container(['alias' => 'satchel']));
		$container = (new Args\Container($room))->parse('satchel');
		$this->assertInstanceOf('\Phud\Items\Container', $container);
	}

	/**
	 *	@expectedException InvalidArgumentException
	 */
	 public function testNoContainer()
	 {
		$this->mob->addItem(new \Phud\Items\Item(['alias' => 'pie']));
		$container = (new Args\Container($this->mob))->parse('pie');
	 }
}
