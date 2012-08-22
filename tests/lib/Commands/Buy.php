<?php

namespace Phud\Tests\Commands;
use Phud\Commands\Arguments as Args;

class Buy extends \PHPUnit_Framework_TestCase
{
	protected $mob = null;
	protected $room_id_with_shop = 10;
	protected $room_id_no_shop = 1;

	public function setup()
	{
		$this->mob = new \Phud\Actors\Mob();
		$this->mob->modifyCurrency('copper', 100);
		$this->mob->setRoom(\Phud\Room\Room::getByID($this->room_id_with_shop));
	}

	public function testBuy()
	{
		(new \Phud\Commands\Buy())->tryPerform($this->mob, 'buy pie');
		$this->assertInstanceOf('\Phud\Items\Food', $this->mob->getItemByInput('pie'));
	}

	public function testNotEnoughMoney()
	{
		$this->mob->modifyCurrency('copper', -100);
		$buying = new \Phud\Commands\Buy();
		$buying->tryPerform($this->mob, 'buy pie');
		$this->assertEmpty($this->mob->getItems());
		$this->assertEquals($buying->getFailMessage(), $this->mob->getNotification());
	}

	public function testMobNotInShop()
	{
		$this->mob->setRoom(\Phud\Room\Room::getByID($this->room_id_no_shop));
		$buying = new \Phud\Commands\Buy();
		$buying->tryPerform($this->mob, 'buy pie');
		$this->assertEmpty($this->mob->getItems());
		$this->assertEquals($buying->getFailMessage(), $this->mob->getNotification());
	}

	public function testNoItem()
	{
		$buying = new \Phud\Commands\Buy();
		$buying->tryPerform($this->mob, 'buy doesnotexist');
		$this->assertEmpty($this->mob->getItems());
		$this->assertEquals($buying->getFailMessage(), $this->mob->getNotification());
	}

	public function testTargetingShopkeeper()
	{
		(new \Phud\Commands\Buy())->tryPerform($this->mob, 'buy pie anyan');
		$this->assertInstanceOf('\Phud\Items\Food', $this->mob->getItemByInput('pie'));
	}

	public function testTargetingNoShopkeeper()
	{
		$buying = new \Phud\Commands\Buy();
		$buying->tryPerform($this->mob, 'buy pie notshopkeeper');
		$this->assertEmpty($this->mob->getItems());
		$this->assertEquals($buying->getFailMessage(), $this->mob->getNotification());
	}
}
