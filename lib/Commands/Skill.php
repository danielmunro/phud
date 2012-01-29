<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Server,
	\Mechanics\Ability\Skill as mSkill,
	\Mechanics\Ability\Ability,
	\Mechanics\Command\User,
	\Living\User as lUser;

class Skill extends User
{
	protected function __construct()
	{
		self::addAlias('skill', $this);
	}

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
