<?php
namespace Phud\Commands;
use Phud\Actors\User as lUser;

class Make extends DM
{
	protected $alias = 'make';

	public function perform(lUser $user, $args = [])
	{
		$target = $user->getRoom()->getActorByInput($args[1]);
		$command = Command::lookup($args[2]);
		if($target && $command) {
			$command->perform($target, $args);
			$user->notify("Done.");
		} else {
			$user->notify("Cannot be done.");
		}
	}
}
