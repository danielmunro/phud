<?php
namespace Phud\Commands\Arguments;
use Phud\Commands\Command as cCommand;

class Command extends Argument
{
	public function parse($arg)
	{
		$command = cCommand::lookup($arg);
		if($command) {
			return $command;
		}
		$this->fail("You cannot do that.");
	}
}
