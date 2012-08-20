<?php
namespace Phud\Commands\Arguments;
use Phud\Actors\Actor as aActor,
	Phud\Abilities\Ability as aAbility;

class Ability extends Argument
{
	protected $search_in = null;

	public function __construct($search_in = null)
	{
		$this->search_in = $search_in;
	}

	protected function parseArg(aActor $actor, $arg)
	{
		$ability = aAbility::lookup($arg);

		if($this->search_in) {
			return in_array($ability->getAlias(), $this->search_in->getAbilities()) ? $ability : $this->fail($actor, "You don't know that ability.");
		}

		if($ability) {
			return $ability;
		}

		$this->fail($actor, "That ability does not exist.");
	}
}
