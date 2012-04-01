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
	
	public function perform(lUser $user, $args = array())
	{
		if(array_key_exists('sleep', $user->getAffects()))
			return Server::out($user, "You need to be able to wake up first.");
		
		$user->save();
		Server::out($user, "Good bye!\r\n");
		Server::instance()->disconnectClient($user->getClient());
	}
}
?>
