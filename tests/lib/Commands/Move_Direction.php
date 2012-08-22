<?php

namespace Phud\Tests\Commands;
use Phud\Commands\Arguments as Args;

class Move_Direction extends \PHPUnit_Framework_TestCase
{
	protected $mob = null;
	protected $room_id = 1;
	protected $command = null;

	public function setup()
	{
		$this->mob = new \Phud\Actors\Mob();
		$this->mob->setRoom(\Phud\Room\Room::getByID($this->room_id));
		$this->command = new \Phud\Commands\Move_Direction();
	}

	public function testMoving()
	{
		$current_room = $this->mob->getRoom();
		foreach($this->mob->getRoom()->getDirections() as $direction => $room) {
			if($room) {
				$moving_to = $room;
				$this->command->perform($this->mob, $direction);
				break;
			}
		}
		$this->assertNotEquals($current_room->getID(), $this->mob->getRoom()->getID());
		$this->assertEquals($moving_to, $this->mob->getRoom());
	}

	public function testMoveFail()
	{
		$current_room = $this->mob->getRoom();
		foreach($this->mob->getRoom()->getDirections() as $direction => $room) {
			if(!$room) {
				$this->command->perform($this->mob, $direction);
				break;
			}
		}
		$this->assertEquals($current_room, $this->mob->getRoom());
	}

	public function testMobNoMovement()
	{
		$this->mob->setAttribute('movement', 0);
		$current_room = $this->mob->getRoom();
		foreach($this->mob->getRoom()->getDirections() as $direction => $room) {
			if($room) {
				$moving_to = $room;
				$this->command->perform($this->mob, $direction);
				break;
			}
		}
		$this->assertEquals($current_room, $this->mob->getRoom());
		$this->assertNotEquals($moving_to->getID(), $this->mob->getRoom()->getID());
	}
}
