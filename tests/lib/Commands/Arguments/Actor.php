<?php

namespace Phud\Tests\Commands\Arguments;
use Phud\Commands\Arguments as Args;

class Actor extends \PHPUnit_Framework_TestCase
{
	protected $room = null;
	
	public function setup()
	{
		$this->room = \Phud\Room\Room::getByID(1);
	}

	public function testActorExists()
	{
		$actor = (new Args\Actor($this->room))->parse('hassan');
		$this->assertInstanceOf('\Phud\Actors\Actor', $actor);
	}

	/**
	 *	@expectedException InvalidArgumentException
	 */
	 public function testNoActor()
	 {
		(new Args\Actor($this->room))->parse('not mob');
	 }
}
