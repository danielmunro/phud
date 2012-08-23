<?php

namespace Phud\Tests\Commands\Arguments;
use Phud\Commands\Arguments as Args;

class Number extends \PHPUnit_Framework_TestCase
{
	/**
	 *	@expectedException InvalidArgumentException
	 */
	public function testString()
	{
		(new Args\Number())->parse('notANumber');
	}

	public function testNotRequiredSilentFail()
	{
		$value = 'notANumber';
		$this->assertNotEquals($value, (new Args\Number())->setNotRequired()->parse($value));
	}

	public function testNumeric()
	{
		$this->assertEquals(1, (new Args\Number())->parse(1));
		$this->assertEquals(123456789, (new Args\Number())->parse(123456789));
		$this->assertEquals(1.5, (new Args\Number())->parse(1.5));
	}
}
