<?php
namespace Phud\Commands;
use Phud\Abilities\Ability,
	Phud\Actors\User as lUser;

class Skill extends User
{
	protected $alias = 'skill';

	public function perform(lUser $user)
	{
		$user->notify("Skills: ");
		$aliases = $user->getAbilities();
		foreach($aliases as $s) {
			$ability = Ability::lookup($s);
			$pad = 20 - strlen($s);
			$label = $s;
			for($i = 0; $i < $pad; $i++)
				$label .= ' ';
			$user->notify($label.' '.$ability->getProficiency());
		}
	}
}
