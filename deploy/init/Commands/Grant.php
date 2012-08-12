<?php
namespace Phud\Commands;
use Phud\Abilities\Ability,
	Phud\Actors\User as aUser;

class Grant extends DM
{
	protected $alias = 'grant';

	public function perform(aUser $user, $args = array())
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
				$target->notify(ucfirst($user)." has bestowed the knowledge of ".$ability." on you.");
			}
			return $user->notify("You've granted ".$ability." to ".$target.".");
		}
		$user->notify("Ability not found.");
	}
}
