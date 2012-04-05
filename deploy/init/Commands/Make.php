<?php
namespace Phud\Commands;
use Phud\Server,
	Phud\Actors\User as lUser;

class Make extends DM
{
	protected $alias = 'make';

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
