<?php
namespace Phud\Commands;
use Phud\Server,
	Phud\Abilities\Ability,
	Phud\Actors\User as lUser;

class Spell extends User
{
	protected $alias = 'spell';

	public function perform(lUser $user, $args = array())
	{
		Server::out($user, "Spells: ");
		$abilities = $user->getAbilities();
		foreach($abilities as $a) {
			$ability = Ability::lookup($a);
			if($ability && $ability['lookup'] instanceof mSpell) {
				$pad = 20 - strlen($a);
				$label = $a;
				for($i = 0; $i < $pad; $i++) {
					$label .= ' ';
				}
				Server::out($user, $label.': '.$ability['lookup']->getProficiency());
			}
		}
	}
}
?>
