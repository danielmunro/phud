<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Server,
	\Mechanics\Command\User,
	\Living\User as lUser;

class Inventory extends User
{
	protected function __construct()
	{
		self::addAlias('inventory', $this);
	}

	public function perform(lUser $user, $args = array())
	{
		Server::out($user, 'Your inventory:');
		Server::out($user, $user->displayContents());
	}
}
?>
