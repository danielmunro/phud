<?php
namespace Phud\Commands;
use Phud\Actors\User as lUser;

class Gossip extends User
{
	protected $alias = 'gossip';
	protected $min_argument_count = 1;
	protected $min_argument_fail = "Gossip what?";

	public function perform(lUser $user, $message)
	{
		$broadcast = ucfirst($user)." gossips, \"".$message."\"";
		$user->getClient()->fire('broadcast', $broadcast);
		$user->notify("You gossip, \"".$message."\"");
	}
	
	protected function getArgumentsFromHints($actor, $args)
	{
		return [implode(' ', array_slice($args, 1))];
	}
}
