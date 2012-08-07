<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Server,
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
		$client = $user->getClient();
		if(array_key_exists('sleep', $user->getAffects())) {
			return $client->write("You need to be able to wake up first.\r\n");
		}
		
		$client->write("Good bye!\r\n");
		$user->save();
		$client->disconnect();
	}
}
