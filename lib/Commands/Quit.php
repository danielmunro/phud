<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Actor,
	\Mechanics\Server,
	\Mechanics\Command\User,
	\Living\User as lUser;
class Quit extends User
{
	protected $alias = 'quit';
	
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
