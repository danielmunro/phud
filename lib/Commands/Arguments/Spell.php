<?php
namespace Phud\Commands\Arguments;
use Phud\Actors\Actor as aActor,
	Phud\Abilities\Spell as aSpell;

class Spell extends Ability
{
	protected function parseArg(aActor $actor, $arg)
	{
		$ability = parent::parseArg($actor, $arg);
		if($ability instanceof aSpell) {
			return $ability;
		} else {
			$this->fail($actor, "That is not a spell.");
		}
	}
}
