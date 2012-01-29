<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Ability,
	\Mechanics\Server,
	\Mechanics\Command\User,
	\Living\User as lUser;

class Dungeon_Master extends User
{
	protected $alias = 'dungeon master';

	public function perform(lUser $user, $args = array())
	{
		$user->setDM(true);
		Server::out($user, "You are now the dungeon master.");
	}
}
?>
