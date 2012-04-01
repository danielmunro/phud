<?php
namespace Phud\Commands;
use Phud\Server,
	Phud\Ability\Ability,
	Phud\Actors\User as lUser;

class Skill extends User
{
	protected $alias = 'skill';

	public function perform(lUser $user, $args = array())
	{
		Server::out($user, "Skills: ");
		$aliases = $user->getAbilities();
		foreach($aliases as $s)
		{
			$ability = Ability::lookup($s);
			$pad = 20 - strlen($s);
			$label = $s;
			for($i = 0; $i < $pad; $i++)
				$label .= ' ';
			Server::out($user, $label.' '.$ability['lookup']->getProficiency());
		}
	}
}
?>
