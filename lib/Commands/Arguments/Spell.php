<?php
namespace Phud\Commands\Arguments;
use Phud\Abilities\Spell as aSpell;

class Spell extends Ability
{
	public function parse($arg)
	{
		$ability = parent::parse($arg);
		if($ability instanceof aSpell) {
			return $ability;
		}
		$this->fail("That is not a spell.");
	}
}
