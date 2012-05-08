<?php
namespace Phud\Commands;
use Phud\Server,
	Phud\Actors\User as lUser;

class Slay extends DM
{
	protected $alias = 'slay';

	public function perform(lUser $user, $args = [])
	{
		$target = $user->getRoom()->getActorByInput($args[1]);
		Server::out($user, "You slay ".$target." in cold blood!");
		$user->getRoom()->announce([
			['actor' => $target, 'message' => ucfirst($user)." slays you in cold blood!"],
			['actor' => $user, 'message' => "You slay ".$target." in cold blood!"],
			['actor' => '*', 'message' => ucfirst($user)." slays ".$target." in cold blood!"]
		]);
		$user->setAttribute('hp', 0);
	}
}
?>
