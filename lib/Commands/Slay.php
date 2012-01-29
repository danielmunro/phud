<?php
namespace Commands;
use \Mechanics\Server,
	\Mechanics\Alias,
	\Mechanics\Race,
	\Mechanics\Command\DM,
	\Living\Mob as lMob,
	\Living\User as lUser;

class Slay extends DM
{
	protected function __construct()
	{
		self::addAlias('slay', $this);
	}

	public function perform(lUser $user, $args = array())
	{
		$target = $user->getRoom()->getActorByInput($args[1]);
		Server::out($user, "You slay ".$target." in cold blood!");
		$user->getRoom()->announce($user, ucfirst($user)." slays ".$target." in cold blood!");
		$target->delete();
	}
}
?>
