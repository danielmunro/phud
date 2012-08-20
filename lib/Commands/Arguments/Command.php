<?php
namespace Phud\Commands\Arguments;
use Phud\Actors\Actor as aActor,
	Phud\Commands\Command as cCommand;

class Command extends Argument
{
	protected function parseArg(aActor $actor, $arg)
	{
		$command = cCommand::lookup($arg);
		if($command) {
			return $command;
		}
		$this->fail($actor, "You cannot do that.");
	}
}
