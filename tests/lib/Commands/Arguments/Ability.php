<?php

namespace Phud\Tests\Commands\Arguments;
use Phud\Commands\Arguments as Args;

class Ability extends \PHPUnit_Framework_TestCase
{
	protected $mob = null;
	
	public function setup()
	{
		$this->mob = new \Phud\Actors\Mob();
		$this->mob->addAbility(\Phud\Abilities\Ability::lookup('kick'));
	}

	public function testAbilityExists()
	{
		$ability = (new Args\Ability())->parse('kick');
		$this->assertInstanceOf('\Phud\Abilities\Ability', $ability);
	}

	/**
	 *	@expectedException InvalidArgumentException
	 */
	 public function testAbilityNotFound()
	 {
		$ability = (new Args\Ability())->parse('not ability');
	 }

	 public function testActorHasAbility()
	 {
		$ability = (new Args\Ability($this->mob))->parse('kick');
		$this->assertInstanceOf('\Phud\Abilities\Ability', $ability);
	 }
}
