<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Server,
	\Mechanics\Command\User as cUser,
	\Living\User as lUser;

class Who extends cUser
{
	protected function __construct()
	{
		self::addAlias('who', $this);
	}

	public function perform(lUser $user, $args = array())
	{
		Server::out($user, 'Who list:');
		$users = lUser::getInstances();
		foreach($users as $u) {
			Server::out($user, '[' . $u->getLevel() . ' ' . $u->getRace()['alias'] . '] ' . $u);
		}
		$size = sizeof($users);
		Server::out($user, $size . ' player' . ($size != 1 ? 's' : '') . ' found.');
	}
}
?>
