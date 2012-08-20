<?php
namespace Phud\Commands;
use Phud\Abilities\Ability,
	Phud\Actors\User as aUser;

class Grant extends DM
{
	protected $alias = 'grant';
	protected $min_argument_count = 2;
	protected $min_argument_fail = "Grant what? To whom?";

	public function perform(aUser $user, Ability $ability, $target)
	{
		$target->addAbility($ability);
		if($target !== $user) {
			$target->notify(ucfirst($user)." has bestowed the knowledge of ".$ability." on you.");
		}
		$user->notify("You've granted ".$ability." to ".$target.".");
	}

	protected function getArgumentsFromHints($actor, $args)
	{
		return [
			(new Arguments\Ability())->parse($actor, $args[1]),
			(new Arguments\Actor())->parse($actor, $args[2])
		];
	}
}
