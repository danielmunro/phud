<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Server,
	\Mechanics\Command\User,
	\Living\User as lUser;

class Inventory extends User
{
	protected $alias = 'inventory';

	public function perform(lUser $user, $args = array())
	{
		Server::out($user, 'Your inventory:');
		Server::out($user, $user->displayContents());
	}
}
?>
