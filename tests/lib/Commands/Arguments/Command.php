<?php

namespace Phud\Tests\Commands\Arguments;
use Phud\Commands\Arguments as Args;

class Command extends \PHPUnit_Framework_TestCase
{
	public function testCommands()
	{
		$commands = \Phud\Commands\Command::getAliases();
		foreach($commands as $command => $info) {
			$instance = (new Args\Command())->parse($command);
			$this->assertInstanceOf('\Phud\Commands\Command', $instance);
		}
	}

	/**
	 *	@expectedException InvalidArgumentException
	 */
	 public function testInvalidCommand()
	 {
		(new Args\Command())->parse('not a command');
	 }
}
