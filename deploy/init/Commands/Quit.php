<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Actors\User as lUser;

class Quit extends User
{
	protected $alias = 'quit';
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING,
		Actor::DISPOSITION_SLEEPING
	];
	
	public function perform(lUser $user, $args = [])
	{
		if(array_key_exists('sleep', $user->getAffects())) {
			return $user->notify("You need to be able to wake up first.");
		}
		
		$user->notify("Good bye!");
		$user->save();
		$user->getClient()->disconnect();
	}
}
