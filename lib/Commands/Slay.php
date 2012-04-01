<?php
namespace Phud\Commands;
use Phud\Server,
	Phud\Actors\User as lUser;

class Slay extends DM
{
	protected $alias = 'slay';

	public function perform(lUser $user, $args = array())
	{
		$target = $user->getRoom()->getActorByInput($args[1]);
		Server::out($user, "You slay ".$target." in cold blood!");
		$user->getRoom()->announce($user, ucfirst($user)." slays ".$target." in cold blood!");
		$target->delete();
	}
}
?>
