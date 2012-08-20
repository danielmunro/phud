<?php
namespace Phud\Commands;
use Phud\Actors\User as aUser,
	Phud\Actors\Actor;

class Make extends DM
{
	protected $alias = 'make';
	protected $min_argument_count = 2;
	protected $min_argument_fail = "Command whom to do what?";

	public function perform(aUser $user, Actor $actor, Command $command, $args)
	{
		$command->tryPerform($actor, $args);
		$user->notify("Done.");
	}

	protected function getArgumentsFromHints($actor, $args)
	{
		return [
			(new Arguments\Actor())->parse($actor, $args[1]),
			(new Arguments\Command())->parse($actor, $args[2]),
			array_slice($args, 2)
		];
	}
}
