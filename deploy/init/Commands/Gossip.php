<?php
namespace Phud\Commands;
use Phud\Actors\User as lUser;

class Gossip extends User
{
	protected $alias = 'gossip';

	public function perform(lUser $user, $args = [])
	{
		$message = implode(' ', array_slice($args, 1));
		$broadcast = ucfirst($user)." gossips, \"".$message."\"";
		$user->getClient()->fire('broadcast', $broadcast);
		$user->notify("You gossip, \"".$message."\"");
	}
}
