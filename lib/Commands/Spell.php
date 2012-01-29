<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Server,
	\Mechanics\Ability\Ability,
	\Mechanics\Ability\Spell as mSpell,
	\Mechanics\Command\User,
	\Living\User as lUser;

class Spell extends User
{
	protected function __construct()
	{
		self::addAlias('spell', $this);
	}

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
