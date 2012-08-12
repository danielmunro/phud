<?php
namespace Phud\Commands;
use Phud\Abilities\Ability,
	Phud\Abilities\Spell as aSpell,
	Phud\Actors\User as lUser;

class Spell extends User
{
	protected $alias = 'spell';

	public function perform(lUser $user, $args = [])
	{
		$user->notify("Spells: ");
		$abilities = $user->getAbilities();
		foreach($abilities as $a) {
			$ability = Ability::lookup($a);
			if($ability instanceof aSpell) {
				$pad = 20 - strlen($a);
				$label = $a;
				for($i = 0; $i < $pad; $i++) {
					$label .= ' ';
				}
				$user->notify($label.': '.$ability->getProficiency());
			}
		}
	}
}
