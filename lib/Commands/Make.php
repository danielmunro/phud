<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Server,
	\Mechanics\Command\DM,
	\Mechanics\Command\Command,
	\Living\User as lUser;

class Make extends DM
{

	protected function __construct()
	{
		self::addAlias('make', $this);
	}

	public function perform(lUser $user, $args = array())
	{
		$target = $user->getRoom()->getActorByInput($args[1]);
		$command = Command::lookup($args[2]);
		if($target && $command)
		{
			$command['lookup']->perform($target, $args);
			Server::out($user, "Done.");
		}
		else
		{
			Server::out($user, "Cannot be done.");
		}
	}

}
?>
