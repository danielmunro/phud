<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Actors\User as aUser;

class Affects extends User
{
	protected $alias = ['affects', 11];
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING,
		Actor::DISPOSITION_SLEEPING
	];

	public function perform(aUser $user)
	{
		$message = "You are affected by:\r\n";
		foreach($user->getAffects() as $affect) {
			$message .= ($affect->getMessageAffect() ? $affect->getMessageAffect() : "Unknown").". ".$affect->getTimeout()." ticks.\r\n";
		}
		$user->notify($message);
	}
}
