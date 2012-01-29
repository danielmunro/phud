<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Ability\Ability,
	\Mechanics\Server,
	\Mechanics\Command\DM,
	\Living\User;

class Grant extends DM
{
	protected $alias = 'grant';

	public function perform(User $user, $args = array())
	{
		$target = $user;//$actor->getRoom()->getActorByInput($args);
		if($args[1] === 'admin') {
			$user->setDM(true);
			return;
		}
		$ability = Ability::lookup($args[1]);
		if($ability) {
			$target->addAbility($ability);
			if($target !== $user) {
				Server::out($target, ucfirst($user)." has bestowed the knowledge of ".$ability['alias']." on you.");
			}
			return Server::out($user, "You've granted ".$ability['alias']." to ".$target.".");
		}
		Server::out($user, "Ability not found.");
	}

}
?>
