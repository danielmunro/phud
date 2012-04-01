<?php
namespace Phud\Commands;
use Phud\Server,
	Phud\Actors\Actor,
	Phud\Actors\User as lUser;

class Affects extends User
{
	protected $alias = ['affects', 11];
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING,
		Actor::DISPOSITION_SLEEPING
	];

	public function perform(lUser $user, $args = array())
	{
		Server::out($user, 'You are affected by: ');
		$affects = $user->getAffects();
		foreach($affects as $affect) {
			if($affect->getMessageAffect()) {
				Server::out($user, $affect->getMessageAffect() . '. ' . $affect->getTimeout() . ' ticks.');
			}
		}
	}
}
?>
