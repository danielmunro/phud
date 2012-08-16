<?php
namespace Phud\Commands\Arguments;
use Phud\Actors\Actor as aActor,
	Phud\Abilities\Ability as aAbility;

class Ability extends Argument
{
	protected function parseArg(aActor $actor, $arg)
	{
		$ability = aAbility::lookup($arg);

		if($ability) {
			return $ability;
		}
		$this->fail($actor, "That ability does not exist.");
	}
}
