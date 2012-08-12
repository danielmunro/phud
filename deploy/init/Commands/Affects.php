<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Actors\User as lUser;

class Affects extends User
{
	protected $alias = ['affects', 11];
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING,
		Actor::DISPOSITION_SLEEPING
	];

	public function perform(lUser $user, $args = [])
	{
		$message = "You are affected by:\r\n";
		$affects = $user->getAffects();
		foreach($affects as $affect) {
			if($affect->getMessageAffect()) {
				$message .= $affect->getMessageAffect().". ".$affect->getTimeout()." ticks.\r\n";
			}
		}
		$user->getClient()->write($message);
	}
}
