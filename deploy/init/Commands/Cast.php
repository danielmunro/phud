<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Abilities\Ability,
	Phud\Abilities\Spell as aSpell;

class Cast extends Command
{
	protected $alias = ['cast', 11];
	protected $dispositions = [Actor::DISPOSITION_STANDING];
	protected $min_argument_count = 1;
	protected $min_argument_fail = "Cast what?";
	
	public function perform(Actor $actor, Ability $spell, Actor $target)
	{
		$actor->fire('casting', $spell);
		$actor->notify("You utter the words, \"".$spell."\"");
		$spell->perform($actor, $target);
	}

	protected function getArgumentsFromHints(Actor $caster, $args)
	{
		return [
			(new Arguments\Spell($caster))->parse($caster, $args[1]),
			sizeof($args) === 3 ? (new Arguments\Actor())->parse($caster, $args) : $caster;
		];
	}
}
