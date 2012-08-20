<?php
namespace Phud\Commands;
use Phud\Actors\User as aUser,
	Phud\Actors\Actor;

class Slay extends DM
{
	protected $alias = 'slay';
	protected $min_argument_count = 1;
	protected $min_argument_fail = "Slay what?";

	public function perform(aUser $user, Actor $target)
	{
		$target->setAttribute('hp', 0);
		$user->getRoom()->announce([
			['actor' => $target, 'message' => ucfirst($user)." slays you in cold blood!"],
			['actor' => $user, 'message' => "You slay ".$target." in cold blood!"],
			['actor' => '*', 'message' => ucfirst($user)." slays ".$target." in cold blood!"]
		]);
	}

	protected function getArgumentsFromHints($actor, $args)
	{
		return [(new Arguments\Actor())->parse($actor, $args[1])];
	}
}
